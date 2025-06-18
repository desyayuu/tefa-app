<?php
namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class DataDokumenPenunjangMahasiswaController extends Controller
{
    // Cek Role Mahasiswa dalam Proyek
    private function checkMahasiswaRole($proyekId, $mahasiswaId)
    {
        $isMember = DB::table('t_project_member_mahasiswa')
            ->where('proyek_id', $proyekId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->exists();

        return ['isMember' => $isMember];
    }

    // Akses dokumen yang beda untuk Leader dan Member
    private function getAllowedDocumentTypes($isMember)
    {
        if ($isMember) {
            $allowedDocumentNames = [
                'Dokumen Teknis',
                'Dokumen Pengujian', 
                'Dokumen Lainnya',
                'Manual Book'
            ];

            return DB::table('m_jenis_dokumen_penunjang')
                ->whereNull('deleted_at')
                ->whereIn('nama_jenis_dokumen_penunjang', $allowedDocumentNames)
                ->pluck('jenis_dokumen_penunjang_id')
                ->toArray();
        }
    }


    public function addDokumenPenunjang(Request $request)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkmahasiswaRole($request->proyek_id, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambah dokumen di proyek ini'
                ], 403);
            }

            // Get allowed document types
            $allowedDocumentTypeIds = $this->getAllowedDocumentTypes($roleCheck['isMember']);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'proyek_id' => 'required|exists:m_proyek,proyek_id',
                'nama_dokumen_penunjang' => 'required|string|max:255',
                'jenis_dokumen_penunjang_id' => [
                    'required',
                    'exists:m_jenis_dokumen_penunjang,jenis_dokumen_penunjang_id',
                    function ($attribute, $value, $fail) use ($allowedDocumentTypeIds) {
                        if (!in_array($value, $allowedDocumentTypeIds)) {
                            $fail('Anda tidak memiliki akses untuk menambah jenis dokumen ini.');
                        }
                    },
                ],
                'file_dokumen_penunjang' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate UUID for primary key
            $dokumenPenunjangId = Str::uuid()->toString();

            // Handle file upload
            if ($request->hasFile('file_dokumen_penunjang')) {
                $file = $request->file('file_dokumen_penunjang');
                
                if ($file->isValid()) {
                    // Create directory if not exists
                    $uploadPath = public_path('uploads/dokumen_penunjang/' . $request->proyek_id);
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Move file to destination
                    if ($file->move($uploadPath, $filename)) {
                        // Insert record into database
                        DB::table('m_dokumen_penunjang_proyek')->insert([
                            'dokumen_penunjang_proyek_id' => $dokumenPenunjangId,
                            'proyek_id' => $request->proyek_id,
                            'jenis_dokumen_penunjang_id' => $request->jenis_dokumen_penunjang_id,
                            'nama_dokumen_penunjang' => $request->nama_dokumen_penunjang,
                            'file_dokumen_penunjang' => 'uploads/dokumen_penunjang/' . $request->proyek_id . '/' . $filename,
                            'created_at' => now(),
                            'created_by' => $mahasiswaId, // Menggunakan mahasiswa_id
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Dokumen penunjang berhasil ditambahkan',
                            'data' => [
                                'dokumen_penunjang_proyek_id' => $dokumenPenunjangId
                            ]
                        ], 201);
                    } else {
                        throw new \Exception("Failed to move uploaded file");
                    }
                } else {
                    throw new \Exception("Uploaded file is not valid");
                }
            } else {
                throw new \Exception("No file uploaded");
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDokumenPenunjang($proyekId, Request $request)
    {
        $mahasiswaId = session('mahasiswa_id');
        
        if (!$mahasiswaId) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 401);
        }

        // Check role mahasiswa dalam proyek
        $roleCheck = $this->checkmahasiswaRole($proyekId, $mahasiswaId);
        
        if (!$roleCheck['isMember']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke proyek ini'
            ], 403);
        }

        // Get allowed document types based on role
        $allowedDocumentTypeIds = $this->getAllowedDocumentTypes($roleCheck['isMember']);

        // Get search parameter
        $search = $request->input('search');
        $perPage = $request->input('per_page', 3);
        $page = $request->input('page', 1);
        
        // Build query with joins and role-based filtering
        $query = DB::table('m_dokumen_penunjang_proyek')
            ->join('m_jenis_dokumen_penunjang', 'm_dokumen_penunjang_proyek.jenis_dokumen_penunjang_id', '=', 'm_jenis_dokumen_penunjang.jenis_dokumen_penunjang_id')
            ->where('proyek_id', $proyekId)
            ->whereNull('m_dokumen_penunjang_proyek.deleted_at')
            ->whereIn('m_dokumen_penunjang_proyek.jenis_dokumen_penunjang_id', $allowedDocumentTypeIds) // Filter berdasarkan role
            ->select(
                'm_dokumen_penunjang_proyek.*',
                'm_jenis_dokumen_penunjang.nama_jenis_dokumen_penunjang as jenis_dokumen'
            );
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_dokumen_penunjang', 'like', "%{$search}%")
                ->orWhere('m_jenis_dokumen_penunjang.nama_jenis_dokumen_penunjang', 'like', "%{$search}%");
            });
        }
        
        // Get ordered results
        $dokumen = $query->orderBy('m_dokumen_penunjang_proyek.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        $paginationHtml = ''; 
        if($dokumen->hasPages()) {
            $paginationHtml = view('vendor.pagination.custom', [
                'paginator' => $dokumen,
                'elements' => $dokumen->links()->elements,
            ])->render();
        }
        
        return response()->json([
            'success' => true,
            'data' => $dokumen,
            'pagination' => [
                'current_page' => $dokumen->currentPage(),
                'last_page' => $dokumen->lastPage(),
                'per_page' => $dokumen->perPage(),
                'total' => $dokumen->total(),
                'html' => $paginationHtml
            ],
            'user_role' => [
                'is_member' => $roleCheck['isMember']
            ]
        ]);
    }

    public function deleteDokumenPenunjang($id)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Get document info before deleting
            $dokumen = DB::table('m_dokumen_penunjang_proyek')
                ->where('dokumen_penunjang_proyek_id', $id)
                ->first();

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkmahasiswaRole($dokumen->proyek_id, $mahasiswaId);
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus dokumen ini'
                ], 403);
            }

            // Delete file if exists
            $filePath = public_path($dokumen->file_dokumen_penunjang);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Soft delete the document
            DB::table('m_dokumen_penunjang_proyek')
                ->where('dokumen_penunjang_proyek_id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $mahasiswaId,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen penunjang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDokumenPenunjang($id)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            $dokumen = DB::table('m_dokumen_penunjang_proyek')
                ->where('dokumen_penunjang_proyek_id', $id)
                ->first();

        
            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkmahasiswaRole($dokumen->proyek_id, $mahasiswaId);
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mendownload dokumen ini'
                ], 403);
            }

            // Check if document type is allowed for current user role
            $allowedDocumentTypeIds = $this->getAllowedDocumentTypes($roleCheck['isMember']);
            if (!in_array($dokumen->jenis_dokumen_penunjang_id, $allowedDocumentTypeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk jenis dokumen ini'
                ], 403);
            }

            // Get the file path
            $filePath = public_path($dokumen->file_dokumen_penunjang);
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            $contentTypes = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
            
            $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
            
            // Generate a clean filename
            $fileName = $dokumen->nama_dokumen_penunjang . '.' . $extension;
            return Response::download($filePath, $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

}