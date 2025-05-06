<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProyekController extends Controller
{
    public function getDataProyek(Request $request){
        $jenisProyek = DB::table('m_jenis_proyek')->whereNull('deleted_at')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->whereNull('deleted_at')->get();
        $dataDosen = DB::table('d_dosen')->whereNull('deleted_at')->get();
        $dataProfesional = DB::table('d_profesional')->whereNull('deleted_at')->get();
        
        // Gunakan Query Builder untuk membuat query dasar
        $query = DB::table('m_proyek')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.status_proyek',
                't_project_leader.leader_type',
                DB::raw('CASE 
                    WHEN t_project_leader.leader_type = "Dosen" THEN d_dosen.nama_dosen
                    WHEN t_project_leader.leader_type = "Profesional" THEN d_profesional.nama_profesional
                    ELSE NULL
                END as nama_project_leader')
            )
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
            ->leftJoin('d_dosen', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_dosen.dosen_id')
                    ->where('t_project_leader.leader_type', '=', 'Dosen');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_leader.leader_type', '=', 'Profesional');
            });
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'like', "%{$search}%")
                  ->orWhere('d_mitra_proyek.nama_mitra', 'like', "%{$search}%");
            });
        }
        
        // Gunakan paginate untuk menghasilkan objek paginasi yang benar
        $proyek = $query->orderBy('m_proyek.created_at', 'desc')->paginate(10);
        
        // Tambahkan search parameter ke variable untuk ditampilkan di view
        $search = $request->search;
        
        return view('pages.Koordinator.DataProyek.table_data_proyek', compact(
            'proyek',
            'search',
            'jenisProyek',
            'daftarMitra',
            'dataDosen',
            'dataProfesional'
        ), [
            'titleSidebar' => 'Data Proyek',
        ]);
    }

    public function tambahDataProyek(Request $request){
        $request->validate([
            'mitra_id'          => 'required|uuid',
            'jenis_proyek'      => 'required|uuid',
            'nama_proyek'       => 'required|string|max:255',
            'status_proyek'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'dana_pendanaan'    => 'required', 
            'leader_type'       => 'required|in:Dosen,Profesional', 
            'leader_id'         => 'required|uuid',
            'deskripsi'         => 'nullable|string|max:1000', // Pastikan sama dengan nama field di form
        ]);
    
        // Format dana pendanaan
        $danaPendanaan = $request->input('dana_pendanaan');
        // Hapus karakter non-numerik
        $danaPendanaan = preg_replace('/\D/', '', $danaPendanaan);
    
        // Buat UUID
        $proyek_id = Str::uuid()->toString();
        $project_leader_id = Str::uuid()->toString();
    
        DB::beginTransaction();
    
        try {
            DB::table('m_proyek')->insert([
                'proyek_id'        => $proyek_id,
                'mitra_proyek_id'  => $request->input('mitra_id'),
                'jenis_proyek_id'  => $request->input('jenis_proyek'),
                'nama_proyek'      => $request->input('nama_proyek'),
                'deskripsi_proyek' => $request->input('deskripsi') ?? null, // Pastikan ini sama dengan nama field di form
                'status_proyek'    => $request->input('status_proyek'),
                'tanggal_mulai'    => $request->input('tanggal_mulai'),
                'tanggal_selesai'  => $request->input('tanggal_selesai'),
                'dana_pendanaan'   => $danaPendanaan,
                'created_at'       => now(),
                'created_by'       => auth()->user()->id ?? session('user_id'),
            ]);
    
            // Simpan ke tabel t_project_leader with polymorphic relationship
            DB::table('t_project_leader')->insert([
                'project_leader_id' => $project_leader_id,
                'proyek_id'         => $proyek_id,
                'leader_type'       => $request->input('leader_type'),
                'leader_id'         => $request->input('leader_id'),
                'created_at'        => now(),
                'created_by'        => auth()->user()->id ?? session('user_id'),
            ]);
    
            DB::commit();
    
            return redirect()->back()->with('success', 'Data proyek berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan proyek: ' . $e->getMessage());
        }
    }

    public function updateDataProyek(Request $request, $proyekId){
        $request->validate([
            'mitra_id'          => 'required|uuid',
            'jenis_proyek'      => 'required|uuid',
            'nama_proyek'       => 'required|string|max:255',
            'status_proyek'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'dana_pendanaan'    => 'nullable|numeric',
            'deskripsi_proyek'  => 'nullable|string|max:1000',
        ]);
    
        DB::beginTransaction();
    
        try {
            $danaPendanaan = $request->input('dana_pendanaan');
            if (is_string($danaPendanaan)) {
                $danaPendanaan = str_replace(['.', ','], ['', '.'], $danaPendanaan);
            }
    
            DB::table('m_proyek')
                ->where('proyek_id', $proyekId)
                ->update([
                    'mitra_proyek_id'  => $request->input('mitra_id'),
                    'jenis_proyek_id'  => $request->input('jenis_proyek'),
                    'nama_proyek'      => $request->input('nama_proyek'),
                    'deskripsi_proyek' => $request->input('deskripsi_proyek'),
                    'status_proyek'    => $request->input('status_proyek'),
                    'tanggal_mulai'    => $request->input('tanggal_mulai'),
                    'tanggal_selesai'  => $request->input('tanggal_selesai'),
                    'dana_pendanaan'   => $danaPendanaan,
                    'updated_at'       => now(),
                    'updated_by'       => auth()->user()->id ?? session('user_id')
                ]);
    
            DB::commit();
            return redirect()->back()
            ->with('success', 'Data proyek berhasil diperbarui.')
            ->with('section_error', 'detail_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
            ->with('error', 'Gagal memperbarui proyek: ' . $e->getMessage()
            ->with('section_error', 'detail_proyek'));
        }
    }

    public function getDataProyekById($id){
        // Get project data
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
        
        // Get project leader information
        $projectLeader = DB::table('t_project_leader')
            ->where('proyek_id', $id)
            ->first();
            
        $leaderInfo = null;
        
        if ($projectLeader) {
            if ($projectLeader->leader_type === 'Dosen') {
                $leaderInfo = DB::table('d_dosen')
                    ->where('dosen_id', $projectLeader->leader_id)
                    ->select('dosen_id as id', 'nama_dosen as nama')
                    ->first();
            } elseif ($projectLeader->leader_type === 'Profesional') {
                $leaderInfo = DB::table('d_profesional')
                    ->where('profesional_id', $projectLeader->leader_id)
                    ->select('profesional_id as id', 'nama_profesional as nama')
                    ->first();
            }
        }
        
        // Ambil data yang diperlukan untuk dropdown - FILTER yang deleted_at IS NULL
        $jenisProyek = DB::table('m_jenis_proyek')
            ->whereNull('deleted_at')
            ->get();
            
        $daftarMitra = DB::table('d_mitra_proyek')
            ->whereNull('deleted_at')
            ->get();
            
        $dataDosen = DB::table('d_dosen')
            ->whereNull('deleted_at')  // Hanya ambil dosen yang tidak dihapus
            ->get();
            
        $dataProfesional = DB::table('d_profesional')
            ->whereNull('deleted_at')  // Hanya ambil profesional yang tidak dihapus
            ->get();
        
        return view('pages.Koordinator.DataProyek.kelola_data_proyek', compact(
            'proyek', 
            'projectLeader', 
            'leaderInfo',
            'jenisProyek',
            'daftarMitra',
            'dataDosen',
            'dataProfesional',
        ), [
            'titleSidebar' => 'Detail Data Proyek',
        ]);
    }

    public function updateProjectLeader(Request $request, $proyekId) {
        $request->validate([
            'leader_type' => 'required|in:Dosen,Profesional',
            'leader_id' => 'required|uuid'
        ]);
        
        DB::beginTransaction();
        
        try {
            $existingLeader = DB::table('t_project_leader')
                ->where('proyek_id', $proyekId)
                ->first();
                
            if ($existingLeader) {
                DB::table('t_project_leader')
                    ->where('project_leader_id', $existingLeader->project_leader_id)
                    ->update([
                        'leader_type' => $request->leader_type,
                        'leader_id' => $request->leader_id,
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->id ?? session('user_id')
                    ]);
                DB::table('m_proyek')
                    ->where('proyek_id', $proyekId)
                    ->update([
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->id ?? session('user_id')
                    ]);
            } else {
                // Create new leader
                $projectLeaderId = Str::uuid()->toString();
                DB::table('t_project_leader')->insert([
                    'project_leader_id' => $projectLeaderId,
                    'proyek_id' => $proyekId,
                    'leader_type' => $request->leader_type,
                    'leader_id' => $request->leader_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                    'updated_by' => auth()->user()->id ?? session('user_id')
                ]);
            }
            
            DB::commit();
            return redirect()->back()
                ->with('success', 'Project leader berhasil diperbarui.')
                ->with('section_error', 'anggota_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui project leader: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
    }
}