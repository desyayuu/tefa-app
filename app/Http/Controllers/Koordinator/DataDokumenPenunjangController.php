<?php
namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class DataDokumenPenunjangController extends Controller{

    public function index($id, Request $request)
    {
        $proyek = DB::table('m_proyek')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->where('m_proyek.proyek_id', $id)
            ->select(
                'm_proyek.*',
                'd_mitra_proyek.nama_mitra',
                'm_jenis_proyek.nama_jenis_proyek'
            )
            ->first();

        if (!$proyek) {
            return redirect()->route('koordinator.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }

        $search = $request->input('searchDokumenPenunjang');
        $query = DB::table('m_dokumen_penunjang_proyek as dp')
            ->join('m_jenis_dokumen_penunjang as jdp', 'dp.jenis_dokumen_penunjang_id', '=', 'jdp.jenis_dokumen_penunjang_id')
            ->where('dp.proyek_id', $id)
            ->whereNull('dp.deleted_at');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('dp.nama_dokumen_penunjang', 'like', "%{$search}%")
                  ->orWhere('jdp.nama_jenis_dokumen_penunjang', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 3);
        $dokumenPenunjang = $query->orderBy('dp.created_at', 'desc')
            ->select(
                'dp.dokumen_penunjang_proyek_id',
                'dp.nama_dokumen_penunjang',
                'jdp.nama_jenis_dokumen_penunjang as jenis_dokumen',
                'dp.file_dokumen_penunjang as file_path',
                'dp.created_at'
            )
            ->paginate($perPage);

        return view('pages.Koordinator.DataProyek.data_dokumen_penunjang', compact('proyek', 'dokumenPenunjang', 'search'));
    }

    public function addDokumenPenunjang(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'proyek_id' => 'required|exists:m_proyek,proyek_id',
                'nama_dokumen_penunjang' => 'required|string|max:255',
                'jenis_dokumen_penunjang_id' => 'required|exists:m_jenis_dokumen_penunjang,jenis_dokumen_penunjang_id',
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
                            'created_by' => auth()->user()->id ?? session('user_id'),
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
        // Get search parameter
        $search = $request->input('search');
        $perPage = $request->input('per_page', 3);
        $page = $request->input('page', 1);
        
        // Build query with joins
        $query = DB::table('m_dokumen_penunjang_proyek')
            ->join('m_jenis_dokumen_penunjang', 'm_dokumen_penunjang_proyek.jenis_dokumen_penunjang_id', '=', 'm_jenis_dokumen_penunjang.jenis_dokumen_penunjang_id')
            ->where('proyek_id', $proyekId)
            ->whereNull('m_dokumen_penunjang_proyek.deleted_at')
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
            // Tambahkan atribut data-page ke link pagination
            $paginationHtml = view('vendor.pagination.custom', [
                'paginator' => $dokumen,
                'elements' => $dokumen->links()->elements,
            ])->render();
        }
        
        // PERBAIKAN: Kembalikan data paginasi dalam format yang konsisten
        return response()->json([
            'success' => true,
            'data' => $dokumen, // Mengembalikan seluruh paginator object
            'pagination' => [
                'current_page' => $dokumen->currentPage(),
                'last_page' => $dokumen->lastPage(),
                'per_page' => $dokumen->perPage(),
                'total' => $dokumen->total(),
                'html' => $paginationHtml
            ]
        ]);
    }

    public function deleteDokumenPenunjang($id)
    {
        try {
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
                    'deleted_by' => auth()->user()->id ?? session('user_id'),
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
            // Get document info
            $dokumen = DB::table('m_dokumen_penunjang_proyek')
                ->where('dokumen_penunjang_proyek_id', $id)
                ->first();

            if (!$dokumen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            // Get the file path
            $filePath = public_path($dokumen->file_dokumen_penunjang);
            
            // Check if file exists
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
            
            // Return file as download response
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