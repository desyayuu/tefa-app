<?php

namespace App\Http\Controllers\Koordinator\DataKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                $join->on('t_project_leader.leader_id', '=', 'd_dosen.dosen_id')
                    ->where('t_project_leader.leader_type', '=', 'Dosen');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_leader.leader_type', '=', 'Profesional');
            });
            
        // Apply search filter if provided
        if ($search) {
            $query->where('m_proyek.nama_proyek', 'like', '%' . $search . '%');
        }
            
        // Get results
        $proyek = $query->orderBy('m_proyek.created_at', 'desc')->get();
        
        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'proyek' => $proyek,
                'search' => $search
            ]);
        }
        
        // Regular view response
        return view('pages.Koordinator.DataKeuanganProyek.table_dana_keluar_proyek', [
            'proyek' => $proyek,
            'search' => $search,
            'titleSidebar' => 'Data Keluar Keuangan Proyek'
        ]);
    }
}
