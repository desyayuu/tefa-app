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

    
    public function storeWithFiles(Request $request)
    {
        try {
            // Log the request for debugging
            \Log::info('Store with files request received', [
                'has_file_main' => $request->hasFile('file_keuangan_tefa'),
                'data_length' => $request->input('keuangan_tefa_data') ? strlen($request->input('keuangan_tefa_data')) : 0,
                'request_keys' => array_keys($request->all())
            ]);
            
            // Decode JSON data from input
            $jsonData = $request->input('keuangan_tefa_data');
            $keuanganTefaData = json_decode($jsonData, true);
            
            if (empty($keuanganTefaData)) {
                throw new \Exception('Tidak ada data keuangan tefa untuk disimpan');
            }
            
            // Log data to be processed
            \Log::info('Multiple keuangan data to process:', ['count' => count($keuanganTefaData)]);
            
            // IMPORTANT: Sort the items by sequence to maintain the order they were added in the form
            usort($keuanganTefaData, function($a, $b) {
                return $a['sequence'] - $b['sequence'];
            });
            
            // Array to store processed records
            $processedRecords = [];
            $currentTimestamp = now();
            
            // Process each keuangan item with its own file - in sequence order
            foreach ($keuanganTefaData as $item) {
                $itemId = $item['id'];
                $nominal = str_replace('.', '', $item['nominal']);
                $filePath = null;

                // Check if there's a specific file for this item
                $fileKey = "file_keuangan_tefa_{$itemId}";
                
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    \Log::info("Processing file for item {$itemId}", [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType()
                    ]);
                    
                    if ($file->isValid()) {
                        // Create directory if needed
                        $uploadPath = public_path('uploads/keuangan_tefa');
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        
                        // Generate unique filename
                        $filename = 'bukti_transaksi_' . $itemId . '_' . time() . '.' . $file->getClientOriginalExtension();
                        
                        // Move file to destination
                        if ($file->move($uploadPath, $filename)) {
                            $filePath = 'uploads/keuangan_tefa/' . $filename;
                            \Log::info("File for item {$itemId} saved to {$filePath}");
                        } else {
                            \Log::error("Failed to move file for item {$itemId}");
                        }
                    } else {
                        \Log::error("Invalid file for item {$itemId}");
                    }
                } else {
                    \Log::info("No file found for item {$itemId}");
                }
                
                // Generate UUID for database record
                $keuanganTefaId = Str::uuid()->toString();
                
                // Prepare data for database insertion
                // We'll use a small timestamp offset to preserve order in the database
                $createdAt = (clone $currentTimestamp)->addSeconds(isset($item['sequence']) ? $item['sequence'] : 0);
                
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
                    'bukti_transaksi' => $filePath, // Each item gets its own file path
                    'created_at' => $createdAt, // Use offset timestamps to preserve order
                    'created_by' => auth()->id() ?? session('user_id') ?? 1,
                    'entry_sequence' => isset($item['sequence']) ? $item['sequence'] : null, // Store sequence for reference
                ];
                
                // Insert into database
                DB::table('t_keuangan_tefa')->insert($data);
                
                $processedRecords[] = $keuanganTefaId;
                \Log::info("Record inserted for item {$itemId} with keuangan_tefa_id {$keuanganTefaId} and sequence {$item['sequence']}");
            }
            
            // Fetch the inserted records
            $results = DB::table('t_keuangan_tefa')
                ->whereIn('keuangan_tefa_id', $processedRecords)
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data keuangan tefa berhasil disimpan',
                'data' => $results,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error storing keuangan tefa with files: ' . $e->getMessage());
            \Log::error('Error stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function getDataKeuanganTefa(Request $request){
    //     $jenisTransaksi = DB::table('m_jenis_transaksi')
    //         ->whereNull('deleted_at')
    //         ->orderBy('nama_jenis_transaksi')
    //         ->get();

    //     $jenisKeuangan = DB::table('m_jenis_keuangan_tefa')
    //         ->whereNull('deleted_at')
    //         ->orderBy('nama_jenis_keuangan_tefa')
    //         ->get();
            
    //     $proyek = DB::table('m_proyek')
    //         ->whereNull('deleted_at')
    //         ->orderBy('nama_proyek')
    //         ->get();
            
    //     // Build query with filters
    //     $query = DB::table('t_keuangan_tefa as kt')
    //         ->leftJoin('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
    //         ->leftJoin('m_jenis_keuangan_tefa as jk', 'kt.jenis_keuangan_tefa_id', '=', 'jk.jenis_keuangan_tefa_id')
    //         ->leftJoin('m_proyek as p', 'kt.proyek_id', '=', 'p.proyek_id')
    //         ->leftJoin('m_sub_jenis_transaksi as sjt', 'kt.sub_jenis_transaksi_id', '=', 'sjt.sub_jenis_transaksi_id')
    //         ->select(
    //             'kt.keuangan_tefa_id',
    //             'kt.tanggal_transaksi',
    //             'jt.nama_jenis_transaksi',
    //             'jk.nama_jenis_keuangan_tefa',
    //             'p.nama_proyek',
    //             'sjt.nama_sub_jenis_transaksi',
    //             'kt.nama_transaksi',
    //             'kt.nominal_transaksi',
    //             'kt.bukti_transaksi',
    //             'kt.created_at',
    //             'kt.entry_sequence'
    //         )
    //         ->whereNull('kt.deleted_at');
        
    //     // Apply date range filter
    //     if ($request->filled('tanggal_mulai')) {
    //         $query->where('kt.tanggal_transaksi', '>=', $request->input('tanggal_mulai'));
    //     }
        
    //     if ($request->filled('tanggal_akhir')) {
    //         $query->where('kt.tanggal_transaksi', '<=', $request->input('tanggal_akhir'));
    //     }
        
    //     // Apply jenis transaksi filter
    //     if ($request->filled('jenis_transaksi')) {
    //         $query->where('jt.nama_jenis_transaksi', $request->input('jenis_transaksi'));
    //     }
        
    //     // Apply jenis keuangan filter
    //     if ($request->filled('jenis_keuangan')) {
    //         $query->where('jk.nama_jenis_keuangan_tefa', $request->input('jenis_keuangan'));
    //     }

    //     if ($request->filled('sub_jenis_transaksi_id')) {
    //         $query->where('kt.sub_jenis_transaksi_id', $request->input('sub_jenis_transaksi_id'));
    //     }

        
    //     // Apply nama transaksi filter (using LIKE for partial matches)
    //     if ($request->filled('nama_transaksi')) {
    //         $query->where('kt.nama_transaksi', 'LIKE', '%' . $request->input('nama_transaksi') . '%');
    //     }
        
    //     // Apply proyek filter
    //     if ($request->filled('proyek_id')) {
    //         $query->where('kt.proyek_id', $request->input('proyek_id'));
    //     }
        
    //     // Apply nominal range filter
    //     if ($request->filled('nominal_min')) {
    //         $query->where('kt.nominal_transaksi', '>=', $request->input('nominal_min'));
    //     }
        
    //     if ($request->filled('nominal_max')) {
    //         $query->where('kt.nominal_transaksi', '<=', $request->input('nominal_max'));
    //     }
        
    //     // Order by created_at DESC as the primary sort
    //     $query->orderBy('kt.created_at', 'desc');
        
    //     // Execute the query with pagination
    //     $keuanganTefa = $query->paginate(10);
        
    //     // Log query if debug mode is on
    //     if (config('app.debug')) {
    //         \Log::info('Keuangan Tefa Query', [
    //             'sql' => $query->toSql(),
    //             'bindings' => $query->getBindings(),
    //             'filters' => $request->all()
    //         ]);
    //     }
        
    //     $allTransactionsQuery = clone $query;

    //     // Modify the select clause for the saldo calculation query
    //     $allTransactionsQuery->select(
    //         'kt.keuangan_tefa_id',
    //         'kt.tanggal_transaksi', // Include for proper chronological ordering
    //         'kt.created_at',
    //         'jt.nama_jenis_transaksi',
    //         'kt.nominal_transaksi'
    //     );

    //     // Make sure transactions are ordered chronologically for running balance calculation
    //     $allTransactionsQuery->orderBy('kt.tanggal_transaksi', 'asc')
    //                         ->orderBy('kt.created_at', 'asc');

    //     // Execute this query to get all filtered transactions for saldo calculation
    //     $allTransactions = $allTransactionsQuery->get();

    //     // Calculate running total (saldo) only for filtered transactions
    //     $totalSaldo = 0;
    //     $saldoMap = [];

    //     foreach ($allTransactions as $transaction) {
    //         if ($transaction->nama_jenis_transaksi === 'Pemasukan') {
    //             $totalSaldo += $transaction->nominal_transaksi;
    //         } else {
    //             $totalSaldo -= $transaction->nominal_transaksi;
    //         }
    //         $saldoMap[$transaction->keuangan_tefa_id] = $totalSaldo;
    //     }

    //     // Now execute the main query with pagination
    //     $keuanganTefa = $query->paginate(10);

    //     // Map saldo to each record in the paginated results
    //     $keuanganTefaCollection = collect($keuanganTefa->items())->map(function ($item) use ($saldoMap) {
    //         $item->saldo = $saldoMap[$item->keuangan_tefa_id] ?? 0; // Use null coalescing operator to handle edge cases
    //         return $item;
    //     });

    //     $keuanganTefa->setCollection($keuanganTefaCollection);
                
    //             // Check if this is an AJAX request
    //             if ($request->ajax() || $request->wantsJson()) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'jenisTransaksi' => $jenisTransaksi,
    //                     'jenisKeuangan' => $jenisKeuangan,
    //                     'proyek' => $proyek,
    //                     'keuanganTefa' => $keuanganTefa,
    //                     'filters_applied' => $request->has('tanggal_mulai') 
    //                         || $request->has('tanggal_akhir') 
    //                         || $request->has('jenis_transaksi') 
    //                         || $request->has('jenis_keuangan')
    //                         || $request->has('nama_transaksi')
    //                         || $request->has('proyek_id')
    //                         || $request->has('nominal_min')
    //                         || $request->has('nominal_max')
    //                         || $request->has('sub_jenis_transaksi_id')
    //                 ]);
    //             }
                    
    //             return view('pages.Koordinator.DataKeuangan.kelola_data_keuangan_tefa', [
    //                 'titleSidebar' => 'Data Keuangan TEFA',
    //                 'active' => 'data-keuangan-tefa',
    //                 'jenisTransaksi' => $jenisTransaksi,
    //                 'jenisKeuangan' => $jenisKeuangan,
    //                 'proyek' => $proyek,
    //                 'keuanganTefa' => $keuanganTefa,
    //             ]);
    // }
    public function getDataKeuanganTefa(Request $request)
{
    $jenisTransaksi = DB::table('m_jenis_transaksi')
        ->whereNull('deleted_at')
        ->orderBy('nama_jenis_transaksi')
        ->get();

    $jenisKeuangan = DB::table('m_jenis_keuangan_tefa')
        ->whereNull('deleted_at')
        ->orderBy('nama_jenis_keuangan_tefa')
        ->get();
        
    $proyek = DB::table('m_proyek')
        ->whereNull('deleted_at')
        ->orderBy('nama_proyek')
        ->get();
        
    // Build query with filters
    $query = DB::table('t_keuangan_tefa as kt')
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
            'kt.created_at',
            'kt.entry_sequence'
        )
        ->whereNull('kt.deleted_at');
    
    // Apply date range filter
    if ($request->filled('tanggal_mulai')) {
        $query->where('kt.tanggal_transaksi', '>=', $request->input('tanggal_mulai'));
    }
    
    if ($request->filled('tanggal_akhir')) {
        $query->where('kt.tanggal_transaksi', '<=', $request->input('tanggal_akhir'));
    }
    
    // Apply jenis transaksi filter
    if ($request->filled('jenis_transaksi')) {
        $query->where('jt.nama_jenis_transaksi', $request->input('jenis_transaksi'));
    }
    
    // Apply jenis keuangan filter
    if ($request->filled('jenis_keuangan')) {
        $query->where('jk.nama_jenis_keuangan_tefa', $request->input('jenis_keuangan'));
    }

    if ($request->filled('sub_jenis_transaksi_id')) {
        $query->where('kt.sub_jenis_transaksi_id', $request->input('sub_jenis_transaksi_id'));
    }
    
    // Apply nama transaksi filter (using LIKE for partial matches)
    if ($request->filled('nama_transaksi')) {
        $query->where('kt.nama_transaksi', 'LIKE', '%' . $request->input('nama_transaksi') . '%');
    }
    
    // Apply proyek filter
    if ($request->filled('proyek_id')) {
        $query->where('kt.proyek_id', $request->input('proyek_id'));
    }
    
    // Apply nominal range filter
    if ($request->filled('nominal_min')) {
        $query->where('kt.nominal_transaksi', '>=', $request->input('nominal_min'));
    }
    
    if ($request->filled('nominal_max')) {
        $query->where('kt.nominal_transaksi', '<=', $request->input('nominal_max'));
    }
    
    // Log query if debug mode is on
    if (config('app.debug')) {
        \Log::info('Keuangan Tefa Query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'filters' => $request->all()
        ]);
    }
    
    // Clone the query for saldo calculation
    $saldoQuery = clone $query;
    
    // Get all transactions for saldo calculation in chronological order
    $allTransactions = $saldoQuery->select(
            'kt.keuangan_tefa_id',
            'kt.tanggal_transaksi',
            'jt.nama_jenis_transaksi',
            'kt.nominal_transaksi'
        )
        ->orderBy('kt.tanggal_transaksi', 'asc')  // Sort by date ascending for correct saldo calculation
        ->orderBy('kt.created_at', 'asc')         // Then by creation time if multiple on same date
        ->get();
    
    // Calculate saldo for each transaction in chronological order
    $saldos = [];
    $runningTotal = 0;
    
    foreach ($allTransactions as $transaction) {
        if ($transaction->nama_jenis_transaksi === 'Pemasukan') {
            $runningTotal += $transaction->nominal_transaksi;
        } else {
            $runningTotal -= $transaction->nominal_transaksi;
        }
        $saldos[$transaction->keuangan_tefa_id] = $runningTotal;
    }
    
    // Now get paginated data in reverse chronological order for display
    $keuanganTefa = $query->orderBy('kt.tanggal_transaksi', 'desc')  // Newest first
                          ->orderBy('kt.created_at', 'desc')          // Then by creation time
                          ->paginate(10);
    
    // Attach saldo to each record
    $keuanganTefaCollection = collect($keuanganTefa->items())->map(function ($item) use ($saldos) {
        $item->saldo = $saldos[$item->keuangan_tefa_id] ?? 0;
        return $item;
    });
    
    $keuanganTefa->setCollection($keuanganTefaCollection);
    
    // Check if this is an AJAX request
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'jenisTransaksi' => $jenisTransaksi,
            'jenisKeuangan' => $jenisKeuangan,
            'proyek' => $proyek,
            'keuanganTefa' => $keuanganTefa,
            'filters_applied' => $request->has('tanggal_mulai') 
                || $request->has('tanggal_akhir') 
                || $request->has('jenis_transaksi') 
                || $request->has('jenis_keuangan')
                || $request->has('nama_transaksi')
                || $request->has('proyek_id')
                || $request->has('nominal_min')
                || $request->has('nominal_max')
                || $request->has('sub_jenis_transaksi_id')
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
            
            if (!$isSingle) {
                $jsonData = $request->input('keuangan_tefa_data');
                if ($jsonData) {
                    $keuanganTefaData = json_decode($jsonData, true);
                    
                    if (!empty($keuanganTefaData)) {
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
                $filename = 'bukti_transaksi_' . time() . '_' . '.' . $file->getClientOriginalExtension();
                
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
                    $filename = 'bukti_transaksi_' . time() . '_' . '.' . $file->getClientOriginalExtension();
                    
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

    public function getKeuanganTefaById($id)
    {
        try {
            $keuanganTefa = DB::table('t_keuangan_tefa as kt')
                ->leftJoin('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->leftJoin('m_jenis_keuangan_tefa as jk', 'kt.jenis_keuangan_tefa_id', '=', 'jk.jenis_keuangan_tefa_id')
                ->leftJoin('m_proyek as p', 'kt.proyek_id', '=', 'p.proyek_id')
                ->leftJoin('m_sub_jenis_transaksi as sjt', 'kt.sub_jenis_transaksi_id', '=', 'sjt.sub_jenis_transaksi_id')
                ->select(
                    'kt.*',
                    'jt.nama_jenis_transaksi',
                    'jk.nama_jenis_keuangan_tefa',
                    'p.nama_proyek',
                    'sjt.nama_sub_jenis_transaksi'
                )
                ->where('kt.keuangan_tefa_id', $id)
                ->first();
                
            if (!$keuanganTefa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data keuangan tefa tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $keuanganTefa
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting keuangan tefa: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Update keuangan tefa request:', $request->all());
            
            // Validate basic fields first
            $rules = [
                'edit_tanggal_transaksi' => 'required|date',
                'edit_jenis_transaksi_id' => 'required|exists:m_jenis_transaksi,jenis_transaksi_id',
                'edit_jenis_keuangan_tefa_id' => 'required|exists:m_jenis_keuangan_tefa,jenis_keuangan_tefa_id',
                'edit_nama_transaksi' => 'required|string|max:255',
                'edit_nominal' => 'required|string',
                'edit_deskripsi_transaksi' => 'nullable|string',
                'edit_file_keuangan_tefa' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
            ];
            
            // Get jenis keuangan & jenis transaksi to determine conditional validation rules
            $jenisTransaksiId = $request->input('edit_jenis_transaksi_id');
            $jenisKeuanganId = $request->input('edit_jenis_keuangan_tefa_id');
            
            $jenisTransaksi = DB::table('m_jenis_transaksi')
                ->where('jenis_transaksi_id', $jenisTransaksiId)
                ->first();
                
            $jenisKeuanganTefa = DB::table('m_jenis_keuangan_tefa')
                ->where('jenis_keuangan_tefa_id', $jenisKeuanganId)
                ->first();
            
            // Add conditional validation rules
            if ($jenisKeuanganTefa && $jenisKeuanganTefa->nama_jenis_keuangan_tefa === 'Proyek') {
                $rules['edit_proyek_id_selected'] = 'required|exists:m_proyek,proyek_id';
            } else {
                $rules['edit_proyek_id_selected'] = 'nullable|exists:m_proyek,proyek_id';
            }
            
            if ($jenisTransaksi && $jenisTransaksi->nama_jenis_transaksi === 'Pengeluaran') {
                $rules['edit_sub_jenis_transaksi_id'] = 'required|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id';
            } else {
                $rules['edit_sub_jenis_transaksi_id'] = 'nullable|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id';
            }
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get current data
            $currentData = DB::table('t_keuangan_tefa')->where('keuangan_tefa_id', $id)->first();
            if (!$currentData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data keuangan tefa tidak ditemukan'
                ], 404);
            }
            
            // Handle file upload if there's a new file
            $filePath = $currentData->bukti_transaksi;
            if ($request->hasFile('edit_file_keuangan_tefa')) {
                $file = $request->file('edit_file_keuangan_tefa');
                
                if ($file->isValid()) {
                    $uploadPath = public_path('uploads/keuangan_tefa');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    $filename = 'keuangan_tefa_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    if ($file->move($uploadPath, $filename)) {
                        $filePath = 'uploads/keuangan_tefa/' . $filename;
                        
                        if ($currentData->bukti_transaksi && file_exists(public_path($currentData->bukti_transaksi))) {
                            unlink(public_path($currentData->bukti_transaksi));
                        }
                    }
                }
            }
            
            // Process nominal (remove thousand separators)
            $nominal = str_replace('.', '', $request->input('edit_nominal'));
            
            // Prepare update data
            $data = [
                'tanggal_transaksi' => $request->input('edit_tanggal_transaksi'),
                'jenis_transaksi_id' => $request->input('edit_jenis_transaksi_id'),
                'jenis_keuangan_tefa_id' => $request->input('edit_jenis_keuangan_tefa_id'),
                'nama_transaksi' => $request->input('edit_nama_transaksi'),
                'nominal_transaksi' => $nominal,
                'deskripsi_transaksi' => $request->input('edit_deskripsi_transaksi'),
                'bukti_transaksi' => $filePath,
                'updated_at' => now(),
                'updated_by' => auth()->id() ?? session('user_id') ?? 1,
            ];
            
            // Set proyek_id based on jenis keuangan
            if ($jenisKeuanganTefa && $jenisKeuanganTefa->nama_jenis_keuangan_tefa === 'Proyek') {
                $data['proyek_id'] = $request->input('edit_proyek_id_selected');
            } else {
                $data['proyek_id'] = null;
            }
            
            // Set sub_jenis_transaksi_id based on jenis transaksi
            if ($jenisTransaksi && $jenisTransaksi->nama_jenis_transaksi === 'Pengeluaran') {
                $data['sub_jenis_transaksi_id'] = $request->input('edit_sub_jenis_transaksi_id');
            } else {
                $data['sub_jenis_transaksi_id'] = null;
            }
            
            \Log::info('Data to update:', $data);
            
            // Update database
            DB::table('t_keuangan_tefa')->where('keuangan_tefa_id', $id)->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Data keuangan tefa berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating keuangan tefa: ' . $e->getMessage());
            \Log::error('Exception stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSummary()
    {
        try {
            // Hitung total pemasukan
            $totalPemasukan = DB::table('t_keuangan_tefa as kt')
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->where('jt.nama_jenis_transaksi', 'Pemasukan')
                ->whereNull('kt.deleted_at')
                ->sum('kt.nominal_transaksi');
            
            // Hitung total pengeluaran
            $totalPengeluaran = DB::table('t_keuangan_tefa as kt')
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->where('jt.nama_jenis_transaksi', 'Pengeluaran')
                ->whereNull('kt.deleted_at')
                ->sum('kt.nominal_transaksi');
            
            // Hitung saldo (pemasukan - pengeluaran)
            $saldo = $totalPemasukan - $totalPengeluaran;
            
            return response()->json([
                'success' => true,
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo' => $saldo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
