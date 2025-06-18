<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KoordinatorController extends Controller
{
    public function dashboard(){
        $koordinator = DB::table('d_koordinator')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Koordinator.dashboard', compact('koordinator'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    public function getProyekData()
    {
        try {
            // Count projects by status
            $proyekInisiasi = DB::table('m_proyek')
                ->where('status_proyek', 'Initiation')
                ->whereNull('deleted_at')
                ->count();

            $proyekInProgress = DB::table('m_proyek')
                ->where('status_proyek', 'In Progress')
                ->whereNull('deleted_at')
                ->count();

            $proyekDone = DB::table('m_proyek')
                ->where('status_proyek', 'Done')
                ->whereNull('deleted_at')
                ->count();

            // Total projects
            $totalProyek = DB::table('m_proyek')
                ->whereNull('deleted_at')
                ->count();

            // Recent projects
            $proyekTerbaru = DB::table('m_proyek')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Log for debugging
            \Log::info('Project data calculated', [
                'proyek_inisiasi' => $proyekInisiasi,
                'proyek_in_progress' => $proyekInProgress,
                'proyek_done' => $proyekDone,
                'total_proyek' => $totalProyek
            ]);

            return response()->json([
                'status' => 'success',
                'proyekInisiasi' => $proyekInisiasi,
                'proyekInProgress' => $proyekInProgress,
                'proyekDone' => $proyekDone,
                'totalProyek' => $totalProyek,
                'proyekTerbaru' => $proyekTerbaru,
                'message' => 'Data proyek berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting project data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data proyek: ' . $e->getMessage(),
                'proyekInisiasi' => 0,
                'proyekInProgress' => 0,
                'proyekDone' => 0,
                'totalProyek' => 0
            ], 500);
        }
    }

    /**
     * Get partner data for dashboard
     */
    public function getMitraData()
    {
        try {
            $totalMitra = DB::table('d_mitra_proyek')
                ->whereNull('deleted_at')
                ->count();

            //Count Mitra that proyek is in progress
        $totalMitraInProgress = DB::table('d_mitra_proyek as dmp')
            ->join('m_proyek as mp', 'dmp.mitra_proyek_id', '=', 'mp.mitra_proyek_id')
            ->where('mp.status_proyek', 'In Progress')
            ->whereNull('dmp.deleted_at')
            ->whereNull('mp.deleted_at')
            ->distinct('dmp.mitra_proyek_id')
            ->count('dmp.mitra_proyek_id');

            return response()->json([
                'status' => 'success',
                'totalMitra' => $totalMitra,
                'totalMitraInProgress' => $totalMitraInProgress,
                'message' => 'Data mitra berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting partner data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data mitra: ' . $e->getMessage(),
                'totalMitra' => 0
            ], 500);
        }
    }


    /**
     * Get financial data for TEFA dashboard
     */
    public function getKeuanganData()
    {
        try {
            // Calculate total income (pemasukan)
            $totalPemasukan = DB::table('t_keuangan_tefa as kt')
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->where('jt.nama_jenis_transaksi', 'Pemasukan') // Sesuaikan dengan nama di tabel master
                ->whereNull('kt.deleted_at')
                ->whereNull('jt.deleted_at')
                ->sum('kt.nominal_transaksi');

            // Calculate total expenses (pengeluaran)
            $totalPengeluaran = DB::table('t_keuangan_tefa as kt')
                ->join('m_jenis_transaksi as jt', 'kt.jenis_transaksi_id', '=', 'jt.jenis_transaksi_id')
                ->where('jt.nama_jenis_transaksi', 'Pengeluaran') // Sesuaikan dengan nama di tabel master
                ->whereNull('kt.deleted_at')
                ->whereNull('jt.deleted_at')
                ->sum('kt.nominal_transaksi');

            // Calculate current balance (saldo saat ini)
            $saldoSaatIni = $totalPemasukan - $totalPengeluaran;

            // Get additional statistics
            $totalTransaksi = DB::table('t_keuangan_tefa')
                ->whereNull('deleted_at')
                ->count();

            return response()->json([
                'status' => 'success',
                'saldoSaatIni' => (float) $saldoSaatIni,
                'totalPemasukan' => (float) $totalPemasukan,
                'totalPengeluaran' => (float) $totalPengeluaran,
                'totalTransaksi' => $totalTransaksi,
                'message' => 'Data keuangan TEFA berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting financial data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data keuangan: ' . $e->getMessage(),
                'saldoSaatIni' => 0,
                'totalPemasukan' => 0,
                'totalPengeluaran' => 0
            ], 500);
        }
    }

}
