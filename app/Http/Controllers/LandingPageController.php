<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LandingPageController extends Controller 
{
    public function getJenisProyek(){
        return DB::table('m_jenis_proyek')->get();
    }
        
    public function layananKami()
    {
        $jenisProyek = $this->getJenisProyek();
        return view('pages.layanan_kami', compact('jenisProyek'));
    }
        
    public function landingPage()
    {
        $jenisProyek = $this->getJenisProyek();
        $summaryData = $this->countSummaryProyek();
        $proyekPoster = $this->getProyekPoster();
        
        return view('pages.landing_page', compact('jenisProyek', 'summaryData', 'proyekPoster'));
    }

    public function countSummaryProyek(){
        $summaryData = [];
        
        // Total Proyek (hanya yang belum dihapus)
        $summaryData['total_proyek'] = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->count();
            
        // Total Partisipasi Mahasiswa (distinct mahasiswa yang terlibat dalam proyek)
        $summaryData['total_mahasiswa'] = DB::table('t_project_member_mahasiswa')
            ->join('m_proyek', 't_project_member_mahasiswa.proyek_id', '=', 'm_proyek.proyek_id')
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->distinct()
            ->count('t_project_member_mahasiswa.mahasiswa_id');
            
        // Total Dosen yang terlibat (sebagai leader atau member)
        $dosenAsLeader = DB::table('t_project_leader')
            ->join('m_proyek', 't_project_leader.proyek_id', '=', 'm_proyek.proyek_id')
            ->where('t_project_leader.leader_type', 'Dosen')
            ->whereNull('t_project_leader.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_leader.leader_id');
            
        $dosenAsMember = DB::table('t_project_member_dosen')
            ->join('m_proyek', 't_project_member_dosen.proyek_id', '=', 'm_proyek.proyek_id')
            ->whereNull('t_project_member_dosen.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_member_dosen.dosen_id');
            
        $allDosen = $dosenAsLeader->merge($dosenAsMember)->unique();
        $summaryData['total_dosen'] = $allDosen->count();
        
        // Total Mitra Industri/Perusahaan (distinct mitra yang terlibat dalam proyek)
        $summaryData['total_mitra'] = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->distinct()
            ->count('mitra_proyek_id');
            
        // Total Profesional yang terlibat (sebagai leader)
        $summaryData['total_profesional'] = DB::table('t_project_leader')
            ->join('m_proyek', 't_project_leader.proyek_id', '=', 'm_proyek.proyek_id')
            ->where('t_project_leader.leader_type', 'Profesional')
            ->whereNull('t_project_leader.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->distinct()
            ->count('t_project_leader.leader_id');
            
        return $summaryData;
    }

    public function getProyekPoster(){
        return DB::table('d_luaran_proyek')
            ->join('m_proyek', 'd_luaran_proyek.proyek_id', '=', 'm_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.dana_pendanaan',
                'd_luaran_proyek.luaran_proyek_id',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNotNull('d_luaran_proyek.poster_proyek')
            ->where('d_luaran_proyek.poster_proyek', '!=', '')
            ->whereNull('d_luaran_proyek.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->orderBy('m_proyek.created_at', 'desc')
            ->limit(10) // Batasi 10 poster terbaru
            ->get();
    }

    public function getAllProyek()
    {
        $proyekList = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.*',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNull('m_proyek.deleted_at')
            ->whereNull('d_luaran_proyek.deleted_at')
            ->orderBy('m_proyek.created_at', 'desc')
            ->paginate(12);

        $jenisProyek = $this->getJenisProyek();
        
        return view('pages.all_proyek', compact('proyekList', 'jenisProyek'));
    }

    public function getProyekDetail($proyekId)
    {
        $proyek = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.*',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->where('m_proyek.proyek_id', $proyekId)
            ->whereNull('m_proyek.deleted_at')
            ->first();

        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan');
        }

        // Get team members
        $mahasiswa = DB::table('t_project_member_mahasiswa')
            ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
            ->where('t_project_member_mahasiswa.proyek_id', $proyekId)
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->select('d_mahasiswa.nama_mahasiswa', 'd_mahasiswa.nim')
            ->get();

        $dosen = DB::table('t_project_member_dosen')
            ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
            ->where('t_project_member_dosen.proyek_id', $proyekId)
            ->whereNull('t_project_member_dosen.deleted_at')
            ->select('d_dosen.nama_dosen', 'd_dosen.nip')
            ->get();

        $leaders = DB::table('t_project_leader')
            ->where('proyek_id', $proyekId)
            ->whereNull('deleted_at')
            ->get();

        return view('pages.proyek_detail', compact('proyek', 'mahasiswa', 'dosen', 'leaders'));
    }
}