<?php

namespace App\Http\Controllers\Koordinator\DataKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DataKeuanganTefaController extends Controller
{

    public function getJenisTransaksi()
    {
        try {
            $jenisTransaksi = DB::table('m_jenis_transaksi')
                ->whereNull('deleted_at')
                ->orderBy('nama_jenis_transaksi')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $jenisTransaksi
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting jenis transaksi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data jenis transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProyek()
    {
        try {
            $proyek = DB::table('m_proyek')
                ->whereNull('deleted_at')
                ->orderBy('nama_proyek')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $proyek
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting proyek: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data proyek: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataKeuanganTefa(Request $request)
    {
        $jenisTransaksi = DB::table('m_jenis_transaksi')
            ->whereNull('deleted_at')
            ->orderBy('nama_jenis_transaksi')
            ->get();
            
        // Mengambil data jenis keuangan tefa (proyek/non-proyek)
        $jenisKeuangan = DB::table('m_jenis_keuangan_tefa')
            ->whereNull('deleted_at')
            ->orderBy('nama_jenis_keuangan_tefa')
            ->get();
            
        // Mengambil data proyek (untuk dropdown proyek)
        $proyek = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->orderBy('nama_proyek')
            ->get();
            
        // Mengambil data keuangan tefa untuk tabel utama
        $keuanganTefa = DB::table('t_keuangan_tefa as kt')
            ->leftJoin('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
            ->leftJoin('m_jenis_keuangan_tefa as jk', 'kt.jenis_keuangan_tefa_id', '=', 'jk.jenis_keuangan_tefa_id')
            ->leftJoin('m_proyek as p', 'kt.proyek_id', '=', 'p.proyek_id')
            ->leftJoin('m_sub_jenis_transaksi as sjt', 'kt.sub_jenis_transaksi_id', '=', 'sjt.sub_jenis_transaksi_id')
            ->select(
                'kt.keuangan_tefa_id',
                'kt.tanggal_transaksi',
                'jt.nama_jenis_transaksi',
                'jk.nama_jenis_keuangan_tefa',
                'p.nama_proyek',
                'sjt.nama_sub_jenis_transaksi',
                'kt.nama_transaksi',
                'kt.nominal_transaksi',
                'kt.bukti_transaksi',
                'kt.created_at'
            )
            ->whereNull('kt.deleted_at')
            ->orderBy('kt.created_at', 'desc')
            ->paginate(10);
            
        // Dapatkan semua data untuk menghitung saldo secara kronologis
        $allTransactions = DB::table('t_keuangan_tefa as kt')
            ->leftJoin('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
            ->select(
                'kt.keuangan_tefa_id',
                'jt.nama_jenis_transaksi',
                'kt.nominal_transaksi',
                'kt.created_at'
            )
            ->whereNull('kt.deleted_at')
            ->orderBy('kt.created_at', 'asc')
            ->get();
        
        // Hitung total saldo berdasarkan semua transaksi
        $totalSaldo = 0;
        $saldoMap = [];
        
        foreach ($allTransactions as $transaction) {
            // Jika pemasukan, tambahkan ke saldo
            if ($transaction->nama_jenis_transaksi === 'Pemasukan') {
                $totalSaldo += $transaction->nominal_transaksi;
            } 
            // Jika pengeluaran, kurangi dari saldo
            else {
                $totalSaldo -= $transaction->nominal_transaksi;
            }
            
            // Simpan saldo untuk setiap ID transaksi
            $saldoMap[$transaction->keuangan_tefa_id] = $totalSaldo;
        }
        
        // Terapkan saldo yang benar ke koleksi yang sudah diurutkan berdasarkan created_at desc
        $keuanganTefaCollection = collect($keuanganTefa->items())->map(function ($item) use ($saldoMap) {
            $item->saldo = $saldoMap[$item->keuangan_tefa_id];
            return $item;
        });
        
        // Ubah item di keuanganTefa dengan koleksi yang sudah dihitung saldonya
        $keuanganTefa->setCollection($keuanganTefaCollection);
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'jenisTransaksi' => $jenisTransaksi,
                'jenisKeuangan' => $jenisKeuangan,
                'proyek' => $proyek,
                'keuanganTefa' => $keuanganTefa,
            ]);
        }
            
        return view('pages.Koordinator.DataKeuangan.kelola_data_keuangan_tefa', [
            'titleSidebar' => 'Data Keuangan TEFA',
            'active' => 'data-keuangan-tefa',
            'jenisTransaksi' => $jenisTransaksi,
            'jenisKeuangan' => $jenisKeuangan,
            'proyek' => $proyek,
            'keuanganTefa' => $keuanganTefa,
        ]);
    }
    
    public function getSubJenisTransaksi(Request $request)
    {
        try {
            $jenisTransaksiId = $request->input('jenis_transaksi_id');
            $jenisKeuanganTefaId = $request->input('jenis_keuangan_tefa_id');
            
            // Validasi input
            if (!$jenisTransaksiId || !$jenisKeuanganTefaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter jenis transaksi dan jenis keuangan diperlukan'
                ], 400);
            }
            
            // Query sub jenis transaksi
            $subJenisTransaksi = DB::table('m_sub_jenis_transaksi')
                ->where('jenis_transaksi_id', $jenisTransaksiId)
                ->where('jenis_keuangan_tefa_id', $jenisKeuanganTefaId)
                ->whereNull('deleted_at')
                ->select('sub_jenis_transaksi_id as id', 'nama_sub_jenis_transaksi as text')
                ->orderBy('nama_sub_jenis_transaksi')
                ->get();
                
            return response()->json([
                'success' => true,
                'results' => $subJenisTransaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getJenisKeuanganTefa(Request $request)
    {
        try {
            // Ambil data jenis keuangan tefa
            $jenisKeuanganTefa = DB::table('m_jenis_keuangan_tefa')
                ->whereNull('deleted_at')
                ->orderBy('nama_jenis_keuangan_tefa')
                ->get();
                
            return response()->json([
                'success' => true,
                'results' => $jenisKeuanganTefa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Log semua request data
            \Log::info('Request data keuangan tefa: ', [
                'is_single' => $request->input('is_single'),
                'has_file' => $request->hasFile('file_keuangan_tefa'),
                'file_valid' => $request->hasFile('file_keuangan_tefa') ? $request->file('file_keuangan_tefa')->isValid() : 'N/A',
                'data_length' => $request->input('keuangan_tefa_data') ? strlen($request->input('keuangan_tefa_data')) : 0
            ]);
            
            // Proses data berdasarkan mode (single atau multiple)
            $isSingle = $request->input('is_single') === '1';
            
            // Khusus untuk mode multiple, cek apakah ada data JSON yang valid
            if (!$isSingle) {
                $jsonData = $request->input('keuangan_tefa_data');
                if ($jsonData) {
                    $keuanganTefaData = json_decode($jsonData, true);
                    
                    if (!empty($keuanganTefaData)) {
                        // Ada data dalam mode multiple, bypass validasi field form
                        \Log::info('Multiple mode dengan data yang valid, bypass validasi field form');
                        $keuanganTefaData = $this->processMultipleKeuanganTefa($request);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Data keuangan tefa berhasil disimpan',
                            'data' => $keuanganTefaData
                        ]);
                    }
                }
            }
            
            // Jika single mode atau multiple mode tanpa data, lakukan validasi standar
            $rules = [
                'tanggal_transaksi' => 'required|date',
                'jenis_transaksi_id' => 'required|exists:m_jenis_transaksi,jenis_transaksi_id',
                'jenis_keuangan_tefa_id' => 'required|exists:m_jenis_keuangan_tefa,jenis_keuangan_tefa_id',
                'proyek_id_selected' => 'nullable|exists:m_proyek,proyek_id',
                'sub_jenis_transaksi_id' => 'nullable|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id',
                'nama_transaksi' => 'required|string|max:255',
                'nominal' => 'required|string',
                'deskripsi_transaksi' => 'nullable|string',
                'file_keuangan_tefa' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
                'is_single' => 'required|in:0,1',
                'keuangan_tefa_data' => 'nullable|json',
            ];
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($isSingle) {
                // Mode single - simpan satu data
                $keuanganTefa = $this->processSingleKeuanganTefa($request);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data keuangan tefa berhasil disimpan',
                    'data' => $keuanganTefa
                ]);
            } else {
                // Mode multiple - simpan banyak data (seharusnya tidak sampai di sini jika ada data)
                $keuanganTefaData = $this->processMultipleKeuanganTefa($request);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data keuangan tefa berhasil disimpan',
                    'data' => $keuanganTefaData
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error storing keuangan tefa: ' . $e->getMessage());
            \Log::error('Error stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processSingleKeuanganTefa(Request $request)
    {
        // Log semua request data
        \Log::info('Proses Single Keuangan Tefa', $request->all());
        
        $nominal = str_replace('.', '', $request->input('nominal'));
        $filePath = null;

        // Handle file upload - pendekatan mirip dengan addDokumenPenunjang
        if ($request->hasFile('file_keuangan_tefa')) {
            $file = $request->file('file_keuangan_tefa');
            \Log::info('File info:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'valid' => $file->isValid()
            ]);
            
            if ($file->isValid()) {
                // Buat direktori jika belum ada
                $uploadPath = public_path('uploads/keuangan_tefa');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                // Cek direktori
                \Log::info('Directory info:', [
                    'path' => $uploadPath,
                    'exists' => is_dir($uploadPath),
                    'writable' => is_writable($uploadPath)
                ]);
                
                // Generate unique filename
                $filename = 'keuangan_tefa_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                try {
                    // Pindahkan file ke tujuan dengan langsung
                    if ($file->move($uploadPath, $filename)) {
                        $filePath = 'uploads/keuangan_tefa/' . $filename;
                        \Log::info('File berhasil dipindahkan ke ' . $filePath);
                    } else {
                        \Log::error('Gagal memindahkan file');
                    }
                } catch (\Exception $e) {
                    \Log::error('Exception saat memindahkan file: ' . $e->getMessage());
                }
            } else {
                \Log::error('File tidak valid');
            }
        } else {
            \Log::info('Tidak ada file yang diupload');
        }
        
        // Generate UUID untuk keuangan_tefa_id
        $keuanganTefaId = Str::uuid()->toString();
        
        $data = [
            'keuangan_tefa_id' => $keuanganTefaId,
            'tanggal_transaksi' => $request->input('tanggal_transaksi'),
            'jenis_transaksi_id' => $request->input('jenis_transaksi_id'),
            'jenis_keuangan_tefa_id' => $request->input('jenis_keuangan_tefa_id'),
            'proyek_id' => $request->input('proyek_id_selected'),
            'sub_jenis_transaksi_id' => $request->input('sub_jenis_transaksi_id'),
            'nama_transaksi' => $request->input('nama_transaksi'),
            'nominal_transaksi' => $nominal,
            'deskripsi_transaksi' => $request->input('deskripsi_transaksi'),
            'bukti_transaksi' => $filePath, // Jika tidak ada file, ini akan null
            'created_at' => now(),
            'created_by' => auth()->id() ?? session('user_id') ?? 1,
        ];
        
        // Log data yang akan diinsert
        \Log::info('Data untuk insert:', $data);
        
        // Insert ke database dengan explicit keuangan_tefa_id
        DB::table('t_keuangan_tefa')->insert($data);
        
        // Ambil data yang sudah diinsert
        $result = DB::table('t_keuangan_tefa')->where('keuangan_tefa_id', $keuanganTefaId)->first();
        
        // Log hasil
        \Log::info('Data hasil insert:', (array) $result);
        
        return $result;
    }

    private function processMultipleKeuanganTefa(Request $request)
    {
        \Log::info('Proses Multiple Keuangan Tefa', $request->all());
        
        // Decode JSON data dari input
        $jsonData = $request->input('keuangan_tefa_data');
        $keuanganTefaData = json_decode($jsonData, true);
        
        if (empty($keuanganTefaData)) {
            throw new \Exception('Tidak ada data keuangan tefa untuk disimpan');
        }
        
        // Log data yang akan diproses
        \Log::info('Data keuangan dari JSON:', ['count' => count($keuanganTefaData)]);
        
        // Process file upload if exists (for documentation in multiple mode)
        $filePath = null;
        if ($request->hasFile('file_keuangan_tefa')) {
            $file = $request->file('file_keuangan_tefa');
            \Log::info('File multiple info:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'valid' => $file->isValid()
            ]);
            
            if ($file->isValid()) {
                try {
                    // Buat direktori jika belum ada
                    $uploadPath = public_path('uploads/keuangan_tefa');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Cek direktori
                    \Log::info('Directory info multiple:', [
                        'path' => $uploadPath,
                        'exists' => is_dir($uploadPath),
                        'writable' => is_writable($uploadPath)
                    ]);
                    
                    // Generate unique filename
                    $filename = 'multiple_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Pindahkan file ke tujuan
                    if ($file->move($uploadPath, $filename)) {
                        $filePath = 'uploads/keuangan_tefa/' . $filename;
                        \Log::info('File multiple berhasil dipindahkan ke ' . $filePath);
                    } else {
                        \Log::error('Gagal memindahkan file multiple');
                    }
                } catch (\Exception $e) {
                    \Log::error('Exception saat memindahkan file multiple: ' . $e->getMessage());
                    \Log::error('Exception stack trace: ' . $e->getTraceAsString());
                }
            } else {
                \Log::error('File multiple tidak valid');
            }
        } else {
            \Log::info('Tidak ada file multiple yang diupload');
        }
        
        // Array untuk menyimpan ID yang diinsert
        $insertedIds = [];
        
        // Iterate melalui data dan insert ke database
        foreach ($keuanganTefaData as $item) {
            // Prepare nominal (hapus format ribuan)
            $nominal = str_replace('.', '', $item['nominal']);
            
            // Generate a UUID for keuangan_tefa_id
            $keuanganTefaId = Str::uuid()->toString();
            
            $data = [
                'keuangan_tefa_id' => $keuanganTefaId,
                'tanggal_transaksi' => $item['tanggal_transaksi'],
                'jenis_transaksi_id' => $item['jenis_transaksi_id'],
                'jenis_keuangan_tefa_id' => $item['jenis_keuangan_tefa_id'],
                'proyek_id' => $item['proyek_id'],
                'sub_jenis_transaksi_id' => $item['sub_jenis_transaksi_id'],
                'nama_transaksi' => $item['nama_transaksi'],
                'nominal_transaksi' => $nominal,
                'deskripsi_transaksi' => $item['deskripsi_transaksi'] ?? null,
                'bukti_transaksi' => $filePath, // Jika tidak ada file, ini akan null
                'created_at' => now(),
                'created_by' => auth()->id() ?? session('user_id') ?? 1,
            ];
            
            // Log data yang akan diinsert
            \Log::info('Data multiple untuk insert:', ['id' => $keuanganTefaId, 'bukti_transaksi' => $filePath]);
            
            // Insert with explicit keuangan_tefa_id
            DB::table('t_keuangan_tefa')->insert($data);
            
            $insertedIds[] = $keuanganTefaId;
        }
        
        // Ambil data yang sudah diinsert
        $results = DB::table('t_keuangan_tefa')
            ->whereIn('keuangan_tefa_id', $insertedIds)
            ->get();
        
        // Log hasil
        \Log::info('Data hasil multiple insert, jumlah: ' . count($results));
        
        return $results;
    }
}