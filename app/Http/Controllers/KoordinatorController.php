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

    //Proyek
    public function getDataProyek(Request $request){

        $jenisProyek = DB::table('m_jenis_proyek')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->get();
        $dataDosen = DB::table('d_dosen')->get();
        $query = DB::table('m_proyek')
        ->select(
            'm_proyek.proyek_id',
            'm_proyek.nama_proyek',
            'm_proyek.deskripsi_proyek',
            'm_proyek.tanggal_mulai',
            'm_proyek.tanggal_selesai',
            'm_proyek.status_proyek',
            'd_dosen.nama_dosen as nama_project_leader'
        )
        ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
        ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
        ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
        ->leftJoin('d_dosen', 't_project_leader.dosen_id', '=', 'd_dosen.dosen_id');
    
        $proyek = $query->get();
        
        return view('pages.Koordinator.data_proyek', compact('proyek'), [
            'titleSidebar' => 'Data Proyek',
            'jenisProyek' => $jenisProyek,
            'daftarMitra' => $daftarMitra,
            'dataDosen' => $dataDosen,
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
            'dana_pendanaan'    => 'nullable|numeric',
            'project_leader_id' => 'required|uuid'
        ]);
    
        // Buat UUID
        $proyek_id = Str::uuid()->toString();
        $project_leader_id = Str::uuid()->toString();
    
        DB::beginTransaction();
    
        try {
            // Simpan ke tabel m_proyek
            DB::table('m_proyek')->insert([
                'proyek_id'        => $proyek_id,
                'mitra_proyek_id'  => $request->input('mitra_id'),
                'jenis_proyek_id'  => $request->input('jenis_proyek'),
                'nama_proyek'      => $request->input('nama_proyek'),
                'deskripsi_proyek' => '-', // sementara, bisa diubah jadi input
                'status_proyek'    => $request->input('status_proyek'),
                'tanggal_mulai'    => $request->input('tanggal_mulai'),
                'tanggal_selesai'  => $request->input('tanggal_selesai'),
                'dana_pendanaan'   => $request->input('dana_pendanaan'),
                'created_at'       => now(),
                'created_by'       => auth()->user()->id ?? session('user_id'),
            ]);
    
            // Simpan ke tabel t_project_leader
            DB::table('t_project_leader')->insert([
                'project_leader_id' => $project_leader_id, // pastikan nama kolomnya benar
                'dosen_id'          => $request->input('project_leader_id'),
                'proyek_id'         => $proyek_id,
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

    public function getDataProyekById($id){
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $id)
            ->first();
        
        if (!$proyek) {
            return response()->json(['error' => 'Data proyek tidak ditemukan.'], 404);
        }
        
        return view('pages.Koordinator.detail_data_proyek', compact('proyek'), [
            'titleSidebar' => 'Detail Data Proyek',
        ]);
    }   
}