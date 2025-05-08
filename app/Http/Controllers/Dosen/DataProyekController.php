<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DataProyekController extends Controller
{
    public function getDataProyek(Request $request)
    {
        $dosenId = session('dosen_id');
    
        if (!$dosenId) {
            return response()->json(['message' => 'Data dosen tidak ditemukan'], 404);
        }
        
        // Query dasar untuk mengambil proyek
        $query = DB::table('m_proyek')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.status_proyek',
                'd_mitra_proyek.nama_mitra',
                'm_jenis_proyek.nama_jenis_proyek',
                DB::raw("CASE 
                    WHEN t_project_leader.leader_type = 'Dosen' AND t_project_leader.leader_id = '$dosenId' THEN 'Project Leader'
                    ELSE 'Anggota'
                END as peran")
            )
            ->whereNull('m_proyek.deleted_at')
            ->where(function($query) use ($dosenId) {
                // Proyek dimana dosen menjadi project leader
                $query->where(function($q) use ($dosenId) {
                    $q->where('t_project_leader.leader_type', 'Dosen')
                      ->where('t_project_leader.leader_id', $dosenId);
                })
                // ATAU proyek dimana dosen menjadi anggota
                ->orWhereExists(function($subquery) use ($dosenId) {
                    $subquery->select(DB::raw(1))
                        ->from('t_project_member_dosen')
                        ->whereRaw('t_project_member_dosen.proyek_id = m_proyek.proyek_id')
                        ->where('t_project_member_dosen.dosen_id', $dosenId)
                        ->whereNull('t_project_member_dosen.deleted_at');
                });
            });
    
        // Search variable untuk ditampilkan kembali di form
        $search = $request->search;
    
        // Filter pencarian jika ada
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'like', '%' . $search . '%')
                  ->orWhere('d_mitra_proyek.nama_mitra', 'like', '%' . $search . '%');
            });
        }
    
        // Ambil data dengan paginasi
        $data = $query->orderBy('m_proyek.created_at', 'desc')->paginate(10);
        
        return view('pages.Dosen.DataProyek.table_data_proyek', compact('data', 'search'), [
            'titleSidebar' => 'Data Proyek',
        ]);
    }
    
    // Untuk menampilkan halaman detail proyek
    public function detailProyek($id)
    {
        $dosenId = DB::table('d_dosen')
            ->where('user_id', Auth::id())
            ->value('dosen_id');
            
        if (!$dosenId) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan');
        }
        
        // Cek apakah dosen adalah anggota atau leader dari proyek ini
        $isLeader = DB::table('t_project_leader')
            ->where('proyek_id', $id)
            ->where('leader_type', 'Dosen')
            ->where('leader_id', $dosenId)
            ->exists();
            
        $isMember = DB::table('t_project_member_dosen')
            ->where('proyek_id', $id)
            ->where('dosen_id', $dosenId)
            ->whereNull('deleted_at')
            ->exists();
            
        if (!$isLeader && !$isMember) {
            return redirect()->route('dosen.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
        }
        
        // Ambil data proyek
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
            return redirect()->route('dosen.dataProyek')->with('error', 'Data proyek tidak ditemukan');
        }
        
        // Ambil informasi project leader
        $projectLeader = DB::table('t_project_leader')
            ->where('proyek_id', $id)
            ->first();
            
        $leaderInfo = null;
        
        if ($projectLeader) {
            if ($projectLeader->leader_type === 'Dosen') {
                $leaderInfo = DB::table('d_dosen')
                    ->where('dosen_id', $projectLeader->leader_id)
                    ->select('dosen_id as id', 'nama_dosen as nama', 'nidn_dosen as identitas')
                    ->first();
            } elseif ($projectLeader->leader_type === 'Profesional') {
                $leaderInfo = DB::table('d_profesional')
                    ->where('profesional_id', $projectLeader->leader_id)
                    ->select('profesional_id as id', 'nama_profesional as nama', DB::raw('NULL as identitas'))
                    ->first();
            }
        }
        
        // Ambil data anggota dosen, profesional, dan mahasiswa
        $anggotaDosen = DB::table('t_project_member_dosen')
            ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
            ->where('t_project_member_dosen.proyek_id', $id)
            ->whereNull('t_project_member_dosen.deleted_at')
            ->select(
                'd_dosen.nama_dosen as nama',
                'd_dosen.nidn_dosen as identitas'
            )
            ->get();
            
        $anggotaProfesional = DB::table('t_project_member_profesional')
            ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
            ->join('d_user', 'd_profesional.user_id', '=', 'd_user.user_id')
            ->where('t_project_member_profesional.proyek_id', $id)
            ->whereNull('t_project_member_profesional.deleted_at')
            ->select(
                'd_profesional.nama_profesional as nama',
                'd_user.email as identitas'
            )
            ->get();
            
        $anggotaMahasiswa = DB::table('t_project_member_mahasiswa')
            ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
            ->where('t_project_member_mahasiswa.proyek_id', $id)
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->select(
                'd_mahasiswa.nama_mahasiswa as nama',
                'd_mahasiswa.nim_mahasiswa as identitas'
            )
            ->get();
        
        return view('pages.Dosen.DataProyek.detail', compact(
            'proyek',
            'projectLeader',
            'leaderInfo',
            'anggotaDosen',
            'anggotaProfesional',
            'anggotaMahasiswa',
            'isLeader'
        ));
    }
}