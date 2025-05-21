<?php

namespace App\Http\Controllers\Koordinator\DataKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DataMasukKeuanganProyekController extends Controller
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
        return view('pages.Koordinator.DataKeuanganProyek.table_proyek_dana_masuk', [
            'proyek' => $proyek,
            'search' => $search,
            'titleSidebar' => 'Data Pemasukan Keuangan Proyek'
        ]);
    }
    
    public function getDataMasukKeuanganProyek(Request $request, $proyekId)
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
            return redirect()->route('koordinator.dataMasukKeuanganProyek')
                ->with('error', 'Data proyek tidak ditemukan');
        }
        
        // Get transaction types for dropdown in add transaction modal
        $jenisTransaksi = DB::table('m_jenis_transaksi')
            ->where('nama_jenis_transaksi', 'Pemasukan')  // Assuming there's a column to filter income transaction types
            ->whereNull('deleted_at')
            ->get();
        
        // Get financial categories for dropdown in add transaction modal
        $jenisKeuanganTefa = DB::table('m_jenis_keuangan_tefa')
            ->whereNull('deleted_at')
            ->get();
        
        // Get total income for this project
        $totalPemasukan = DB::table('t_keuangan_tefa')
            ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
            ->where('t_keuangan_tefa.proyek_id', $proyekId)
            ->where('m_jenis_transaksi.nama_jenis_transaksi', 'Pemasukan')
            ->whereNull('t_keuangan_tefa.deleted_at')
            ->sum('t_keuangan_tefa.nominal_transaksi');
        
        return view('pages.Koordinator.DataKeuanganProyek.kelola_dana_masuk_proyek', [
            'proyek' => $proyek,
            'jenisTransaksi' => $jenisTransaksi,
            'jenisKeuanganTefa' => $jenisKeuanganTefa,
            'totalPemasukan' => $totalPemasukan,
            'titleSidebar' => 'Detail Pemasukan Keuangan Proyek'
        ]);
    }
    
    public function getDataTransaksiProyek(Request $request, $proyekId)
    {
        $search = $request->get('search');
        
        // Get financial transactions for this project
        $query = DB::table('t_keuangan_tefa')
            ->select(
                't_keuangan_tefa.keuangan_tefa_id',
                't_keuangan_tefa.tanggal_transaksi',
                't_keuangan_tefa.nama_transaksi',
                't_keuangan_tefa.deskripsi_transaksi',
                't_keuangan_tefa.nominal_transaksi',
                't_keuangan_tefa.bukti_transaksi',
                'm_jenis_transaksi.nama_jenis_transaksi',
                'm_jenis_keuangan_tefa.nama_jenis_keuangan_tefa'
            )
            ->join('m_jenis_transaksi', 't_keuangan_tefa.jenis_transaksi_id', '=', 'm_jenis_transaksi.jenis_transaksi_id')
            ->join('m_jenis_keuangan_tefa', 't_keuangan_tefa.jenis_keuangan_tefa_id', '=', 'm_jenis_keuangan_tefa.jenis_keuangan_tefa_id')
            ->where('t_keuangan_tefa.proyek_id', $proyekId)
            ->where('m_jenis_transaksi.nama_jenis_transaksi', 'Pemasukan')  // Filter for income only
            ->whereNull('t_keuangan_tefa.deleted_at');
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t_keuangan_tefa.nama_transaksi', 'like', "%{$search}%")
                ->orWhere('t_keuangan_tefa.deskripsi_transaksi', 'like', "%{$search}%")
                ->orWhere('m_jenis_keuangan_tefa.nama_jenis_keuangan_tefa', 'like', "%{$search}%");
            });
        }
        
        $transaksi = $query->orderBy('t_keuangan_tefa.tanggal_transaksi', 'desc')->get();
        
        // Format the data for display
        $formattedTransaksi = $transaksi->map(function($item) {
            return [
                'id' => $item->keuangan_tefa_id,
                'tanggal' => date('d-m-Y', strtotime($item->tanggal_transaksi)),
                'keterangan' => $item->nama_transaksi,
                'nominal' => number_format($item->nominal_transaksi, 0, ',', '.'),
                'bukti' => $item->bukti_transaksi ? 
                    '<a href="'.route('koordinator.downloadBuktiTransaksi', $item->bukti_transaksi).'" class="btn btn-sm btn-info" target="_blank">
                        <i class="bi bi-download"></i> Lihat
                    </a>' : 
                    '<span class="text-muted">-</span>',
                'aksi' => '<button type="button" class="btn btn-sm btn-danger delete-transaction" data-id="'.$item->keuangan_tefa_id.'">
                    <i class="bi bi-trash"></i>
                </button>'
            ];
        });
        
        return response()->json([
            'data' => $formattedTransaksi
        ]);
    }
    
    public function tambahTransaksiPemasukan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proyek_id' => 'required|uuid',
            'tanggal_transaksi' => 'required|date',
            'jenis_transaksi_id' => 'required|uuid',
            'jenis_keuangan_tefa_id' => 'required|uuid',
            'nama_transaksi' => 'required|string|max:255',
            'deskripsi_transaksi' => 'nullable|string',
            'nominal' => 'required',
            'bukti_transaksi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $transaksiId = Str::uuid()->toString();
            
            // Process nominal (remove formatting)
            $nominal = str_replace(['.',','], ['','.'], $request->nominal);
            
            $data = [
                'transaksi_id' => $transaksiId,
                'proyek_id' => $request->proyek_id,
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'jenis_transaksi_id' => $request->jenis_transaksi_id,
                'jenis_keuangan_tefa_id' => $request->jenis_keuangan_tefa_id,
                'nama_transaksi' => $request->nama_transaksi,
                'deskripsi_transaksi' => $request->deskripsi_transaksi,
                'nominal' => $nominal,
                'created_at' => now(),
                'created_by' => auth()->user()->id ?? session('user_id'),
            ];
            
            // Handle file upload
            if ($request->hasFile('bukti_transaksi')) {
                $file = $request->file('bukti_transaksi');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Store file in storage/app/public/bukti_transaksi
                $path = $file->storeAs('bukti_transaksi', $fileName, 'public');
                
                $data['bukti_transaksi'] = $fileName;
            }
            
            DB::table('t_keuangan_tefa')->insert($data);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding income transaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi: ' . $e->getMessage()
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
        $path = storage_path('app/public/bukti_transaksi/' . $fileName);
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()->with('error', 'File tidak ditemukan');
    }
}