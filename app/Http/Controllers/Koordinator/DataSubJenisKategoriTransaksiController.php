<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class DataSubJenisKategoriTransaksiController extends Controller
{
    //get untuk dropdown 
    public function getJenisKeuanganTefa()
    {
        try {
            $jenisKeuanganTefa = DB::table('m_jenis_keuangan_tefa')
                ->select('jenis_keuangan_tefa_id', 'nama_jenis_keuangan_tefa')
                ->whereNull('deleted_at')
                ->orderBy('nama_jenis_keuangan_tefa', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $jenisKeuanganTefa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    //get untuk dropdown 
    public function getJenisTransaksi()
    {
        try {
            $jenisTransaksi = DB::table('m_jenis_transaksi')
                ->select('jenis_transaksi_id', 'nama_jenis_transaksi')
                ->whereNull('deleted_at')
                ->orderBy('nama_jenis_transaksi', 'asc')
                ->get();
                
            return response()->json([
                'status' => 'success',
                'data' => $jenisTransaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataSubJenisKategoriTransaksi(Request $request)
    {
        try {
            $search = $request->input('search');
            
            $query = DB::table('m_sub_jenis_transaksi as sjt')
                ->join('m_jenis_transaksi as jt', 'sjt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->join('m_jenis_keuangan_tefa as jkt', 'sjt.jenis_keuangan_tefa_id', '=', 'jkt.jenis_keuangan_tefa_id')
                ->select(
                    'sjt.sub_jenis_transaksi_id',
                    'sjt.nama_sub_jenis_transaksi',
                    'sjt.deskripsi_sub_jenis_transaksi',
                    'jt.nama_jenis_transaksi',
                    'jkt.nama_jenis_keuangan_tefa',
                    'sjt.jenis_transaksi_id',
                    'sjt.jenis_keuangan_tefa_id'
                )
                ->whereNull('sjt.deleted_at')
                ->whereNull('jt.deleted_at')
                ->whereNull('jkt.deleted_at');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('sjt.nama_sub_jenis_transaksi', 'LIKE', "%{$search}%")
                    ->orWhere('jt.nama_jenis_transaksi', 'LIKE', "%{$search}%")
                    ->orWhere('jkt.nama_jenis_keuangan_tefa', 'LIKE', "%{$search}%");
                });
            }

            // PERBAIKAN: Nama variabel harus sama dengan yang digunakan di compact()
            $subJenis = $query->orderBy('jt.nama_jenis_transaksi', 'asc')
                            ->orderBy('jkt.nama_jenis_keuangan_tefa', 'asc')
                            ->paginate(10);
            
            // PERBAIKAN: Gunakan compact dengan nama variabel yang benar
            return view('pages.Koordinator.DataSubJenisKategoriTransaksi.table_data_sub_jenis_transaksi', 
                compact('subJenis', 'search'), [
                    'titleSidebar' => 'Sub Jenis Kategori Transaksi'
                ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error fetching data: ' . $e->getMessage());
        }
    }

    public function storeDataSubJenisTransaksi(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'jenis_transaksi_id' => 'required|exists:m_jenis_transaksi,jenis_transaksi_id',
                'jenis_keuangan_tefa_id' => 'required|exists:m_jenis_keuangan_tefa,jenis_keuangan_tefa_id',
                'nama_sub_jenis_transaksi' => 'required|string|max:255',
                'keterangan' => 'nullable|string|max:1000'
            ], [
                'jenis_transaksi_id.required' => 'Jenis transaksi harus dipilih',
                'jenis_transaksi_id.exists' => 'Jenis transaksi tidak valid',
                'jenis_keuangan_tefa_id.required' => 'Kategori transaksi harus dipilih',
                'jenis_keuangan_tefa_id.exists' => 'Kategori transaksi tidak valid',
                'nama_sub_jenis_transaksi.required' => 'Nama sub jenis kategori harus diisi',
                'nama_sub_jenis_transaksi.max' => 'Nama sub jenis kategori maksimal 255 karakter',
                'keterangan.max' => 'Keterangan maksimal 1000 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah nama sub jenis transaksi sudah ada untuk kombinasi jenis transaksi dan kategori yang sama
            $existing = DB::table('m_sub_jenis_transaksi')
                ->where('nama_sub_jenis_transaksi', $request->nama_sub_jenis_transaksi)
                ->where('jenis_transaksi_id', $request->jenis_transaksi_id)
                ->where('jenis_keuangan_tefa_id', $request->jenis_keuangan_tefa_id)
                ->whereNull('deleted_at')
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nama sub jenis kategori sudah ada untuk kombinasi jenis transaksi dan kategori ini'
                ], 400);
            }

            $newId = Str::uuid()->toString();

            // Insert data baru
            $inserted = DB::table('m_sub_jenis_transaksi')->insert([
                'sub_jenis_transaksi_id' => $newId, 
                'jenis_transaksi_id' => $request->jenis_transaksi_id,
                'jenis_keuangan_tefa_id' => $request->jenis_keuangan_tefa_id,
                'nama_sub_jenis_transaksi' => $request->nama_sub_jenis_transaksi,
                'deskripsi_sub_jenis_transaksi' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($inserted) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data sub jenis kategori transaksi berhasil ditambahkan'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mendapatkan detail data berdasarkan ID
    public function detailDataSubJenisTransaksi($id)
    {
        try {
            $data = DB::table('m_sub_jenis_transaksi as sjt')
                ->join('m_jenis_transaksi as jt', 'sjt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->join('m_jenis_keuangan_tefa as jkt', 'sjt.jenis_keuangan_tefa_id', '=', 'jkt.jenis_keuangan_tefa_id')
                ->select(
                    'sjt.*',
                    'jt.nama_jenis_transaksi',
                    'jkt.nama_jenis_keuangan_tefa'
                )
                ->where('sjt.sub_jenis_transaksi_id', $id)
                ->whereNull('sjt.deleted_at')
                ->first();

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk update data
    public function updateDataSubJenisTransaksi(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'jenis_transaksi_id' => 'required|exists:m_jenis_transaksi,jenis_transaksi_id',
                'jenis_keuangan_tefa_id' => 'required|exists:m_jenis_keuangan_tefa,jenis_keuangan_tefa_id',
                'nama_sub_jenis_transaksi' => 'required|string|max:255',
                'keterangan' => 'nullable|string|max:1000'
            ], [
                'jenis_transaksi_id.required' => 'Jenis transaksi harus dipilih',
                'jenis_transaksi_id.exists' => 'Jenis transaksi tidak valid',
                'jenis_keuangan_tefa_id.required' => 'Kategori transaksi harus dipilih',
                'jenis_keuangan_tefa_id.exists' => 'Kategori transaksi tidak valid',
                'nama_sub_jenis_transaksi.required' => 'Nama sub jenis kategori harus diisi',
                'nama_sub_jenis_transaksi.max' => 'Nama sub jenis kategori maksimal 255 karakter',
                'keterangan.max' => 'Keterangan maksimal 1000 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah data exists
            $existing = DB::table('m_sub_jenis_transaksi')
                ->where('sub_jenis_transaksi_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Cek duplikasi nama (kecuali untuk data yang sedang diedit)
            $duplicate = DB::table('m_sub_jenis_transaksi')
                ->where('nama_sub_jenis_transaksi', $request->nama_sub_jenis_transaksi)
                ->where('jenis_transaksi_id', $request->jenis_transaksi_id)
                ->where('jenis_keuangan_tefa_id', $request->jenis_keuangan_tefa_id)
                ->where('sub_jenis_transaksi_id', '!=', $id)
                ->whereNull('deleted_at')
                ->first();

            if ($duplicate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nama sub jenis kategori sudah ada untuk kombinasi jenis transaksi dan kategori ini'
                ], 400);
            }

            // Update data
            $updated = DB::table('m_sub_jenis_transaksi')
                ->where('sub_jenis_transaksi_id', $id)
                ->update([
                    'jenis_transaksi_id' => $request->jenis_transaksi_id,
                    'jenis_keuangan_tefa_id' => $request->jenis_keuangan_tefa_id,
                    'nama_sub_jenis_transaksi' => $request->nama_sub_jenis_transaksi,
                    'deskripsi_sub_jenis_transaksi' => $request->keterangan,
                    'updated_at' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data sub jenis kategori transaksi berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengupdate data'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDataSubJenisTransaksi($id)
    {
        try {
            $data= DB::table('m_sub_jenis_transaksi')
                ->where('sub_jenis_transaksi_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            DB::table('m_sub_jenis_transaksi')  
                ->where('sub_jenis_transaksi_id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => session('user_id')
            ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data sub jenis kategori transaksi berhasil dihapus'
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting data: ' . $e->getMessage()
            ], 500);
        }
    }
}
