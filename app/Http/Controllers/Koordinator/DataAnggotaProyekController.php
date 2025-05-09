<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class DataAnggotaProyekController extends Controller{

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