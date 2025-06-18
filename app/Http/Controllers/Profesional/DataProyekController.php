<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DataProyekController extends Controller
{
    private function getProfesionalId(){
        $profesionalId = session('profesional_id');
    
        if (!$profesionalId) {
            return response()->json(['message' => 'Data profesional tidak ditemukan'], 404);
        }

        return $profesionalId;
    }
    public function getDataProyek(Request $request)
    {

        $profesionalId = $this->getProfesionalId();
        
        // Query dasar untuk mengambil proyek
        $query = DB::table('m_proyek')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
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
                DB::raw("CASE 
                    WHEN t_project_leader.leader_type = 'Profesional' AND t_project_leader.leader_id = '$profesionalId' THEN 'Project Leader'
                    ELSE 'Anggota'
                END as peran")
            )
            ->whereNull('m_proyek.deleted_at')
            ->where(function($query) use ($profesionalId) {
                $query->where(function($q) use ($profesionalId) {
                    $q->where('t_project_leader.leader_type', 'Profesional')
                      ->where('t_project_leader.leader_id', $profesionalId);
                })
                // ATAU proyek dimana profesional menjadi anggota
                ->orWhereExists(function($subquery) use ($profesionalId) {
                    $subquery->select(DB::raw(1))
                        ->from('t_project_member_profesional')
                        ->whereRaw('t_project_member_profesional.proyek_id = m_proyek.proyek_id')
                        ->where('t_project_member_profesional.profesional_id', $profesionalId)
                        ->whereNull('t_project_member_profesional.deleted_at');
                });
            });
    
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
        
        return view('pages.Profesional.DataProyek.table_data_proyek', compact('data', 'search'), [
            'titleSidebar' => 'Data Proyek',
        ]);
    }
    
    public function detailProyek($id, Request $request)
    {
        $profesionalId = session('profesional_id');
            
        if (!$profesionalId) {
            return redirect()->route('profesional.dashboard')->with('error', 'Data profesional tidak ditemukan');
        }
        
        $isLeader = DB::table('t_project_leader')
            ->where('proyek_id', $id)
            ->where('leader_type', 'Profesional')
            ->where('leader_id', $profesionalId)
            ->exists();
            
        $isMember = DB::table('t_project_member_profesional')
            ->where('proyek_id', $id)
            ->where('profesional_id', $profesionalId)
            ->whereNull('deleted_at')
            ->exists();
            
        if (!$isLeader && !$isMember) {
            return redirect()->route('profesional.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
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
            return redirect()->route('profesional.dataProyek')->with('error', 'Data proyek tidak ditemukan');
        }

        $jenisProyek = DB::table('m_jenis_proyek')->whereNull('deleted_at')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->whereNull('deleted_at')->get();
        $dataDosen = DB::table('d_dosen')->whereNull('deleted_at')->get();       
        $dataProfesional = DB::table('d_profesional')->whereNull('deleted_at')->get();
        $dataMahasiswa = DB::table('d_mahasiswa')->whereNull('deleted_at')->get();

        if ($isLeader) {
            $jenisDokumenPenunjang = DB::table('m_jenis_dokumen_penunjang')
                ->whereNull('deleted_at')
                ->get();
        } else {
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
        // Ambil data anggota profesional, profesional, dan mahasiswa
        $anggotaDosen = $this->getAnggotaDosen($id);
        $anggotaMahasiswa = $this->getAnggotaMahasiswa($id);
        $anggotaProfesional = $this->getAnggotaProfesional($id);
        
        return view('pages.Profesional.DataProyek.kelola_data_proyek', compact(
            'proyek',
            'projectLeader',
            'leaderInfo',
            'anggotaDosen',
            'anggotaProfesional',
            'anggotaMahasiswa',
            'isLeader',
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
                    'updated_by'       => auth()->user()->id ?? session('user_id')
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

    public function tambahAnggotaDosen(Request $request, $proyekId){
        $request->validate([
            'selected_dosen' => 'required',
        ]);
        
        // Decode JSON dari hidden input
        $selectedDosen = json_decode($request->selected_dosen, true);
        
        if (empty($selectedDosen)) {
            return redirect()->back()
                ->with('error', 'Tidak ada dosen yang dipilih')
                ->with('section_error', 'anggota_proyek');
        }
        
        DB::beginTransaction();
        
        try {
            $insertedCount = 0;
            $skippedCount = 0;
            
            foreach ($selectedDosen as $dosenId) {
                // Cek apakah dosen sudah menjadi anggota proyek ini
                $existingMember = DB::table('t_project_member_dosen')
                    ->where('proyek_id', $proyekId)
                    ->where('dosen_id', $dosenId)
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($existingMember) {
                    $skippedCount++;
                    continue;
                }
                
                // Generate UUID
                $memberId = Str::uuid()->toString();
                
                // Insert data anggota
                DB::table('t_project_member_dosen')->insert([
                    'project_member_dosen_id' => $memberId,
                    'dosen_id' => $dosenId,
                    'proyek_id' => $proyekId,
                    'created_at' => now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                ]);
                
                $insertedCount++;
            }
            
            DB::commit();
            
            if ($insertedCount > 0) {
                $message = $insertedCount . ' dosen berhasil ditambahkan sebagai anggota proyek';
                if ($skippedCount > 0) {
                    $message .= ' (' . $skippedCount . ' dosen dilewati karena sudah menjadi anggota)';
                }
                return redirect()->back()
                    ->with('success', $message)
                    ->with('section_error', 'anggota_proyek');
            } else {
                return redirect()->back()
                    ->with('error', 'Semua dosen yang dipilih sudah menjadi anggota proyek')
                    ->with('section_error', 'anggota_proyek');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan anggota dosen: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
    }

    public function hapusAnggotaDosen(Request $request, $proyekId, $memberId){
        DB::beginTransaction();

        try {
            // Soft delete anggota
            DB::table('t_project_member_dosen')
                ->where('project_member_dosen_id', $memberId)
                ->where('proyek_id', $proyekId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => auth()->user()->id ?? session('user_id'),
                ]);

            DB::commit();
            return redirect()->back()
                ->with('success', 'Anggota dosen berhasil dihapus dari proyek')
                ->with('section_error', 'anggota_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus anggota dosen: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
    }
}