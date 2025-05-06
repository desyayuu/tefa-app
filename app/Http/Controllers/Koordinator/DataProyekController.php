<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProyekController extends Controller
{


    public function getDataProyek(Request $request){
        $jenisProyek = DB::table('m_jenis_proyek')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->get();
        $dataDosen = DB::table('d_dosen')->get();
        $dataProfesional = DB::table('d_profesional')->get(); // Make sure you add this
        
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
                    WHEN t_project_leader.leader_type = "dosen" THEN d_dosen.nama_dosen
                    WHEN t_project_leader.leader_type = "profesional" THEN d_profesional.nama_profesional
                    ELSE NULL
                END as nama_project_leader')
            )
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
            ->leftJoin('d_dosen', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_dosen.dosen_id')
                    ->where('t_project_leader.leader_type', '=', 'dosen');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_leader.leader_type', '=', 'profesional');
            });
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('m_proyek.nama_proyek', 'like', "%{$search}%");
        }
        
        $proyek = $query->get();
        
        return view('pages.Koordinator.DataProyek.table_data_proyek', compact('proyek'), [
            'titleSidebar' => 'Data Proyek',
            'jenisProyek' => $jenisProyek,
            'daftarMitra' => $daftarMitra,
            'dataDosen' => $dataDosen,
            'dataProfesional' => $dataProfesional, // Pass professional data to the view
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
            'leader_type'       => 'required|in:Dosen,Profesional', // Add leader type validation
            'leader_id'         => 'required|uuid' // Now a generic leader ID field
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
                'deskripsi_proyek' => $request->input('deskripsi_proyek', '-'), // Allow for description input
                'status_proyek'    => $request->input('status_proyek'),
                'tanggal_mulai'    => $request->input('tanggal_mulai'),
                'tanggal_selesai'  => $request->input('tanggal_selesai'),
                'dana_pendanaan'   => $request->input('dana_pendanaan'),
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
        $proyek->nama_project_leader = 'Tidak ada project leader'; // Default value
        
        if ($projectLeader) {
            if ($projectLeader->leader_type === 'dosen') {
                $leaderInfo = DB::table('d_dosen')
                    ->where('dosen_id', $projectLeader->leader_id)
                    ->select('dosen_id as id', 'nama_dosen as nama', 'nidn_dosen as id_number', 'profile_img_dosen as profile_img')
                    ->first();
                
                if ($leaderInfo) {
                    $proyek->nama_project_leader = $leaderInfo->nama;
                }
            } elseif ($projectLeader->leader_type === 'profesional') {
                $leaderInfo = DB::table('d_profesional')
                    ->where('profesional_id', $projectLeader->leader_id)
                    ->select('profesional_id as id', 'nama_profesional as nama', 'nidn as id_number', 'profile_img as profile_img')
                    ->first();
                
                if ($leaderInfo) {
                    $proyek->nama_project_leader = $leaderInfo->nama;
                }
            }
        }
        
        // Ambil data yang diperlukan untuk dropdown (meskipun hanya untuk display)
        $jenisProyek = DB::table('m_jenis_proyek')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->get();
        $dataDosen = DB::table('d_dosen')->get();
        $dataProfesional = DB::table('d_profesional')->get();
        
        
        return view('pages.Koordinator.DataProyek.detail_data_proyek', compact(
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
}