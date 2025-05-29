<?php

namespace App\Http\Controllers\Koordinator\DataKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DataKeluarKeuanganProyekController extends Controller
{
    public function getDataProyek(Request $request)
    {
        $search = $request->get('search');
        $query = DB::table('m_proyek')
            ->select(
                'm_proyek.proyek_id as id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.status_proyek',
                'm_proyek.dana_pendanaan',
                't_project_leader.leader_type',
                DB::raw('CASE
                    WHEN t_project_leader.leader_type = "Dosen" THEN d_dosen.nama_dosen
                    WHEN t_project_leader.leader_type = "Profesional" THEN d_profesional.nama_profesional
                    ELSE NULL
                END as nama_project_leader')
            )
            ->whereNull('m_proyek.deleted_at')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
            ->leftJoin('d_dosen', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_dosen.dosen_id')->where('t_project_leader.leader_type', '=', 'Dosen');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_profesional.profesional_id')->where('t_project_leader.leader_type', '=', 'Profesional');
            });
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'like', '%' . $search . '%')
                  ->orWhere('m_proyek.status_proyek', 'like', '%' . $search . '%')
                  ->orWhere('d_dosen.nama_dosen', 'like', '%' . $search . '%')
                  ->orWhere('d_profesional.nama_profesional', 'like', '%' . $search . '%');
            });
        }
        
        $proyek = $query->orderBy('m_proyek.created_at', 'desc')->paginate(10);
        return view('pages.Koordinator.DataKeuanganProyek.table_proyek_dana_keluar', [
            'proyek' => $proyek,
            'search' => $search,
            'titleSidebar' => 'Data Pengeluaran Keuangan Proyek'
        ]);
    }

    public function getTransaksiDetailForEdit(Request $request, $proyekId, $transaksiId)
    {
        try {
            Log::info('Getting transaction detail for edit', [
                'proyek_id' => $proyekId,
                'transaksi_id' => $transaksiId
            ]);

            // Get transaction details with all related data
            $transaksi = DB::table('t_keuangan_tefa')
                ->select(
                    't_keuangan_tefa.keuangan_tefa_id',
                    't_keuangan_tefa.proyek_id',
                    't_keuangan_tefa.tanggal_transaksi',
                    't_keuangan_tefa.jenis_transaksi_id',
                    't_keuangan_tefa.jenis_keuangan_tefa_id',
                    't_keuangan_tefa.nama_transaksi',
                    't_keuangan_tefa.deskripsi_transaksi',
                    't_keuangan_tefa.nominal_transaksi',
                    't_keuangan_tefa.bukti_transaksi',
                    't_keuangan_tefa.sub_jenis_transaksi_id',
                    'm_jenis_transaksi.nama_jenis_transaksi',
                    'm_jenis_keuangan_tefa.nama_jenis_keuangan_tefa',
                    'm_sub_jenis_transaksi.nama_sub_jenis_transaksi',
                    'm_proyek.nama_proyek'
                )
                ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
                ->join('m_jenis_keuangan_tefa', 't_keuangan_tefa.jenis_keuangan_tefa_id', '=', 'm_jenis_keuangan_tefa.jenis_keuangan_tefa_id')
                ->join('m_proyek', 't_keuangan_tefa.proyek_id', '=', 'm_proyek.proyek_id')
                ->leftJoin('m_sub_jenis_transaksi', 't_keuangan_tefa.sub_jenis_transaksi_id', '=', 'm_sub_jenis_transaksi.sub_jenis_transaksi_id')
                ->where('t_keuangan_tefa.keuangan_tefa_id', $transaksiId)
                ->where('t_keuangan_tefa.proyek_id', $proyekId) // Extra security check
                ->where('m_jenis_transaksi.nama_jenis_transaksi', 'Pengeluaran') // Only allow income transactions
                ->whereNull('t_keuangan_tefa.deleted_at')
                ->first();

            if (!$transaksi) {
                Log::warning('Transaction not found or not accessible', [
                    'proyek_id' => $proyekId,
                    'transaksi_id' => $transaksiId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan atau tidak dapat diakses'
                ], 404);
            }

            // Format tanggal untuk input date HTML (YYYY-MM-DD)
            $tanggalFormatted = date('Y-m-d', strtotime($transaksi->tanggal_transaksi));

            // Format the data for frontend
            $formattedData = [
                'keuangan_tefa_id' => $transaksi->keuangan_tefa_id,
                'proyek_id' => $transaksi->proyek_id,
                'nama_proyek' => $transaksi->nama_proyek,
                'tanggal_transaksi' => $tanggalFormatted,
                'jenis_transaksi_id' => $transaksi->jenis_transaksi_id,
                'jenis_keuangan_tefa_id' => $transaksi->jenis_keuangan_tefa_id,
                'nama_transaksi' => $transaksi->nama_transaksi,
                'deskripsi_transaksi' => $transaksi->deskripsi_transaksi,
                'nominal_transaksi' => number_format($transaksi->nominal_transaksi, 0, ',', '.'),
                'nominal_raw' => $transaksi->nominal_transaksi,
                'bukti_transaksi' => $transaksi->bukti_transaksi,
                'sub_jenis_transaksi_id' => $transaksi->sub_jenis_transaksi_id,
                'nama_sub_jenis_transaksi' => $transaksi->nama_sub_jenis_transaksi,
                'has_file' => !empty($transaksi->bukti_transaksi),
                'file_name' => $transaksi->bukti_transaksi ? basename($transaksi->bukti_transaksi) : null,
                'file_url' => $transaksi->bukti_transaksi ? asset($transaksi->bukti_transaksi) : null
            ];

            Log::info('Transaction detail retrieved successfully for edit', [
                'transaksi_id' => $transaksiId,
                'proyek_id' => $proyekId,
                'tanggal_formatted' => $tanggalFormatted,
                'has_subkategori' => !empty($transaksi->sub_jenis_transaksi_id),
                'has_file' => !empty($transaksi->bukti_transaksi)
            ]);

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction detail for edit: ' . $e->getMessage(), [
                'proyek_id' => $proyekId,
                'transaksi_id' => $transaksiId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data transaksi'
            ], 500);
        }
    }
    
    public function hapusTransaksi(Request $request, $transaksiId)
    {
        DB::beginTransaction();
        
        try {
            DB::table('t_keuangan_tefa')
                ->where('keuangan_tefa_id', $transaksiId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => auth()->user()->id ?? session('user_id')
                ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function downloadBuktiTransaksi($fileName)
    {
        $path = public_path($fileName);
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()->with('error', 'File tidak ditemukan');
    }
    
    public function getSubJenisTransaksi(Request $request)
    {
        try {
            $jenisTransaksiId = $request->input('jenis_transaksi_id');
            $jenisKeuanganTefaId = $request->input('jenis_keuangan_tefa_id');
            
            if (!$jenisTransaksiId || !$jenisKeuanganTefaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak lengkap',
                    'results' => []
                ]);
            }
            
            $subJenis = DB::table('m_sub_jenis_transaksi')
                ->select(
                    'm_sub_jenis_transaksi.sub_jenis_transaksi_id as id',
                    'm_sub_jenis_transaksi.nama_sub_jenis_transaksi as text',
                    'm_sub_jenis_transaksi.deskripsi_sub_jenis_transaksi'
                )
                ->where('jenis_transaksi_id', $jenisTransaksiId)
                ->where('jenis_keuangan_tefa_id', $jenisKeuanganTefaId)
                ->whereNull('deleted_at')
                ->orderBy('nama_sub_jenis_transaksi', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'results' => $subJenis,
                'count' => $subJenis->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting sub jenis transaksi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'results' => []
            ], 500);
        }
    }

    public function getDataKeluarKeuanganProyek(Request $request, $proyekId)
    {
        // Get project details
        $proyek = DB::table('m_proyek')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.dana_pendanaan',
                'm_proyek.status_proyek'
            )
            ->where('proyek_id', $proyekId)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$proyek) {
            return redirect()->route('koordinator.dataKeluarKeuanganProyek')
                ->with('error', 'Data proyek tidak ditemukan');
        }
        
        // Get transaction types for dropdown in add transaction modal
        $jenisTransaksi = DB::table('m_jenis_transaksi')
            ->where('nama_jenis_transaksi', 'Pengeluaran')
            ->whereNull('deleted_at')
            ->get();
        
        // Get financial categories for dropdown in add transaction modal
        $jenisKeuanganTefa = DB::table('m_jenis_keuangan_tefa')
            ->whereNull('deleted_at')
            ->get();
        
        // Get subkategori pengeluaran - ENHANCED to handle empty cases better
        $subkategoriPengeluaran = $this->getSubkategoriPengeluaranForProyek();
        
        // Enhanced logging for debugging
        Log::info('Subkategori pengeluaran for proyek', [
            'proyek_id' => $proyekId,
            'subkategori_count' => $subkategoriPengeluaran->count(),
            'has_subkategori' => $subkategoriPengeluaran->count() > 0
        ]);
        
        // Get total income for this project
        $totalPengeluaran = DB::table('t_keuangan_tefa')
            ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
            ->where('t_keuangan_tefa.proyek_id', $proyekId)
            ->where('m_jenis_transaksi.nama_jenis_transaksi', 'Pengeluaran')
            ->whereNull('t_keuangan_tefa.deleted_at')
            ->sum('t_keuangan_tefa.nominal_transaksi');
        
        return view('pages.Koordinator.DataKeuanganProyek.kelola_dana_keluar_proyek', [
            'proyek' => $proyek,
            'jenisTransaksi' => $jenisTransaksi,
            'jenisKeuanganTefa' => $jenisKeuanganTefa,
            'subkategoriPengeluaran' => $subkategoriPengeluaran,
            'hasSubkategoriPengeluaran' => $subkategoriPengeluaran->count() > 0, // Add this flag
            'totalPengeluaran' => $totalPengeluaran,
            'titleSidebar' => 'Detail Pengeluaran Keuangan Proyek'
        ]);
    }
    
    private function getSubkategoriPengeluaranForProyek()
    {
        try {
            // Get "Pengeluaran" transaction type ID
            $jenisTransaksiId = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->value('jenis_transaksi_id');
            
            // Get "Proyek" financial category ID
            $jenisKeuanganId = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->value('jenis_keuangan_tefa_id');
            
            if (!$jenisTransaksiId || !$jenisKeuanganId) {
                Log::warning('Missing jenis_transaksi_id or jenis_keuangan_tefa_id for subcategory lookup', [
                    'jenis_transaksi_id' => $jenisTransaksiId,
                    'jenis_keuangan_id' => $jenisKeuanganId
                ]);
                return collect([]);
            }
            
            // Get subcategories using the same query structure as keuangan TEFA
            $subkategori = DB::table('m_sub_jenis_transaksi')
                ->select(
                    'm_sub_jenis_transaksi.sub_jenis_transaksi_id',
                    'm_sub_jenis_transaksi.nama_sub_jenis_transaksi',
                    'm_sub_jenis_transaksi.deskripsi_sub_jenis_transaksi'
                )
                ->where('m_sub_jenis_transaksi.jenis_transaksi_id', $jenisTransaksiId)
                ->where('m_sub_jenis_transaksi.jenis_keuangan_tefa_id', $jenisKeuanganId)
                ->whereNull('m_sub_jenis_transaksi.deleted_at')
                ->orderBy('m_sub_jenis_transaksi.nama_sub_jenis_transaksi', 'asc')
                ->get();
            
            Log::info('Subkategori lookup result', [
                'count' => $subkategori->count(),
                'jenis_transaksi_id' => $jenisTransaksiId,
                'jenis_keuangan_id' => $jenisKeuanganId,
                'subkategori_names' => $subkategori->pluck('nama_sub_jenis_transaksi')->toArray()
            ]);
            
            return $subkategori;
            
        } catch (\Exception $e) {
            Log::error('Error getting subkategori pengeluaran for proyek: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }
    
    public function tambahTransaksiPengeluaran(Request $request)
    {
        try {
            Log::info('Tambah transaksi pengeluaran request:', [
                'is_single' => $request->input('is_single'),
                'tanggal_transaksi' => $request->input('tanggal_transaksi'),
                'has_file' => $request->hasFile('bukti_transaksi'),
                'has_subkategori' => $request->filled('subkategori_pengeluaran_id'),
                'subkategori_value' => $request->input('subkategori_pengeluaran_id'),
                'data_length' => $request->input('pengeluaran_data') ? strlen($request->input('pengeluaran_data')) : 0
            ]);
            
            // Get the IDs for "Pengeluaran" and "Proyek"
            $pengeluaranId = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->value('jenis_transaksi_id');
                
            $proyekId = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->value('jenis_keuangan_tefa_id');
            
            // Force these values regardless of what's submitted in the form
            $request->merge([
                'jenis_transaksi_id' => $pengeluaranId,
                'jenis_keuangan_tefa_id' => $proyekId
            ]);
            
            // Process data based on mode (single or multiple)
            $isSingle = $request->input('is_single') === '1';
            
            if (!$isSingle) {
                // Multiple mode
                $jsonData = $request->input('pengeluaran_data');
                if ($jsonData) {
                    $pengeluaranData = json_decode($jsonData, true);
                    
                    if (!empty($pengeluaranData)) {
                        return $this->storeWithFiles($request);
                    }
                }
            }
            
            // Enhanced validation rules - subkategori is completely optional
            $validationRules = [
                'proyek_id' => 'required|uuid',
                'tanggal_transaksi' => 'required|date|date_format:Y-m-d', // Pastikan format tanggal
                'jenis_transaksi_id' => 'required|uuid',
                'jenis_keuangan_tefa_id' => 'required|uuid',
                'nama_transaksi' => 'required|string|max:255',
                'deskripsi_transaksi' => 'nullable|string',
                'nominal' => 'required',
                'bukti_transaksi' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,ppt,pptx,xls,xlsx|max:10240',
                'subkategori_pengeluaran_id' => 'nullable|uuid',
            ];
            
            // Only add exists validation if subkategori is provided
            if ($request->filled('subkategori_pengeluaran_id')) {
                $validationRules['subkategori_pengeluaran_id'] = 'nullable|uuid|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id';
            }
            
            $validator = Validator::make($request->all(), $validationRules);
            
            if ($validator->fails()) {
                Log::warning('Validation failed for tambah transaksi pengeluaran', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->except(['bukti_transaksi'])
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $transaksiId = Str::uuid()->toString();
            
            // Process nominal (remove formatting)
            $nominal = str_replace(['.',','], ['','.'], $request->nominal);
            
            // Prepare data - handle null subkategori gracefully
            $subkategoriId = $request->filled('subkategori_pengeluaran_id') ? 
                            $request->input('subkategori_pengeluaran_id') : null;
            
            // Format tanggal untuk database - pastikan format DATE (YYYY-MM-DD)
            $tanggalTransaksi = $request->tanggal_transaksi;
            if ($tanggalTransaksi) {
                try {
                    $dateObj = new \DateTime($tanggalTransaksi);
                    $tanggalTransaksi = $dateObj->format('Y-m-d'); // Simpan hanya tanggal, bukan datetime
                } catch (\Exception $e) {
                    Log::error('Invalid date format: ' . $tanggalTransaksi);
                    throw new \Exception('Format tanggal tidak valid');
                }
            }
            
            $data = [
                'keuangan_tefa_id' => $transaksiId,
                'proyek_id' => $request->proyek_id,
                'tanggal_transaksi' => $tanggalTransaksi, // Simpan sebagai DATE, bukan DATETIME
                'jenis_transaksi_id' => $request->jenis_transaksi_id,
                'jenis_keuangan_tefa_id' => $request->jenis_keuangan_tefa_id,
                'nama_transaksi' => $request->nama_transaksi,
                'deskripsi_transaksi' => $request->deskripsi_transaksi,
                'nominal_transaksi' => $nominal,
                'sub_jenis_transaksi_id' => $subkategoriId, // This can be null
                'created_at' => now(),
                'created_by' => auth()->user()->id ?? session('user_id'),
            ];
            
            Log::info('Data to be inserted:', [
                'keuangan_tefa_id' => $transaksiId,
                'tanggal_transaksi' => $tanggalTransaksi,
                'sub_jenis_transaksi_id' => $subkategoriId,
                'has_subkategori' => !is_null($subkategoriId)
            ]);
            
            // Handle file upload
            if ($request->hasFile('bukti_transaksi')) {
                $file = $request->file('bukti_transaksi');
                
                if ($file->isValid()) {
                    $uploadPath = public_path('uploads/keuangan_tefa');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    $filename = 'bukti_transaksi_' . $transaksiId . '_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    if ($file->move($uploadPath, $filename)) {
                        $data['bukti_transaksi'] = 'uploads/keuangan_tefa/' . $filename;
                    }
                }
            }
            
            DB::table('t_keuangan_tefa')->insert($data);
            
            DB::commit();
            
            Log::info('Transaction successfully inserted', [
                'keuangan_tefa_id' => $transaksiId,
                'proyek_id' => $request->proyek_id,
                'tanggal_saved' => $tanggalTransaksi,
                'has_subkategori' => !is_null($subkategoriId)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil ditambahkan'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding income transaction: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['bukti_transaksi'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function storeWithFiles(Request $request)
    {
        try {
            Log::info('Store with files request received', [
                'has_file_main' => $request->hasFile('bukti_transaksi'),
                'data_length' => $request->input('pengeluaran_data') ? strlen($request->input('pengeluaran_data')) : 0,
                'request_keys' => array_keys($request->all())
            ]);
            
            // Get the IDs for "Pengeluaran" and "Proyek"
            $pengeluaranId = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->value('jenis_transaksi_id');
                
            $proyekId = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->value('jenis_keuangan_tefa_id');
            
            $jsonData = $request->input('pengeluaran_data');
            
            if (empty($jsonData)) {
                Log::error('No pengeluaran_data received in request');
                throw new \Exception('Tidak ada data pengeluaran untuk disimpan');
            }
            
            $pengeluaranData = json_decode($jsonData, true);
            
            if (empty($pengeluaranData)) {
                Log::error('Failed to decode JSON or empty array', ['json_data' => $jsonData]);
                throw new \Exception('Data pengeluaran tidak valid atau kosong');
            }
            
            Log::info('Multiple pengeluaran data to process:', [
                'count' => count($pengeluaranData),
                'sample_item' => isset($pengeluaranData[0]) ? [
                    'has_subkategori' => !empty($pengeluaranData[0]['subkategori_pengeluaran_id']),
                    'subkategori_id' => $pengeluaranData[0]['subkategori_pengeluaran_id'] ?? 'null'
                ] : null
            ]);
            
            // Sort the items by sequence to maintain the order they were added in the form
            usort($pengeluaranData, function($a, $b) {
                return ($a['sequence'] ?? 0) - ($b['sequence'] ?? 0);
            });
            
            // Array to store processed records
            $processedRecords = [];
            $currentTimestamp = now();
            
            DB::beginTransaction();
            
            // Process each item with its own file - in sequence order
            foreach ($pengeluaranData as $item) {
                $itemId = $item['id'];
                $nominal = str_replace('.', '', $item['nominal']);
                $filePath = null;

                // Check if there's a specific file for this item
                $fileKey = "bukti_transaksi_{$itemId}";
                
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    Log::info("Processing file for item {$itemId}", [
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
                            Log::info("File for item {$itemId} saved to {$filePath}");
                        } else {
                            Log::error("Failed to move file for item {$itemId}");
                        }
                    } else {
                        Log::error("Invalid file for item {$itemId}");
                    }
                } else {
                    Log::info("No file found for item {$itemId}");
                }
                
                // Generate UUID for database record
                $keuanganTefaId = Str::uuid()->toString();
                
                // Prepare data for database insertion
                $createdAt = (clone $currentTimestamp)->addSeconds(isset($item['sequence']) ? $item['sequence'] : 0);
                
                // Handle subkategori - can be null
                $subkategoriId = !empty($item['subkategori_pengeluaran_id']) ? 
                                $item['subkategori_pengeluaran_id'] : null;
                
                $data = [
                    'keuangan_tefa_id' => $keuanganTefaId,
                    'proyek_id' => $request->input('proyek_id'),
                    'tanggal_transaksi' => date('Y-m-d', strtotime($item['tanggal_transaksi'])), // Format tanggal
                    'jenis_transaksi_id' => $pengeluaranId,
                    'jenis_keuangan_tefa_id' => $proyekId,
                    'nama_transaksi' => $item['nama_transaksi'],
                    'nominal_transaksi' => $nominal,
                    'deskripsi_transaksi' => $item['deskripsi_transaksi'] ?? null,
                    'bukti_transaksi' => $filePath,
                    'sub_jenis_transaksi_id' => $subkategoriId,
                    'created_at' => $createdAt,
                    'created_by' => auth()->id() ?? session('user_id') ?? 1,
                    'entry_sequence' => isset($item['sequence']) ? $item['sequence'] : null,
                ];
                
                Log::info("Preparing to insert record for item {$itemId}", [
                    'keuangan_tefa_id' => $keuanganTefaId,
                    'has_subkategori' => !is_null($subkategoriId),
                    'subkategori_id' => $subkategoriId
                ]);
                
                // Insert into database
                DB::table('t_keuangan_tefa')->insert($data);
                
                $processedRecords[] = $keuanganTefaId;
                Log::info("Record inserted for item {$itemId} with keuangan_tefa_id {$keuanganTefaId}");
            }
            
            DB::commit();
            
            // Fetch the inserted records
            $results = DB::table('t_keuangan_tefa')
                ->whereIn('keuangan_tefa_id', $processedRecords)
                ->get();
            
            Log::info('Successfully stored multiple pengeluaran records', [
                'total_records' => count($results),
                'records_with_subkategori' => $results->whereNotNull('sub_jenis_transaksi_id')->count(),
                'records_without_subkategori' => $results->whereNull('sub_jenis_transaksi_id')->count()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data pengeluaran berhasil disimpan',
                'data' => $results,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pengeluaran with files: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransaksiDetail(Request $request, $transaksiId)
    {
        try {
            // Get transaction details with all related data
            $transaksi = DB::table('t_keuangan_tefa')
                ->select(
                    't_keuangan_tefa.keuangan_tefa_id',
                    't_keuangan_tefa.proyek_id',
                    't_keuangan_tefa.tanggal_transaksi',
                    't_keuangan_tefa.jenis_transaksi_id',
                    't_keuangan_tefa.jenis_keuangan_tefa_id',
                    't_keuangan_tefa.nama_transaksi',
                    't_keuangan_tefa.deskripsi_transaksi',
                    't_keuangan_tefa.nominal_transaksi',
                    't_keuangan_tefa.bukti_transaksi',
                    't_keuangan_tefa.sub_jenis_transaksi_id',
                    'm_jenis_transaksi.nama_jenis_transaksi',
                    'm_jenis_keuangan_tefa.nama_jenis_keuangan_tefa',
                    'm_sub_jenis_transaksi.nama_sub_jenis_transaksi',
                    'm_proyek.nama_proyek'
                )
                ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
                ->join('m_jenis_keuangan_tefa', 't_keuangan_tefa.jenis_keuangan_tefa_id', '=', 'm_jenis_keuangan_tefa.jenis_keuangan_tefa_id')
                ->join('m_proyek', 't_keuangan_tefa.proyek_id', '=', 'm_proyek.proyek_id')
                ->leftJoin('m_sub_jenis_transaksi', 't_keuangan_tefa.sub_jenis_transaksi_id', '=', 'm_sub_jenis_transaksi.sub_jenis_transaksi_id')
                ->where('t_keuangan_tefa.keuangan_tefa_id', $transaksiId)
                ->whereNull('t_keuangan_tefa.deleted_at')
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 404);
            }

            // Format tanggal untuk input date HTML (YYYY-MM-DD)
            $tanggalFormatted = date('Y-m-d', strtotime($transaksi->tanggal_transaksi));

            // Format the data for frontend
            $formattedData = [
                'keuangan_tefa_id' => $transaksi->keuangan_tefa_id,
                'proyek_id' => $transaksi->proyek_id,
                'nama_proyek' => $transaksi->nama_proyek,
                'tanggal_transaksi' => $tanggalFormatted, // Format yang benar untuk input date
                'jenis_transaksi_id' => $transaksi->jenis_transaksi_id,
                'jenis_keuangan_tefa_id' => $transaksi->jenis_keuangan_tefa_id,
                'nama_transaksi' => $transaksi->nama_transaksi,
                'deskripsi_transaksi' => $transaksi->deskripsi_transaksi,
                'nominal_transaksi' => number_format($transaksi->nominal_transaksi, 0, ',', '.'),
                'nominal_raw' => $transaksi->nominal_transaksi,
                'bukti_transaksi' => $transaksi->bukti_transaksi,
                'sub_jenis_transaksi_id' => $transaksi->sub_jenis_transaksi_id,
                'nama_sub_jenis_transaksi' => $transaksi->nama_sub_jenis_transaksi,
                'has_file' => !empty($transaksi->bukti_transaksi),
                'file_name' => $transaksi->bukti_transaksi ? basename($transaksi->bukti_transaksi) : null,
                'file_url' => $transaksi->bukti_transaksi ? asset($transaksi->bukti_transaksi) : null
            ];

            Log::info('Transaction detail retrieved successfully', [
                'transaksi_id' => $transaksiId,
                'tanggal_formatted' => $tanggalFormatted,
                'has_subkategori' => !empty($transaksi->sub_jenis_transaksi_id)
            ]);

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction detail: ' . $e->getMessage(), [
                'transaksi_id' => $transaksiId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data transaksi'
            ], 500);
        }
    }

    // ✅ KOREKSI: Update method dengan logic conditional required yang benar
    public function updateTransaksiPengeluaran(Request $request, $transaksiId)
    {
        try {
            Log::info('Update transaksi pengeluaran request:', [
                'transaksi_id' => $transaksiId,
                'tanggal_transaksi' => $request->input('edit_tanggal_transaksi'),
                'has_file' => $request->hasFile('edit_file_keuangan_tefa'),
                'has_subkategori' => $request->filled('edit_sub_jenis_transaksi_id'),
                'subkategori_value' => $request->input('edit_sub_jenis_transaksi_id'),
            ]);

            // Get the default IDs
            $pengeluaranRecord = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->whereNull('deleted_at')
                ->first();
                
            $proyekRecord = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->whereNull('deleted_at')
                ->first();

            if (!$pengeluaranRecord || !$proyekRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data referensi tidak ditemukan dalam sistem'
                ], 500);
            }

            $pengeluaranId = $pengeluaranRecord->jenis_transaksi_id;
            $proyekId = $proyekRecord->jenis_keuangan_tefa_id;

            // Force values to ensure consistency
            $request->merge([
                'edit_jenis_transaksi_id' => $pengeluaranId,
                'edit_jenis_keuangan_tefa_id' => $proyekId
            ]);

            // ✅ KOREKSI: Check if subkategori is required based on data availability
            $isSubkategoriRequired = $this->isSubkategoriRequiredForPengeluaran();

            // Enhanced validation rules
            $validationRules = [
                'edit_tanggal_transaksi' => 'required|date|date_format:Y-m-d',
                'edit_nama_transaksi' => 'required|string|max:255',
                'edit_deskripsi_transaksi' => 'nullable|string',
                'edit_nominal' => 'required',
                'edit_file_keuangan_tefa' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,ppt,pptx,xls,xlsx|max:10240',
            ];

            // ✅ KOREKSI: Jika subkategori tersedia di database, maka WAJIB diisi
            if ($isSubkategoriRequired) {
                $validationRules['edit_sub_jenis_transaksi_id'] = 'required|uuid|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id';
            } else {
                $validationRules['edit_sub_jenis_transaksi_id'] = 'nullable|uuid';
                if ($request->filled('edit_sub_jenis_transaksi_id')) {
                    $validationRules['edit_sub_jenis_transaksi_id'] = 'nullable|uuid|exists:m_sub_jenis_transaksi,sub_jenis_transaksi_id';
                }
            }

            $validator = Validator::make($request->all(), $validationRules, [
                'edit_sub_jenis_transaksi_id.required' => 'Kategori Pengeluaran wajib dipilih karena tersedia dalam sistem.',
                'edit_sub_jenis_transaksi_id.exists' => 'Kategori Pengeluaran yang dipilih tidak valid.',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for update transaksi pengeluaran', [
                    'errors' => $validator->errors()->toArray(),
                    'transaksi_id' => $transaksiId,
                    'subkategori_required' => $isSubkategoriRequired
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if transaction exists
            $existingTransaction = DB::table('t_keuangan_tefa')
                ->where('keuangan_tefa_id', $transaksiId)
                ->whereNull('deleted_at')
                ->first();

            if (!$existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Process nominal (remove formatting)
            $nominal = str_replace(['.',','], ['','.'], $request->edit_nominal);

            // ✅ KOREKSI: Handle subkategori sesuai ketersediaan dan requirement
            $subkategoriId = null;
            if ($isSubkategoriRequired) {
                $subkategoriId = $request->input('edit_sub_jenis_transaksi_id');
            } else {
                $subkategoriId = $request->filled('edit_sub_jenis_transaksi_id') ? 
                                $request->input('edit_sub_jenis_transaksi_id') : null;
            }

            // Format tanggal untuk database
            $tanggalTransaksi = $request->edit_tanggal_transaksi;
            if ($tanggalTransaksi) {
                try {
                    $dateObj = new \DateTime($tanggalTransaksi);
                    $tanggalTransaksi = $dateObj->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::error('Invalid date format: ' . $tanggalTransaksi);
                    throw new \Exception('Format tanggal tidak valid');
                }
            }

            // Prepare update data
            $updateData = [
                'tanggal_transaksi' => $tanggalTransaksi,
                'jenis_transaksi_id' => $pengeluaranId,
                'jenis_keuangan_tefa_id' => $proyekId,
                'nama_transaksi' => $request->edit_nama_transaksi,
                'deskripsi_transaksi' => $request->edit_deskripsi_transaksi,
                'nominal_transaksi' => $nominal,
                'sub_jenis_transaksi_id' => $subkategoriId,
                'updated_at' => now(),
                'updated_by' => auth()->user()->id ?? session('user_id'),
            ];

            // Handle file upload if new file is provided
            if ($request->hasFile('edit_file_keuangan_tefa')) {
                $file = $request->file('edit_file_keuangan_tefa');

                if ($file->isValid()) {
                    // Delete old file if exists
                    if ($existingTransaction->bukti_transaksi) {
                        $oldFilePath = public_path($existingTransaction->bukti_transaksi);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                            Log::info('Old file deleted: ' . $oldFilePath);
                        }
                    }

                    // Upload new file
                    $uploadPath = public_path('uploads/keuangan_tefa');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $filename = 'bukti_transaksi_' . $transaksiId . '_' . time() . '.' . $file->getClientOriginalExtension();

                    if ($file->move($uploadPath, $filename)) {
                        $updateData['bukti_transaksi'] = 'uploads/keuangan_tefa/' . $filename;
                        Log::info('New file uploaded: ' . $filename);
                    }
                }
            }

            // Update the transaction
            DB::table('t_keuangan_tefa')
                ->where('keuangan_tefa_id', $transaksiId)
                ->update($updateData);

            DB::commit();

            Log::info('Transaction successfully updated', [
                'keuangan_tefa_id' => $transaksiId,
                'tanggal_updated' => $tanggalTransaksi,
                'has_subkategori' => !is_null($subkategoriId),
                'subkategori_required' => $isSubkategoriRequired,
                'file_updated' => $request->hasFile('edit_file_keuangan_tefa')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating income transaction: ' . $e->getMessage(), [
                'transaksi_id' => $transaksiId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ KOREKSI: Method untuk mengecek apakah subkategori wajib - kembali ke logic asli
    private function isSubkategoriRequiredForPengeluaran()
    {
        try {
            // Get "Pengeluaran" transaction type ID
            $jenisTransaksiId = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->value('jenis_transaksi_id');
            
            // Get "Proyek" financial category ID
            $jenisKeuanganId = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->value('jenis_keuangan_tefa_id');
            
            if (!$jenisTransaksiId || !$jenisKeuanganId) {
                return false;
            }
            
            // ✅ KOREKSI: Check if subcategories exist - jika ada data maka REQUIRED
            $count = DB::table('m_sub_jenis_transaksi')
                ->where('jenis_transaksi_id', $jenisTransaksiId)
                ->where('jenis_keuangan_tefa_id', $jenisKeuanganId)
                ->whereNull('deleted_at')
                ->count();
            
            // Jika ada data subkategori di database, maka WAJIB diisi
            return $count > 0; 
            
        } catch (\Exception $e) {
            Log::error('Error checking if subkategori required: ' . $e->getMessage());
            return false;
        }
    }

    public function getDataTransaksiProyek(Request $request, $proyekId)
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 5);
        
        // ✅ TAMBAHAN: Ambil parameter filter
        $filters = [
            'tanggal_mulai' => $request->get('tanggal_mulai'),
            'tanggal_akhir' => $request->get('tanggal_akhir'),
            'nama_transaksi' => $request->get('nama_transaksi'),
            'kategori_pengeluaran' => $request->get('kategori_pengeluaran'),
            'nominal_min' => $request->get('nominal_min'),
            'nominal_max' => $request->get('nominal_max'),
            'status_bukti' => $request->get('status_bukti')
        ];
        
        // Get financial transactions for this project
        $query = DB::table('t_keuangan_tefa')
            ->select(
                't_keuangan_tefa.keuangan_tefa_id',
                't_keuangan_tefa.tanggal_transaksi',
                't_keuangan_tefa.nama_transaksi',
                't_keuangan_tefa.deskripsi_transaksi',
                't_keuangan_tefa.nominal_transaksi',
                't_keuangan_tefa.bukti_transaksi',
                't_keuangan_tefa.sub_jenis_transaksi_id',
                'm_jenis_transaksi.nama_jenis_transaksi',
                'm_jenis_keuangan_tefa.nama_jenis_keuangan_tefa',
                'm_sub_jenis_transaksi.nama_sub_jenis_transaksi'
            )
            ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
            ->join('m_jenis_keuangan_tefa', 't_keuangan_tefa.jenis_keuangan_tefa_id', '=', 'm_jenis_keuangan_tefa.jenis_keuangan_tefa_id')
            ->leftJoin('m_sub_jenis_transaksi', 't_keuangan_tefa.sub_jenis_transaksi_id', '=', 'm_sub_jenis_transaksi.sub_jenis_transaksi_id')
            ->where('t_keuangan_tefa.proyek_id', $proyekId)
            ->where('m_jenis_transaksi.nama_jenis_transaksi', 'Pengeluaran')
            ->whereNull('t_keuangan_tefa.deleted_at');
        
        // ✅ TAMBAHAN: Apply filters
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('t_keuangan_tefa.tanggal_transaksi', '>=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_akhir'])) {
            $query->whereDate('t_keuangan_tefa.tanggal_transaksi', '<=', $filters['tanggal_akhir']);
        }
        
        if (!empty($filters['nama_transaksi'])) {
            $query->where('t_keuangan_tefa.nama_transaksi', 'like', '%' . $filters['nama_transaksi'] . '%');
        }
        
        if (!empty($filters['kategori_pengeluaran'])) {
            $query->where('t_keuangan_tefa.sub_jenis_transaksi_id', $filters['kategori_pengeluaran']);
        }
        
        if (!empty($filters['nominal_min'])) {
            $nominalMin = str_replace(['.', ','], ['', '.'], $filters['nominal_min']);
            $query->where('t_keuangan_tefa.nominal_transaksi', '>=', $nominalMin);
        }
        
        if (!empty($filters['nominal_max'])) {
            $nominalMax = str_replace(['.', ','], ['', '.'], $filters['nominal_max']);
            $query->where('t_keuangan_tefa.nominal_transaksi', '<=', $nominalMax);
        }
        
        if (!empty($filters['status_bukti'])) {
            if ($filters['status_bukti'] === 'ada') {
                $query->whereNotNull('t_keuangan_tefa.bukti_transaksi');
            } elseif ($filters['status_bukti'] === 'tidak_ada') {
                $query->whereNull('t_keuangan_tefa.bukti_transaksi');
            }
        }
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t_keuangan_tefa.nama_transaksi', 'like', "%{$search}%")
                ->orWhere('t_keuangan_tefa.deskripsi_transaksi', 'like', "%{$search}%")
                ->orWhere('m_jenis_keuangan_tefa.nama_jenis_keuangan_tefa', 'like', "%{$search}%")
                ->orWhere('m_sub_jenis_transaksi.nama_sub_jenis_transaksi', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $transaksi = $query->orderBy('t_keuangan_tefa.tanggal_transaksi', 'desc')
                        ->paginate($perPage);
        
        // Format the data for display
        $formattedTransaksi = $transaksi->getCollection()->map(function($item) {
            return [
                'id' => $item->keuangan_tefa_id,
                'tanggal' => date('d/m/Y', strtotime($item->tanggal_transaksi)),
                'keterangan' => $item->nama_transaksi,
                'kategori' => $item->nama_sub_jenis_transaksi ?? '-',
                'nominal' => number_format($item->nominal_transaksi, 0, ',', '.'),
                'bukti' => $item->bukti_transaksi ? 
                    '<a href="/' . $item->bukti_transaksi . '" class="btn btn-action-download" target="_blank">
                        <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="6.2425" cy="6.32" rx="6.2425" ry="6.32" transform="matrix(4.42541e-08 -1 -1 -4.31754e-08 18.96 20.8086)" fill="#E4F8EB"/>
                            <path d="M10.0067 13.0054L12.64 15.6064M12.64 15.6064L15.2733 13.0054M12.64 15.6064L12.64 5.20228" stroke="#00BC39" stroke-linecap="round"/>
                        </svg>
                    </a>' : 
                    '<span class="text-muted">-</span>',
            ];
        });
        
        // Return response with pagination data
        return response()->json([
            'data' => $formattedTransaksi,
            'pagination' => [
                'current_page' => $transaksi->currentPage(),
                'last_page' => $transaksi->lastPage(),
                'per_page' => $transaksi->perPage(),
                'total' => $transaksi->total(),
                'from' => $transaksi->firstItem(),
                'to' => $transaksi->lastItem(),
                'has_more_pages' => $transaksi->hasMorePages(),
                'prev_page_url' => $transaksi->previousPageUrl(),
                'next_page_url' => $transaksi->nextPageUrl(),
            ],
            'from' => $transaksi->firstItem() ?: 0,
            'to' => $transaksi->lastItem() ?: 0,
            'total' => $transaksi->total(),
            'search' => $search,
            'filters' => $filters
        ]);
    }

    public function getKategoriPengeluaranForFilter(Request $request)
    {
        try {
            // Get "Pengeluaran" transaction type ID
            $jenisTransaksiId = DB::table('m_jenis_transaksi')
                ->where('nama_jenis_transaksi', 'Pengeluaran')
                ->value('jenis_transaksi_id');
            
            // Get "Proyek" financial category ID  
            $jenisKeuanganId = DB::table('m_jenis_keuangan_tefa')
                ->where('nama_jenis_keuangan_tefa', 'Proyek')
                ->value('jenis_keuangan_tefa_id');
            
            if (!$jenisTransaksiId || !$jenisKeuanganId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data referensi tidak ditemukan',
                    'results' => []
                ]);
            }
            
            $subkategori = DB::table('m_sub_jenis_transaksi')
                ->select(
                    'm_sub_jenis_transaksi.sub_jenis_transaksi_id as id',
                    'm_sub_jenis_transaksi.nama_sub_jenis_transaksi as text'
                )
                ->where('m_sub_jenis_transaksi.jenis_transaksi_id', $jenisTransaksiId)
                ->where('m_sub_jenis_transaksi.jenis_keuangan_tefa_id', $jenisKeuanganId)
                ->whereNull('m_sub_jenis_transaksi.deleted_at')
                ->orderBy('m_sub_jenis_transaksi.nama_sub_jenis_transaksi', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'results' => $subkategori,
                'count' => $subkategori->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'results' => []
            ], 500);
        }
    }

    public function getSummary(Request $request, $proyekId = null)
    {
        try {
            if (!$proyekId) {
                $proyekId = $request->input('proyek_id');
            }
            
            if (!$proyekId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proyek ID tidak ditemukan'
                ], 400);
            }

            $proyek = DB::table('m_proyek')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->first();

            if (!$proyek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proyek tidak ditemukan'
                ], 404);
            }

            $totalPengeluaran = DB::table('t_keuangan_tefa as kt')
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->where('kt.proyek_id', $proyekId)
                ->where('jt.nama_jenis_transaksi', 'Pengeluaran')
                ->whereNull('kt.deleted_at')
                ->sum('kt.nominal_transaksi');

            $pengeluaranByKategori = DB::table('t_keuangan_tefa as kt')
                ->select(
                    'msj.nama_sub_jenis_transaksi as kategori',
                    DB::raw('SUM(kt.nominal_transaksi) as total'),
                    DB::raw('COUNT(kt.keuangan_tefa_id) as jumlah_transaksi')
                )
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->leftJoin('m_sub_jenis_transaksi as msj', 'kt.sub_jenis_transaksi_id', '=', 'msj.sub_jenis_transaksi_id')
                ->where('kt.proyek_id', $proyekId)
                ->where('jt.nama_jenis_transaksi', 'Pengeluaran')
                ->whereNull('kt.deleted_at')
                ->groupBy('msj.sub_jenis_transaksi_id', 'msj.nama_sub_jenis_transaksi')
                ->orderBy('total', 'desc')
                ->get();

            $recentTransactions = DB::table('t_keuangan_tefa as kt')
                ->select(
                    'kt.keuangan_tefa_id',
                    'kt.tanggal_transaksi',
                    'kt.nama_transaksi',
                    'kt.nominal_transaksi',
                    'jt.nama_jenis_transaksi',
                    'msj.nama_sub_jenis_transaksi'
                )
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->leftJoin('m_sub_jenis_transaksi as msj', 'kt.sub_jenis_transaksi_id', '=', 'msj.sub_jenis_transaksi_id')
                ->where('kt.proyek_id', $proyekId)
                ->where('jt.nama_jenis_transaksi', 'Pengeluaran')
                ->whereNull('kt.deleted_at')
                ->orderBy('kt.created_at', 'desc')
                ->limit(5)
                ->get();

            Log::info('Summary data retrieved successfully', [
                'proyek_id' => $proyekId,
                'total_pengeluaran' => $totalPengeluaran,
                'kategori_count' => $pengeluaranByKategori->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'proyek_id' => $proyekId,
                    'total_pengeluaran' => $totalPengeluaran ?: 0,
                    'pengeluaran_by_kategori' => $pengeluaranByKategori,
                    'recent_transactions' => $recentTransactions,
                    'transaksi_count' => $recentTransactions->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting summary: ' . $e->getMessage(), [
                'proyek_id' => $proyekId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan ringkasan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
