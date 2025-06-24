<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DataLuaranProfesionalController extends Controller
{

    public function getDataLuaranDokumentasi($id, Request $request)
    {
        // First, check if project exists
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
            return redirect()->route('profesional.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }
        
        // QUERY 1: Mendapatkan data luaran proyek
        $luaranProyek = DB::table('d_luaran_proyek')
            ->where('proyek_id', $id)
            ->whereNull('deleted_at')
            ->first();
        
        // QUERY 2: Mendapatkan data dokumentasi proyek jika luaran proyek ada
        $dokumentasiProyek = collect([]);
        if ($luaranProyek) {
            $dokumentasiProyek = DB::table('d_dokumentasi_proyek')
                ->where('luaran_proyek_id', $luaranProyek->luaran_proyek_id)
                ->whereNull('deleted_at')
                ->get();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'luaran' => $luaranProyek,
                    'dokumentasi' => $dokumentasiProyek
                ]
            ]);
        }
    }

    public function saveLuaranProyek(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'proyek_id' => 'required|exists:m_proyek,proyek_id',
            'link_proyek' => 'nullable|string|max:255',
            'deskripsi_luaran' => 'nullable|string',
            'poster_proyek' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Check if luaran already exists for this project
            $existingLuaran = DB::table('d_luaran_proyek')
                ->where('proyek_id', $request->input('proyek_id'))
                ->whereNull('deleted_at')
                ->first();
            
            $posterPath = null;
            
            // Handle poster upload if provided
            if ($request->hasFile('poster_proyek')) {
                $file = $request->file('poster_proyek');
                $fileName = 'poster_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Create custom upload directory path
                $uploadPath = public_path('uploads/poster/' . $request->proyek_id);
                
                // Create directory if it doesn't exist
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }
                
                // Move the file to the custom path
                $file->move($uploadPath, $fileName);
                
                // Save the relative path to the database - ensure it starts with a slash for proper URL construction
                $posterPath = '/uploads/poster/' . $request->proyek_id . '/' . $fileName;
            }
            
            if ($existingLuaran) {
                // Update existing luaran
                $updateData = [
                    'link_proyek' => $request->input('link_proyek'),
                    'deskripsi_luaran' => $request->input('deskripsi_luaran'),
                    'updated_at' => Carbon::now(),
                    'updated_by' => auth()->user()->id ?? session('user_id')
                ];
                
                // Only update poster if a new one is uploaded
                if ($posterPath) {
                    // Delete old poster if exists and if it has a leading slash, remove it for file system operations
                    if ($existingLuaran->poster_proyek) {
                        $oldPosterPath = $existingLuaran->poster_proyek;
                        if (strpos($oldPosterPath, '/') === 0) {
                            $oldPosterPath = substr($oldPosterPath, 1);
                        }
                        
                        if (File::exists(public_path($oldPosterPath))) {
                            File::delete(public_path($oldPosterPath));
                        }
                    }
                    
                    $updateData['poster_proyek'] = $posterPath;
                }
                
                DB::table('d_luaran_proyek')
                    ->where('luaran_proyek_id', $existingLuaran->luaran_proyek_id)
                    ->update($updateData);
                
                $luaranId = $existingLuaran->luaran_proyek_id;
            } else {
                // Create new luaran with UUID
                $luaranId = Str::uuid()->toString();
                
                DB::table('d_luaran_proyek')->insert([
                    'luaran_proyek_id' => $luaranId,
                    'proyek_id' => $request->input('proyek_id'),
                    'poster_proyek' => $posterPath,
                    'link_proyek' => $request->input('link_proyek'),
                    'deskripsi_luaran' => $request->input('deskripsi_luaran'),
                    'created_at' => Carbon::now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                    'updated_at' => Carbon::now(),
                    'updated_by' => auth()->user()->id ?? session('user_id')
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data luaran proyek berhasil disimpan',
                'data' => ['id' => $luaranId]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving luaran proyek', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload dokumentasi proyek (multiple files)
     */
    public function uploadDokumentasi(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'luaran_proyek_id' => 'required|exists:d_luaran_proyek,luaran_proyek_id',
            'dokumentasi.*' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB max per file
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        if (!$request->hasFile('dokumentasi')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah'
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Get proyek_id from luaran
            $luaran = DB::table('d_luaran_proyek')
                ->where('luaran_proyek_id', $request->input('luaran_proyek_id'))
                ->first();
                
            if (!$luaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data luaran proyek tidak ditemukan'
                ], 404);
            }
            
            $proyek_id = $luaran->proyek_id;
            
            $uploadedFiles = $request->file('dokumentasi');
            $insertedIds = [];
            $uploadedDokumentasi = [];
            
            foreach ($uploadedFiles as $file) {
                // Generate a unique filename
                $fileName = 'dokumentasi_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $originalName = $file->getClientOriginalName();
                
                // Create custom upload directory path
                $uploadPath = public_path('uploads/dokumentasi/' . $proyek_id);
                
                // Create directory if it doesn't exist
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }
                
                // Move the file to the custom path
                $file->move($uploadPath, $fileName);
                
                // Save the relative path to the database - ensure it starts with a slash for proper URL construction
                $filePath = '/uploads/dokumentasi/' . $proyek_id . '/' . $fileName;
                
                // Generate UUID for dokumentasi record
                $dokumentasiId = Str::uuid()->toString();
                
                // Insert record
                DB::table('d_dokumentasi_proyek')->insert([
                    'dokumentasi_proyek_id' => $dokumentasiId,
                    'luaran_proyek_id' => $request->input('luaran_proyek_id'),
                    'proyek_id' => $proyek_id, // Add proyek_id field
                    'nama_file' => $originalName,
                    'path_file' => $filePath,
                    'created_at' => Carbon::now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                    'updated_at' => Carbon::now(),
                    'updated_by' => auth()->user()->id ?? session('user_id')
                ]);
                
                $insertedIds[] = $dokumentasiId;
                
                // Add to uploaded dokumentasi array for response
                $uploadedDokumentasi[] = [
                    'dokumentasi_proyek_id' => $dokumentasiId,
                    'luaran_proyek_id' => $request->input('luaran_proyek_id'),
                    'proyek_id' => $proyek_id,
                    'nama_file' => $originalName,
                    'path_file' => $filePath
                ];
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($insertedIds) . ' dokumentasi berhasil diunggah',
                'data' => [
                    'ids' => $insertedIds,
                    'dokumentasi' => $uploadedDokumentasi
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error uploading dokumentasi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah dokumentasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete dokumentasi proyek
     */
    public function deleteDokumentasi($id)
    {
        try {
            // Check if dokumentasi exists
            $dokumentasi = DB::table('d_dokumentasi_proyek')
                ->where('dokumentasi_proyek_id', $id)
                ->whereNull('deleted_at')
                ->first();
            
            if (!$dokumentasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumentasi tidak ditemukan'
                ], 404);
            }
            
            // Start transaction
            DB::beginTransaction();
            
            // Delete file from public path - Handle paths with or without leading slash
            if ($dokumentasi->path_file) {
                $filePath = $dokumentasi->path_file;
                if (strpos($filePath, '/') === 0) {
                    $filePath = substr($filePath, 1);
                }
                
                if (File::exists(public_path($filePath))) {
                    File::delete(public_path($filePath));
                }
            }
            
            // Soft delete the record
            DB::table('d_dokumentasi_proyek')
                ->where('dokumentasi_proyek_id', $id)
                ->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => auth()->user()->id ?? session('user_id')
                ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumentasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting dokumentasi', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dokumentasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
   
}