<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataBidangKeahlianController extends Controller
{
    private function getMahasiswaId()
    {
        $mahasiswaId = session('mahasiswa_id');
        
        if (!$mahasiswaId) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan'], 404);
        }
        return $mahasiswaId;
    }

    public function index(Request $request)
    {
        $mahasiswaId = $this->getMahasiswaId();
        
        if (!$mahasiswaId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        // Get data mahasiswa yang sedang login
        $mahasiswa = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->select('mahasiswa.*', 'user.*')
            ->where('mahasiswa.mahasiswa_id', $mahasiswaId)
            ->whereNull('mahasiswa.deleted_at')
            ->first();
            
        if (!$mahasiswa) {
            return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Ambil bidang keahlian yang sudah dipilih mahasiswa
        $selectedBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian as mbk')
            ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
            ->where('mbk.mahasiswa_id', $mahasiswaId)
            ->whereNull('mbk.deleted_at')
            ->whereNull('bk.deleted_at')
            ->select('bk.bidang_keahlian_id', 'bk.nama_bidang_keahlian')
            ->orderBy('bk.nama_bidang_keahlian', 'asc')
            ->get();

        // Ambil semua bidang keahlian yang tersedia
        $bidangKeahlian = DB::table('m_bidang_keahlian')
            ->whereNull('deleted_at')
            ->orderBy('nama_bidang_keahlian', 'asc')
            ->get();

        // TAMBAHAN: Get data portofolio mahasiswa dengan pagination dan search
        $searchPortofolio = $request->input('search_portofolio');
        
        $portofolioQuery = DB::table('d_portofolio')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at');
            
        if ($searchPortofolio) {
            $portofolioQuery->where(function($q) use ($searchPortofolio) {
                $q->where('nama_kegiatan', 'like', "%$searchPortofolio%")
                ->orWhere('jenis_kegiatan', 'like', "%$searchPortofolio%")
                ->orWhere('penyelenggara', 'like', "%$searchPortofolio%")
                ->orWhere('peran_dalam_kegiatan', 'like', "%$searchPortofolio%");
            });
        }
        
        $portofolioMahasiswa = $portofolioQuery
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'portofolio_page');
            
        $portofolioMahasiswa->appends(['search_portofolio' => $searchPortofolio]);

        return view('pages.Mahasiswa.DataPortofolio.detail_data_portofolio', compact(
            'mahasiswa', 
            'bidangKeahlian',
            'selectedBidangKeahlian',
            'portofolioMahasiswa',  // TAMBAHAN
            'searchPortofolio'      // TAMBAHAN
        ), [
            'titleSidebar' => 'Bidang Keahlian & Portofolio Mahasiswa'
        ]);
    }

    // ========== BIDANG KEAHLIAN METHODS ==========
    
    public function getBidangKeahlianMahasiswa(Request $request)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            $bidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian as mbk')
                ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
                ->where('mbk.mahasiswa_id', $mahasiswaId)
                ->whereNull('mbk.deleted_at')
                ->whereNull('bk.deleted_at')
                ->select('bk.bidang_keahlian_id', 'bk.nama_bidang_keahlian')
                ->orderBy('bk.nama_bidang_keahlian', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $bidangKeahlian
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting bidang keahlian mahasiswa', [
                'mahasiswa_id' => $mahasiswaId ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data bidang keahlian.'
            ], 500);
        }
    }

    public function updateBidangKeahlianMahasiswa(Request $request)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Validasi mahasiswa exists
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan.'
                ], 404);
            }

            // Validasi input bidang keahlian
            $request->validate([
                'bidang_keahlian' => 'nullable|array',
                'bidang_keahlian.*' => 'exists:m_bidang_keahlian,bidang_keahlian_id'
            ], [
                'bidang_keahlian.array' => 'Format bidang keahlian tidak valid.',
                'bidang_keahlian.*.exists' => 'Bidang keahlian yang dipilih tidak valid.'
            ]);

            DB::beginTransaction();
            
            try {
                // Ambil bidang keahlian yang sudah ada untuk mahasiswa ini
                $existingBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian')
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->whereNull('deleted_at')
                    ->pluck('bidang_keahlian_id')
                    ->toArray();

                // Bidang keahlian yang baru dipilih
                $newBidangKeahlian = $request->input('bidang_keahlian', []);

                // ANALISIS PERUBAHAN:
                // 1. Yang perlu dihapus = ada di existing tapi tidak ada di new
                $toDelete = array_diff($existingBidangKeahlian, $newBidangKeahlian);
                
                // 2. Yang perlu ditambah = ada di new tapi tidak ada di existing  
                $toInsert = array_diff($newBidangKeahlian, $existingBidangKeahlian);
                
                // 3. Yang tetap sama = ada di both (tidak perlu diapa-apakan)
                $unchanged = array_intersect($existingBidangKeahlian, $newBidangKeahlian);

                $deletedCount = 0;
                $insertedCount = 0;

                // HAPUS yang tidak dipilih lagi
                if (!empty($toDelete)) {
                    $deletedCount = DB::table('t_mahasiswa_bidang_keahlian')
                        ->where('mahasiswa_id', $mahasiswaId)
                        ->whereIn('bidang_keahlian_id', $toDelete)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'deleted_by' => $mahasiswaId
                        ]);
                }

                // TAMBAH yang baru dipilih
                if (!empty($toInsert)) {
                    foreach ($toInsert as $bidangKeahlianId) {
                        // Double check apakah bidang keahlian valid
                        $bidangKeahlianExists = DB::table('m_bidang_keahlian')
                            ->where('bidang_keahlian_id', $bidangKeahlianId)
                            ->whereNull('deleted_at')
                            ->exists();
                            
                        if ($bidangKeahlianExists) {
                            DB::table('t_mahasiswa_bidang_keahlian')->insert([
                                'mahasiswa_bidang_keahlian_id' => Str::uuid(),
                                'mahasiswa_id' => $mahasiswaId,
                                'bidang_keahlian_id' => $bidangKeahlianId,
                                'created_at' => now(),
                                'created_by' => $mahasiswaId
                            ]);
                            $insertedCount++;
                        }
                    }
                }

                DB::commit();

                // Log activity dengan detail lengkap
                \Log::info('Bidang keahlian mahasiswa updated successfully', [
                    'mahasiswa_id' => $mahasiswaId,
                    'existing_count' => count($existingBidangKeahlian),
                    'new_count' => count($newBidangKeahlian),
                    'unchanged_count' => count($unchanged),
                    'deleted_count' => $deletedCount,
                    'inserted_count' => $insertedCount,
                    'to_delete' => $toDelete,
                    'to_insert' => $toInsert,
                    'unchanged' => $unchanged,
                    'updated_by' => $mahasiswaId
                ]);

                // Buat message yang lebih informatif
                $message = "Bidang keahlian berhasil diperbarui.";
                $details = [];
                
                if ($insertedCount > 0) {
                    $details[] = "{$insertedCount} bidang keahlian ditambahkan";
                }
                if ($deletedCount > 0) {
                    $details[] = "{$deletedCount} bidang keahlian dihapus";
                }
                if (count($unchanged) > 0) {
                    $details[] = count($unchanged) . " bidang keahlian tetap";
                }
                
                if (!empty($details)) {
                    $message .= " (" . implode(', ', $details) . ")";
                }

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => [
                        'mahasiswa_id' => $mahasiswaId,
                        'total_bidang_keahlian' => count($newBidangKeahlian),
                        'changes' => [
                            'inserted' => $insertedCount,
                            'deleted' => $deletedCount,
                            'unchanged' => count($unchanged)
                        ]
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::error('Error updating bidang keahlian mahasiswa', [
                    'mahasiswa_id' => $mahasiswaId,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui bidang keahlian: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Exception in updateBidangKeahlianMahasiswa', [
                'mahasiswa_id' => $mahasiswaId ?? 'unknown',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllBidangKeahlian()
    {
        try {
            $bidangKeahlian = DB::table('m_bidang_keahlian')
                ->whereNull('deleted_at')
                ->orderBy('nama_bidang_keahlian', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $bidangKeahlian
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting all bidang keahlian', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data bidang keahlian.'
            ], 500);
        }
    }

    // ========== PORTOFOLIO METHODS ==========

    public function tambahPortofolioMahasiswa(Request $request)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            $request->validate([
                'nama_kegiatan' => 'required|string|max:255',
                'jenis_kegiatan' => 'required|in:Magang,Pelatihan,Lomba,Penelitian,Pengabdian,Lainnya',
                'deskripsi_kegiatan' => 'nullable|string',
                'penyelenggara' => 'nullable|string|max:255',
                'tingkat_kegiatan' => 'required|in:Internasional,Nasional,Regional,Lainnya',
                'link_kegiatan' => 'nullable|url|max:255',
                'peran_dalam_kegiatan' => 'nullable|string|max:255',
            ], [
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi.',
                'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih.',
                'tingkat_kegiatan.required' => 'Tingkat kegiatan harus dipilih.',
                'link_kegiatan.url' => 'Format link tidak valid.',
            ]);

            DB::beginTransaction();
            
            try {
                $portofolioId = Str::uuid();
                
                $portofolioData = [
                    'portofolio_id' => $portofolioId,
                    'mahasiswa_id' => $mahasiswaId,
                    'nama_kegiatan' => $request->input('nama_kegiatan'),
                    'jenis_kegiatan' => $request->input('jenis_kegiatan'),
                    'deskripsi_kegiatan' => $request->input('deskripsi_kegiatan'),
                    'penyelenggara' => $request->input('penyelenggara'),
                    'tingkat_kegiatan' => $request->input('tingkat_kegiatan'),
                    'link_kegiatan' => $request->input('link_kegiatan'),
                    'peran_dalam_kegiatan' => $request->input('peran_dalam_kegiatan'),
                    'created_at' => now(),
                    'created_by' => $mahasiswaId,
                ];

                DB::table('d_portofolio')->insert($portofolioData);
                
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data portofolio berhasil ditambahkan.'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Data portofolio berhasil ditambahkan.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error adding portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in tambahPortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePortofolioMahasiswa(Request $request, $id)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->where('mahasiswa_id', $mahasiswaId) 
                ->whereNull('deleted_at')
                ->first();

            if (!$portofolio) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data portofolio tidak ditemukan atau Anda tidak memiliki akses.'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Data portofolio tidak ditemukan.');
            }

            $request->validate([
                'nama_kegiatan' => 'required|string|max:255',
                'jenis_kegiatan' => 'required|in:Magang,Pelatihan,Lomba,Penelitian,Pengabdian,Lainnya',
                'deskripsi_kegiatan' => 'nullable|string',
                'penyelenggara' => 'nullable|string|max:255',
                'tingkat_kegiatan' => 'required|in:Internasional,Nasional,Regional,Lainnya',
                'link_kegiatan' => 'nullable|url|max:255',
                'peran_dalam_kegiatan' => 'nullable|string|max:255',
            ], [
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi.',
                'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih.',
                'tingkat_kegiatan.required' => 'Tingkat kegiatan harus dipilih.',
                'link_kegiatan.url' => 'Format link tidak valid.'
            ]);

            DB::beginTransaction();
            
            try {
                $portofolioData = [
                    'nama_kegiatan' => $request->input('nama_kegiatan'),
                    'jenis_kegiatan' => $request->input('jenis_kegiatan'),
                    'deskripsi_kegiatan' => $request->input('deskripsi_kegiatan'),
                    'penyelenggara' => $request->input('penyelenggara'),
                    'tingkat_kegiatan' => $request->input('tingkat_kegiatan'),
                    'link_kegiatan' => $request->input('link_kegiatan'),
                    'peran_dalam_kegiatan' => $request->input('peran_dalam_kegiatan'),
                    'updated_at' => now(),
                    'updated_by' => $mahasiswaId,
                ];

                DB::table('d_portofolio')
                    ->where('portofolio_id', $id)
                    ->where('mahasiswa_id', $mahasiswaId) // Double check ownership
                    ->update($portofolioData);
                    
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data portofolio berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Data portofolio berhasil diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in updatePortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deletePortofolioMahasiswa($id)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->where('mahasiswa_id', $mahasiswaId) // Pastikan portofolio milik mahasiswa yang login
                ->whereNull('deleted_at')
                ->first();
                
            if (!$portofolio) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data portofolio tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            DB::beginTransaction();
            
            try {
                DB::table('d_portofolio')
                    ->where('portofolio_id', $id)
                    ->where('mahasiswa_id', $mahasiswaId) // Double check ownership
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => $mahasiswaId,
                    ]);
                    
                DB::commit();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data portofolio berhasil dihapus.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in deletePortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPortofolioMahasiswaById($id)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->where('mahasiswa_id', $mahasiswaId) // Pastikan portofolio milik mahasiswa yang login
                ->whereNull('deleted_at')
                ->first();
                
            if (!$portofolio) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data portofolio tidak ditemukan atau Anda tidak memiliki akses.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $portofolio
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Exception in getPortofolioMahasiswaById', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}