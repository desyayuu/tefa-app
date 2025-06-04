<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $mahasiswa = DB::table('d_mahasiswa')
            ->where('user_id', session('user_id'))
            ->first();
            
        if (!$mahasiswa) {
            return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan');
        }
            
        return view('pages.Mahasiswa.dashboard', compact('mahasiswa'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    private function getMahasiswaProyekIds($mahasiswaId)
    {
        // Get project IDs where mahasiswa is a member
        $memberProjects = DB::table('t_project_member_mahasiswa')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->pluck('proyek_id');

        // Combine and get unique project IDs
        $allProjectIds = ($memberProjects)->unique();

        return $allProjectIds;
    }

    public function getProyekData()
    {
        try {
            // Get mahasiswa data from session
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }

            $mahasiswaId = $mahasiswa->mahasiswa_id;

            // Get all project IDs where this mahasiswa is involved
            $proyekIds = $this->getMahasiswaProyekIds($mahasiswaId);

            if ($proyekIds->isEmpty()) {
                // No projects found for this mahasiswa
                return response()->json([
                    'status' => 'success',
                    'proyekInisiasi' => 0,
                    'proyekInProgress' => 0,
                    'proyekDone' => 0,
                    'totalProyek' => 0,
                    'proyekTerbaru' => [],
                    'mahasiswaInfo' => [
                        'mahasiswa_id' => $mahasiswaId,
                        'nama_mahasiswa' => $mahasiswa->nama_mahasiswa ?? 'Unknown'
                    ],
                    'message' => 'Mahasiswa belum terlibat dalam proyek apapun'
                ]);
            }

            // Count projects by status for this mahasiswa
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

            // Total projects for this mahasiswa
            $totalProyek = $proyekInisiasi + $proyekInProgress + $proyekDone;


            $proyekAsMember = DB::table('t_project_member_mahasiswa as tpmd')
                ->join('m_proyek as mp', 'tpmd.proyek_id', '=', 'mp.proyek_id')
                ->where('tpmd.mahasiswa_id', $mahasiswaId)
                ->whereNull('tpmd.deleted_at')
                ->whereNull('mp.deleted_at')
                ->count();

            return response()->json([
                'status' => 'success',
                'proyekInisiasi' => $proyekInisiasi,
                'proyekInProgress' => $proyekInProgress,
                'proyekDone' => $proyekDone,
                'totalProyek' => $totalProyek,
                'mahasiswaInfo' => [
                    'mahasiswa_id' => $mahasiswaId,
                    'nama_mahasiswa' => $mahasiswa->nama_mahasiswa ?? 'Unknown'
                ],
                'message' => 'Data proyek mahasiswa berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting mahasiswa project data', [
                'mahasiswa_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data proyek mahasiswa: ' . $e->getMessage(),
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
            // Get mahasiswa data from session
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('user_id', session('user_id'))
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }

            $mahasiswaId = $mahasiswa->mahasiswa_id;

            // Get all project IDs where this mahasiswa is involved
            $proyekIds = $this->getMahasiswaProyekIds($mahasiswaId);

            if ($proyekIds->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'totalMitraInProgress' => 0,
                    'totalMitraKeseluruhan' => 0,
                    'proyekInProgressCount' => 0,
                    'detailMitraInProgress' => [],
                    'mahasiswaInfo' => [
                        'mahasiswa_id' => $mahasiswaId,
                        'nama_mahasiswa' => $mahasiswa->nama_mahasiswa ?? 'Unknown'
                    ],
                    'message' => 'Mahasiswa belum terlibat dalam proyek dengan mitra'
                ]);
            }

            // Count unique partners involved in this mahasiswa's ongoing projects
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
                'mahasiswaInfo' => [
                    'mahasiswa_id' => $mahasiswaId,
                    'nama_mahasiswa' => $mahasiswa->nama_mahasiswa ?? 'Unknown'
                ],
                'message' => 'Data mitra mahasiswa berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting mahasiswa partner data', [
                'mahasiswa_user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data mitra mahasiswa: ' . $e->getMessage(),
                'totalMitraInProgress' => 0,
                'proyekInProgressCount' => 0
            ], 500);
        }
    }
}