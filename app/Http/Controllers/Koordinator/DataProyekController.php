<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DataProyekController extends Controller
{
    public function getDataProyek(Request $request){
        $jenisProyek = DB::table('m_jenis_proyek')->whereNull('deleted_at')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->whereNull('deleted_at')->get();
        $dataDosen = DB::table('d_dosen')->whereNull('deleted_at')->get();
        $dataProfesional = DB::table('d_profesional')->whereNull('deleted_at')->get();
        
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
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'like', "%{$search}%")
                  ->orWhere('d_mitra_proyek.nama_mitra', 'like', "%{$search}%");
            });
        }
        
        // Gunakan paginate untuk menghasilkan objek paginasi yang benar
        $proyek = $query->orderBy('m_proyek.created_at', 'desc')->paginate(10);
        
        // Tambahkan search parameter ke variable untuk ditampilkan di view
        $search = $request->search;
        
        return view('pages.Koordinator.DataProyek.table_data_proyek', compact(
            'proyek',
            'search',
            'jenisProyek',
            'daftarMitra',
            'dataDosen',
            'dataProfesional'
        ), [
            'titleSidebar' => 'Data Proyek',
        ]);
    }

    public function addDataProyek(Request $request){
        $request->validate([
            'mitra_id'          => 'required|uuid',
            'jenis_proyek'      => 'required|uuid',
            'nama_proyek'       => 'required|string|max:255',
            'status_proyek'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'dana_pendanaan'    => 'required', 
            'leader_type'       => 'required|in:Dosen,Profesional', 
            'leader_id'         => 'required|uuid',
            'deskripsi'         => 'nullable|string|max:1000', // Pastikan sama dengan nama field di form
        ]);
    
        // Format dana pendanaan
        $danaPendanaan = $request->input('dana_pendanaan');
        // Hapus karakter non-numerik
        $danaPendanaan = preg_replace('/\D/', '', $danaPendanaan);
    
        // Buat UUID
        $proyek_id = Str::uuid()->toString();
        $project_leader_id = Str::uuid()->toString();
    
        DB::beginTransaction();
    
        try {
            DB::table('m_proyek')->insert([
                'proyek_id'        => $proyek_id,
                'mitra_proyek_id'  => $request->input('mitra_id'),
                'jenis_proyek_id'  => $request->input('jenis_proyek'),
                'nama_proyek'      => $request->input('nama_proyek'),
                'deskripsi_proyek' => $request->input('deskripsi') ?? null, // Pastikan ini sama dengan nama field di form
                'status_proyek'    => $request->input('status_proyek'),
                'tanggal_mulai'    => $request->input('tanggal_mulai'),
                'tanggal_selesai'  => $request->input('tanggal_selesai'),
                'dana_pendanaan'   => $danaPendanaan,
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

    public function deleteDataProyek(Request $request, $proyekId)
    {
        DB::beginTransaction();
    
        try {
            // Siapkan timestamp dan user_id untuk tracking deleted_at dan deleted_by
            $now = now();
            $userId = auth()->user()->id ?? session('user_id');
    
            // 1. Soft delete anggota dosen
            DB::table('t_project_member_dosen')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                ]);
    
            // 2. Soft delete anggota mahasiswa
            DB::table('t_project_member_mahasiswa')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                ]);
    
            // 3. Soft delete anggota profesional
            DB::table('t_project_member_profesional')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                ]);
    
            // 4. Soft delete project leader
            DB::table('t_project_leader')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                ]);
    
            // 5. Soft delete proyek itu sendiri
            DB::table('m_proyek')
                ->where('proyek_id', $proyekId)
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                ]);
    
            DB::commit();
            return redirect()->route('koordinator.dataProyek')
                ->with('success', 'Data proyek beserta semua anggota berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus data proyek: ' . $e->getMessage());
        }
    }


    public function updateProjectLeader(Request $request, $proyekId) {
        $request->validate([
            'leader_type' => 'required|in:Dosen,Profesional',
            'leader_id' => 'required|uuid'
        ]);
        
        DB::beginTransaction();
        
        try {
            $existingLeader = DB::table('t_project_leader')
                ->where('proyek_id', $proyekId)
                ->first();
                
            if ($existingLeader) {
                DB::table('t_project_leader')
                    ->where('project_leader_id', $existingLeader->project_leader_id)
                    ->update([
                        'leader_type' => $request->leader_type,
                        'leader_id' => $request->leader_id,
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->id ?? session('user_id')
                    ]);
                DB::table('m_proyek')
                    ->where('proyek_id', $proyekId)
                    ->update([
                        'updated_at' => now(),
                        'updated_by' => auth()->user()->id ?? session('user_id')
                    ]);
            } else {
                // Create new leader
                $projectLeaderId = Str::uuid()->toString();
                DB::table('t_project_leader')->insert([
                    'project_leader_id' => $projectLeaderId,
                    'proyek_id' => $proyekId,
                    'leader_type' => $request->leader_type,
                    'leader_id' => $request->leader_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                    'updated_by' => auth()->user()->id ?? session('user_id')
                ]);
            }
            
            DB::commit();
            return redirect()->back()
                ->with('success', 'Project leader berhasil diperbarui.')
                ->with('section_error', 'anggota_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui project leader: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
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

    public function getDataProyekById($id, Request $request)
    {
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
        
        $jenisProyek = DB::table('m_jenis_proyek')->whereNull('deleted_at')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->whereNull('deleted_at')->get();            
        $dataDosen = DB::table('d_dosen')->whereNull('deleted_at')->get();       
        $dataProfesional = DB::table('d_profesional')->whereNull('deleted_at')->get();
        $dataMahasiswa = DB::table('d_mahasiswa')->whereNull('deleted_at')->get();
        $jenisDokumenPenunjang = DB::table('m_jenis_dokumen_penunjang')->whereNull('deleted_at')->get();
    
        // Ambil data anggota dosen
        $anggotaDosen = $this->getAnggotaDosen($id);
        $anggotaMahasiswa = $this->getAnggotaMahasiswa($id);
        $anggotaProfesional = $this->getAnggotaProfesional($id);
    
        // Get search for timeline
        $searchTimeline = $request->input('search_timeline');
        $queryTimeline = DB::table('t_timeline_proyek')
            ->where('proyek_id', $id)
            ->whereNull('deleted_at');
        if ($searchTimeline) {
            $queryTimeline->where(function($q) use ($searchTimeline) {
                $q->where('nama_timeline_proyek', 'like', "%{$searchTimeline}%")
                  ->orWhere('deskripsi_timeline', 'like', "%{$searchTimeline}%");
            });
        }
        $timelines = $queryTimeline->orderBy('tanggal_mulai_timeline', 'desc')->get();
        
        // Get search parameter for dokumen penunjang
        $searchDokumenPenunjang = $request->input('searchDokumenPenunjang');
        $queryDokumen = DB::table('m_dokumen_penunjang_proyek')
            ->join('m_jenis_dokumen_penunjang', 'm_dokumen_penunjang_proyek.jenis_dokumen_penunjang_id', '=', 'm_jenis_dokumen_penunjang.jenis_dokumen_penunjang_id')
            ->where('proyek_id', $id)
            ->whereNull('m_dokumen_penunjang_proyek.deleted_at')
            ->select(
                'm_dokumen_penunjang_proyek.*',
                'm_jenis_dokumen_penunjang.nama_jenis_dokumen_penunjang as jenis_dokumen'
            );
        if ($searchDokumenPenunjang) {
            $queryDokumen->where(function($q) use ($searchDokumenPenunjang) {
                $q->where('nama_dokumen_penunjang', 'like', "%{$searchDokumenPenunjang}%")
                  ->orWhere('m_jenis_dokumen_penunjang.nama_jenis_dokumen_penunjang', 'like', "%{$searchDokumenPenunjang}%");
            });
        }
        $dokumenPenunjang = $queryDokumen->orderBy('m_dokumen_penunjang_proyek.created_at', 'desc')->get();
        
        return view('pages.Koordinator.DataProyek.kelola_data_proyek', compact(
            'proyek', 
            'projectLeader', 
            'leaderInfo',
            'jenisProyek',
            'daftarMitra',
            'dataDosen',
            'dataProfesional',
            'anggotaDosen', 
            'anggotaMahasiswa',
            'anggotaProfesional',
            'dataMahasiswa', 
            'jenisDokumenPenunjang', 
            'timelines', 
            'searchTimeline',
            'dokumenPenunjang',
            'searchDokumenPenunjang'
        ), [
            'titleSidebar' => 'Detail Data Proyek',
        ]);
    }

    public function tambahAnggotaMahasiswa(Request $request, $proyekId){
        $request->validate([
            'selected_mahasiswa' => 'required',
        ]);
        
        // Decode JSON dari hidden input
        $selectedMahasiswa = json_decode($request->selected_mahasiswa, true);
        
        if (empty($selectedMahasiswa)) {
            return redirect()->back()
                ->with('error', 'Tidak ada mahasiswa yang dipilih')
                ->with('section_error', 'anggota_proyek');
        }
        
        DB::beginTransaction();
        
        try {
            $insertedCount = 0;
            $skippedCount = 0;
            
            foreach ($selectedMahasiswa as $mahasiswaId) {
                // Cek apakah mahasiswa sudah menjadi anggota proyek ini
                $existingMember = DB::table('t_project_member_mahasiswa')
                    ->where('proyek_id', $proyekId)
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($existingMember) {
                    $skippedCount++;
                    continue;
                }
                
                // Generate UUID
                $memberId = Str::uuid()->toString();
                
                // Insert data anggota
                DB::table('t_project_member_mahasiswa')->insert([
                    'project_member_mahasiswa_id' => $memberId,
                    'mahasiswa_id' => $mahasiswaId,
                    'proyek_id' => $proyekId,
                    'created_at' => now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                ]);
                
                $insertedCount++;
            }
            
            DB::commit();
            
            if ($insertedCount > 0) {
                $message = $insertedCount . ' mahasiswa berhasil ditambahkan sebagai anggota proyek';
                if ($skippedCount > 0) {
                    $message .= ' (' . $skippedCount . ' mahasiswa dilewati karena sudah menjadi anggota)';
                }
                return redirect()->back()
                    ->with('success', $message)
                    ->with('section_error', 'anggota_proyek');
            } else {
                return redirect()->back()
                    ->with('error', 'Semua mahasiswa yang dipilih sudah menjadi anggota proyek')
                    ->with('section_error', 'anggota_proyek');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan anggota mahasiswa: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
    }
    
    public function hapusAnggotaMahasiswa(Request $request, $proyekId, $memberId){
        DB::beginTransaction();
    
        try {
            // Soft delete anggota
            DB::table('t_project_member_mahasiswa')
                ->where('project_member_mahasiswa_id', $memberId)
                ->where('proyek_id', $proyekId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => auth()->user()->id ?? session('user_id'),
                ]);
    
            DB::commit();
            return redirect()->back()
                ->with('success', 'Anggota mahasiswa berhasil dihapus dari proyek')
                ->with('section_error', 'anggota_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus anggota mahasiswa: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
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

    public function tambahAnggotaProfesional(Request $request, $proyekId){
        $request->validate([
            'selected_profesional' => 'required',
        ]);
        
        // Decode JSON dari hidden input
        $selectedProfesional = json_decode($request->selected_profesional, true);
        
        if (empty($selectedProfesional)) {
            return redirect()->back()
                ->with('error', 'Tidak ada profesional yang dipilih')
                ->with('section_error', 'anggota_proyek');
        }
        
        DB::beginTransaction();
        
        try {
            $insertedCount = 0;
            $skippedCount = 0;
            
            foreach ($selectedProfesional as $profesionalId) {
                // Cek apakah profesional sudah menjadi anggota proyek ini
                $existingMember = DB::table('t_project_member_profesional')
                    ->where('proyek_id', $proyekId)
                    ->where('profesional_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->first();
                
                if ($existingMember) {
                    $skippedCount++;
                    continue;
                }
                
                // Generate UUID
                $memberId = Str::uuid()->toString();
                
                // Insert data anggota
                DB::table('t_project_member_profesional')->insert([
                    'project_member_profesional_id' => $memberId,
                    'profesional_id' => $profesionalId,
                    'proyek_id' => $proyekId,
                    'created_at' => now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                ]);
                
                $insertedCount++;
            }
            
            DB::commit();
            
            if ($insertedCount > 0) {
                $message = $insertedCount . ' profesional berhasil ditambahkan sebagai anggota proyek';
                if ($skippedCount > 0) {
                    $message .= ' (' . $skippedCount . ' profesional dilewati karena sudah menjadi anggota)';
                }
                return redirect()->back()
                    ->with('success', $message)
                    ->with('section_error', 'anggota_proyek');
            } else {
                return redirect()->back()
                    ->with('error', 'Semua profesional yang dipilih sudah menjadi anggota proyek')
                    ->with('section_error', 'anggota_proyek');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan anggota profesional: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
    }
    
    public function hapusAnggotaProfesional(Request $request, $proyekId, $memberId){
        DB::beginTransaction();
    
        try {
            // Soft delete anggota
            DB::table('t_project_member_profesional')
                ->where('project_member_profesional_id', $memberId)
                ->where('proyek_id', $proyekId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => auth()->user()->id ?? session('user_id'),
                ]);
    
            DB::commit();
            return redirect()->back()
                ->with('success', 'Anggota profesional berhasil dihapus dari proyek')
                ->with('section_error', 'anggota_proyek');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus anggota profesional: ' . $e->getMessage())
                ->with('section_error', 'anggota_proyek');
        }
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