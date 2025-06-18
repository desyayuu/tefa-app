<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class DataTimelineMahasiswaController extends Controller
{
    private function checkMahasiswaRole($proyekId, $mahasiswaId )
    {
        $isMember = DB::table('t_project_member_mahasiswa')
            ->where('proyek_id', $proyekId)
            ->where('mahasiswa_id',  $mahasiswaId)
            ->whereNull('deleted_at')
            ->exists();

        return ['isMember' => $isMember];
    }

    public function getDataTimeline($id, Request $request){
         $mahasiswaId = session('mahasiswa_id');
        
        if (! $mahasiswaId) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Check role mahasiswa dalam proyek
        $roleCheck = $this->checkMahasiswaRole($id,  $mahasiswaId);
        if (!$roleCheck['isMember']) {
            return redirect()->route('mahasiswa.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
        }
    
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
            return redirect()->route('mahasiswa.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }
        
        // Get search parameter
        $search = $request->input('search');
        $perPageTimeline = $request->input('per_page_timeline', 3);
        $page = $request->input('page', 1);
        
        // Get timeline data
        $query = DB::table('t_timeline_proyek')
            ->where('proyek_id', $id)
            ->whereNull('deleted_at');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_timeline_proyek', 'like', "%{$search}%")
                ->orWhere('deskripsi_timeline', 'like', "%{$search}%");
            });
        }

        //Pagination
        $timelines = $query->orderBy('tanggal_mulai_timeline', 'asc')
            ->paginate($perPageTimeline, ['*'], 'page', $page);
        $paginationHtml = ''; 
        if($timelines->hasPages()) {
            $paginationHtml = view('vendor.pagination.custom', [
                'paginator' => $timelines,
                'elements' => $timelines->links()->elements,
            ])->render();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'isMember' => $roleCheck['isMember'],
                'data' => $timelines->items(),
                'proyek_dates' => [
                    'tanggal_mulai' => $proyek->tanggal_mulai,
                    'tanggal_selesai' => $proyek->tanggal_selesai
                ],
                'pagination' => [
                    'current_page' => $timelines->currentPage(),
                    'per_page_timeline' => $timelines->perPage(),
                    'total' => $timelines->total(),
                    'last_page' => $timelines->lastPage(),
                    'html' => $paginationHtml
                ]
            ]);
        }
    }
    
    public function detailDataTimeline($id)
    {
        $timeline = DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$timeline) {
            return response()->json([
                'success' => false,
                'message' => 'Data timeline tidak ditemukan'
            ], 404);
        }
        
        // Get project dates for validation
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $timeline->proyek_id)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => $timeline,
            'proyek_dates' => [
                'tanggal_mulai' => $proyek->tanggal_mulai,
                'tanggal_selesai' => $proyek->tanggal_selesai
            ]
        ]);
    }
}