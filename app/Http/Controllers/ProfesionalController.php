<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProfesionalController extends Controller
{
    public function dashboard()
    {
        $profesional = DB::table('d_profesional')
            ->where('user_id', session('user_id'))
            ->first();
            
        if (!$profesional) {
            return redirect()->route('login')->with('error', 'Data profesional tidak ditemukan');
        }
            
        return view('pages.Profesional.dashboard', compact('profesional'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    private function getProfesionalProyekIds($profesionalId)
    {
        // Get project IDs where profesional is a leader
        $leaderProjects = DB::table('t_project_leader')
            ->where('leader_type', 'Profesional')
            ->where('leader_id', $profesionalId)
            ->whereNull('deleted_at')
            ->pluck('proyek_id');

        // Get project IDs where profesional is a member
        $memberProjects = DB::table('t_project_member_profesional')
            ->where('profesional_id', $profesionalId)
            ->whereNull('deleted_at')
            ->pluck('proyek_id');

        // Combine and get unique project IDs
        $allProjectIds = $leaderProjects->merge($memberProjects)->unique();

        return $allProjectIds;
    }

    public function getProyekData()
    {
        try {
            // Get profesional data from session
            $profesional = DB::table('d_profesional')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$profesional) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data profesional tidak ditemukan'
                ], 404);
            }

            $profesionalId = $profesional->profesional_id;

            // Get all project IDs where this profesional is involved
            $proyekIds = $this->getProfesionalProyekIds($profesionalId);

            if ($proyekIds->isEmpty()) {
                // No projects found for this profesional
                return response()->json([
                    'status' => 'success',
                    'proyekInisiasi' => 0,
                    'proyekInProgress' => 0,
                    'proyekDone' => 0,
                    'totalProyek' => 0,
                    'proyekTerbaru' => [],
                    'profesionalInfo' => [
                        'profesional_id' => $profesionalId,
                        'nama_profesional' => $profesional->nama_profesional ?? 'Unknown'
                    ],
                    'message' => 'Profesional belum terlibat dalam proyek apapun'
                ]);
            }

            // Count projects by status for this profesional
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

            // Total projects for this profesional
            $totalProyek = $proyekInisiasi + $proyekInProgress + $proyekDone;

            // Count projects by role
            $proyekAsLeader = DB::table('t_project_leader as tpl')
                ->join('m_proyek as mp', 'tpl.proyek_id', '=', 'mp.proyek_id')
                ->where('tpl.leader_type', 'Profesional')
                ->where('tpl.leader_id', $profesionalId)
                ->whereNull('tpl.deleted_at')
                ->whereNull('mp.deleted_at')
                ->count();

            $proyekAsMember = DB::table('t_project_member_profesional as tpmd')
                ->join('m_proyek as mp', 'tpmd.proyek_id', '=', 'mp.proyek_id')
                ->where('tpmd.profesional_id', $profesionalId)
                ->whereNull('tpmd.deleted_at')
                ->whereNull('mp.deleted_at')
                ->count();

            return response()->json([
                'status' => 'success',
                'proyekInisiasi' => $proyekInisiasi,
                'proyekInProgress' => $proyekInProgress,
                'proyekDone' => $proyekDone,
                'totalProyek' => $totalProyek,
                'profesionalInfo' => [
                    'profesional_id' => $profesionalId,
                    'nama_profesional' => $profesional->nama_profesional ?? 'Unknown'
                ],
                'message' => 'Data proyek profesional berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting profesional project data', [
                'profesional_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data proyek profesional: ' . $e->getMessage(),
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
            // Get profesional data from session
            $profesional = DB::table('d_profesional')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$profesional) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data profesional tidak ditemukan'
                ], 404);
            }

            $profesionalId = $profesional->profesional_id;

            // Get all project IDs where this profesional is involved
            $proyekIds = $this->getProfesionalProyekIds($profesionalId);

            if ($proyekIds->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'totalMitraInProgress' => 0,
                    'totalMitraKeseluruhan' => 0,
                    'proyekInProgressCount' => 0,
                    'detailMitraInProgress' => [],
                    'profesionalInfo' => [
                        'profesional_id' => $profesionalId,
                        'nama_profesional' => $profesional->nama_profesional ?? 'Unknown'
                    ],
                    'message' => 'Profesional belum terlibat dalam proyek dengan mitra'
                ]);
            }

            // Count unique partners involved in this profesional's ongoing projects
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
                'profesionalInfo' => [
                    'profesional_id' => $profesionalId,
                    'nama_profesional' => $profesional->nama_profesional ?? 'Unknown'
                ],
                'message' => 'Data mitra profesional berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting profesional partner data', [
                'profesional_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data mitra profesional: ' . $e->getMessage(),
                'totalMitraInProgress' => 0,
                'proyekInProgressCount' => 0
            ], 500);
        }
    }
}