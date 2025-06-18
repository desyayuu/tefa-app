<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function dashboard()
    {
        $dosen = DB::table('d_dosen')
            ->where('user_id', session('user_id'))
            ->first();
            
        if (!$dosen) {
            return redirect()->route('login')->with('error', 'Data dosen tidak ditemukan');
        }
            
        return view('pages.Dosen.dashboard', compact('dosen'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    private function getDosenProyekIds($dosenId)
    {
        // Get project IDs where dosen is a leader
        $leaderProjects = DB::table('t_project_leader')
            ->where('leader_type', 'Dosen')
            ->where('leader_id', $dosenId)
            ->whereNull('deleted_at')
            ->pluck('proyek_id');

        // Get project IDs where dosen is a member
        $memberProjects = DB::table('t_project_member_dosen')
            ->where('dosen_id', $dosenId)
            ->whereNull('deleted_at')
            ->pluck('proyek_id');

        // Combine and get unique project IDs
        $allProjectIds = $leaderProjects->merge($memberProjects)->unique();

        return $allProjectIds;
    }

    public function getProyekData()
    {
        try {
            // Get dosen data from session
            $dosen = DB::table('d_dosen')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$dosen) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            $dosenId = $dosen->dosen_id;

            // Get all project IDs where this dosen is involved
            $proyekIds = $this->getDosenProyekIds($dosenId);

            if ($proyekIds->isEmpty()) {
                // No projects found for this dosen
                return response()->json([
                    'status' => 'success',
                    'proyekInisiasi' => 0,
                    'proyekInProgress' => 0,
                    'proyekDone' => 0,
                    'totalProyek' => 0,
                    'proyekTerbaru' => [],
                    'dosenInfo' => [
                        'dosen_id' => $dosenId,
                        'nama_dosen' => $dosen->nama_dosen ?? 'Unknown'
                    ],
                    'message' => 'Dosen belum terlibat dalam proyek apapun'
                ]);
            }

            // Count projects by status for this dosen
            $proyekInisiasi = DB::table('m_proyek')
                ->whereIn('proyek_id', $proyekIds)
                ->where('status_proyek', 'Initiation')
                ->whereNull('deleted_at')
                ->count();

            $proyekInProgress = DB::table('m_proyek')
                ->whereIn('proyek_id', $proyekIds)
                ->where('status_proyek', 'In Progress')
                ->whereNull('deleted_at')
                ->count();

            $proyekDone = DB::table('m_proyek')
                ->whereIn('proyek_id', $proyekIds)
                ->where('status_proyek', 'Done')
                ->whereNull('deleted_at')
                ->count();

            // Total projects for this dosen
            $totalProyek = $proyekInisiasi + $proyekInProgress + $proyekDone;

            // Count projects by role
            $proyekAsLeader = DB::table('t_project_leader as tpl')
                ->join('m_proyek as mp', 'tpl.proyek_id', '=', 'mp.proyek_id')
                ->where('tpl.leader_type', 'Dosen')
                ->where('tpl.leader_id', $dosenId)
                ->whereNull('tpl.deleted_at')
                ->whereNull('mp.deleted_at')
                ->count();

            $proyekAsMember = DB::table('t_project_member_dosen as tpmd')
                ->join('m_proyek as mp', 'tpmd.proyek_id', '=', 'mp.proyek_id')
                ->where('tpmd.dosen_id', $dosenId)
                ->whereNull('tpmd.deleted_at')
                ->whereNull('mp.deleted_at')
                ->count();

            return response()->json([
                'status' => 'success',
                'proyekInisiasi' => $proyekInisiasi,
                'proyekInProgress' => $proyekInProgress,
                'proyekDone' => $proyekDone,
                'totalProyek' => $totalProyek,
                'dosenInfo' => [
                    'dosen_id' => $dosenId,
                    'nama_dosen' => $dosen->nama_dosen ?? 'Unknown'
                ],
                'message' => 'Data proyek dosen berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting dosen project data', [
                'dosen_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data proyek dosen: ' . $e->getMessage(),
                'proyekInisiasi' => 0,
                'proyekInProgress' => 0,
                'proyekDone' => 0,
                'totalProyek' => 0
            ], 500);
        }
    }

    public function getMitraData()
    {
        try {
            // Get dosen data from session
            $dosen = DB::table('d_dosen')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$dosen) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            $dosenId = $dosen->dosen_id;

            // Get all project IDs where this dosen is involved
            $proyekIds = $this->getDosenProyekIds($dosenId);

            if ($proyekIds->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'totalMitraInProgress' => 0,
                    'totalMitraKeseluruhan' => 0,
                    'proyekInProgressCount' => 0,
                    'detailMitraInProgress' => [],
                    'dosenInfo' => [
                        'dosen_id' => $dosenId,
                        'nama_dosen' => $dosen->nama_dosen ?? 'Unknown'
                    ],
                    'message' => 'Dosen belum terlibat dalam proyek dengan mitra'
                ]);
            }

            // Count unique partners involved in this dosen's ongoing projects
            $totalMitraInProgress = DB::table('d_mitra_proyek as dmp')
                ->join('m_proyek as mp', 'dmp.mitra_proyek_id', '=', 'mp.mitra_proyek_id')
                ->whereIn('mp.proyek_id', $proyekIds)
                ->where('mp.status_proyek', 'In Progress')
                ->whereNull('dmp.deleted_at')
                ->whereNull('mp.deleted_at')
                ->distinct('dmp.mitra_proyek_id')
                ->count('dmp.mitra_proyek_id');

            return response()->json([
                'status' => 'success',
                'totalMitraInProgress' => $totalMitraInProgress,
                'dosenInfo' => [
                    'dosen_id' => $dosenId,
                    'nama_dosen' => $dosen->nama_dosen ?? 'Unknown'
                ],
                'message' => 'Data mitra dosen berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting dosen partner data', [
                'dosen_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data mitra dosen: ' . $e->getMessage(),
                'totalMitraInProgress' => 0,
                'proyekInProgressCount' => 0
            ], 500);
        }
    }
}