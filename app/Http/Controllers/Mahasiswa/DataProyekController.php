<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DataProyekController extends Controller
{
    private function getMahasiswaId(){         
        $mahasiswaId = session('mahasiswa_id');              
        if (!$mahasiswaId) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan'], 404);
        }          
        return $mahasiswaId;
    } 

    public function getDataProyek(Request $request)
    {
        $mahasiswaId = $this->getMahasiswaId();
                
        // Query untuk mengambil proyek yang diikuti mahasiswa sebagai anggota
        $query = DB::table('m_proyek')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->join('t_project_member_mahasiswa', 'm_proyek.proyek_id', '=', 't_project_member_mahasiswa.proyek_id')
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
                DB::raw("'Anggota' as peran") // Mahasiswa selalu sebagai anggota
            )
            ->whereNull('m_proyek.deleted_at')
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->where('t_project_member_mahasiswa.mahasiswa_id', $mahasiswaId);

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
                
        return view('pages.Mahasiswa.DataProyek.table_data_proyek', compact('data', 'search'), [
            'titleSidebar' => 'Data Proyek',
        ]);
    }
    
    public function detailProyek($id, Request $request)
    {
        $mahasiswaId = session('mahasiswa_id');
            
        if (!$mahasiswaId) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan');
        }
        
        $isMember = DB::table('t_project_member_mahasiswa')
            ->where('proyek_id', $id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('dosen.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
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
            return redirect()->route('dosen.dataProyek')->with('error', 'Data proyek tidak ditemukan');
        }

        $jenisProyek = DB::table('m_jenis_proyek')->whereNull('deleted_at')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->whereNull('deleted_at')->get();
        $dataDosen = DB::table('d_dosen')->whereNull('deleted_at')->get();       
        $dataProfesional = DB::table('d_profesional')->whereNull('deleted_at')->get();
        $dataMahasiswa = DB::table('d_mahasiswa')->whereNull('deleted_at')->get();

        if ($isMember) {
            // Jika member, hanya tampilkan jenis dokumen tertentu
            $allowedDocumentTypes = [
                'Dokumen Teknis',
                'Dokumen Pengujian', 
                'Dokumen Lainnya',
                'Manual Book'
            ];
            
            $jenisDokumenPenunjang = DB::table('m_jenis_dokumen_penunjang')
                ->whereNull('deleted_at')
                ->whereIn('nama_jenis_dokumen_penunjang', $allowedDocumentTypes)
                ->get();
        }
         

        //Anggota Proyek Dan Leader
        $projectLeader = DB::table('t_project_leader')
            ->where('proyek_id', $id)
            ->first();
            
        $leaderInfo = null;
        
        if ($projectLeader) {
            if ($projectLeader->leader_type === 'Dosen') {
                $leaderInfo = DB::table('d_dosen')
                    ->where('dosen_id', $projectLeader->leader_id)
                    ->select('dosen_id as id', 'nama_dosen as nama')
                    ->first();
            } elseif ($projectLeader->leader_type === 'Profesional') {
                $leaderInfo = DB::table('d_profesional')
                    ->where('profesional_id', $projectLeader->leader_id)
                    ->select('profesional_id as id', 'nama_profesional as nama')
                    ->first();
            }
        }
        // Ambil data anggota dosen, profesional, dan mahasiswa
        $anggotaDosen = $this->getAnggotaDosen($id);
        $anggotaMahasiswa = $this->getAnggotaMahasiswa($id);
        $anggotaProfesional = $this->getAnggotaProfesional($id);
        
        return view('pages.Mahasiswa.DataProyek.kelola_data_proyek', compact(
            'proyek',
            'projectLeader',
            'leaderInfo',
            'anggotaDosen',
            'anggotaProfesional',
            'anggotaMahasiswa',
            'isMember',
            'jenisProyek', 
            'daftarMitra', 
            'dataDosen',
            'dataProfesional',
            'dataMahasiswa',
            'jenisDokumenPenunjang'
        ), [
            'titleSidebar' => 'Detail Proyek',
        ]);
    }

    public function updateDataProyek(Request $request, $proyekId){
        $request->validate([
            'mitra_id'          => 'required|uuid',
            'jenis_proyek'      => 'required|uuid',
            'nama_proyek'       => 'required|string|max:255',
            'status_proyek'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'dana_pendanaan'    => 'nullable|numeric',
            'deskripsi_proyek'  => 'nullable|string|max:1000',
        ]);
    
        DB::beginTransaction();
    
        try {
            $danaPendanaan = $request->input('dana_pendanaan');
            if (is_string($danaPendanaan)) {
                $danaPendanaan = str_replace(['.', ','], ['', '.'], $danaPendanaan);
            }
    
            DB::table('m_proyek')
                ->where('proyek_id', $proyekId)
                ->update([
                    'mitra_proyek_id'  => $request->input('mitra_id'),
                    'jenis_proyek_id'  => $request->input('jenis_proyek'),
                    'nama_proyek'      => $request->input('nama_proyek'),
                    'deskripsi_proyek' => $request->input('deskripsi_proyek'),
                    'status_proyek'    => $request->input('status_proyek'),
                    'tanggal_mulai'    => $request->input('tanggal_mulai'),
                    'tanggal_selesai'  => $request->input('tanggal_selesai'),
                    'dana_pendanaan'   => $danaPendanaan,
                    'updated_at'       => now(),
                    'updated_by'       => session('user_id'),
                ]);
    
            DB::commit();
            return redirect()->back()
            ->with('success', 'Data proyek berhasil diperbarui.')
            ->with('section_error', 'detail_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
            ->with('error', 'Gagal memperbarui proyek: ' . $e->getMessage()
            ->with('section_error', 'detail_proyek'));
        }
    }

    // UNTUK ANGGOTA PROYEK
    private function getAnggotaDosen($proyekId){
        return DB::table('t_project_member_dosen')
            ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
            ->where('t_project_member_dosen.proyek_id', $proyekId)
            ->whereNull('t_project_member_dosen.deleted_at')
            ->select(
                't_project_member_dosen.project_member_dosen_id',
                'd_dosen.dosen_id',
                'd_dosen.nama_dosen',
                'd_dosen.nidn_dosen'
            )
            ->get();
    }

    private function getAnggotaMahasiswa($proyekId){
        return DB::table('t_project_member_mahasiswa')
            ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
            ->where('t_project_member_mahasiswa.proyek_id', $proyekId)
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->select(
                't_project_member_mahasiswa.project_member_mahasiswa_id',
                'd_mahasiswa.mahasiswa_id',
                'd_mahasiswa.nama_mahasiswa',
                'd_mahasiswa.nim_mahasiswa'
            )
            ->get();
    }
    
    private function getAnggotaProfesional($proyekId){
        return DB::table('t_project_member_profesional')
            ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
            ->join('d_user', 'd_profesional.user_id', '=', 'd_user.user_id')
            ->where('t_project_member_profesional.proyek_id', $proyekId)
            ->whereNull('t_project_member_profesional.deleted_at')
            ->select(
                't_project_member_profesional.project_member_profesional_id',
                'd_profesional.profesional_id',
                'd_profesional.nama_profesional',
                'd_user.email'
            )
            ->get();
    }
}