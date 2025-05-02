<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataUserController extends Controller
{
    public function getDataUser(Request $request){
        $search = $request->input('search');
    
        $query = DB::table('d_user')
            ->select(
                'd_user.user_id', 'd_user.email', 'd_user.role', 'd_user.status as status', 'd_user.created_at', 'd_user.updated_at', 'd_user.deleted_at',
                'd_dosen.nama_dosen as nama_dosen', 'd_dosen.nidn_dosen as nidn_dosen', 'd_dosen.telepon_dosen as telepon_dosen',
                'd_profesional.nama_profesional as nama_profesional', 'd_profesional.telepon_profesional as telepon_profesional',
                'd_mahasiswa.nama_mahasiswa as nama_mahasiswa', 'd_mahasiswa.nim_mahasiswa', 'd_mahasiswa.telepon_mahasiswa as telepon_mahasiswa',
                'd_koordinator.telepon_koordinator as telepon_koordinator', 'd_koordinator.nidn_koordinator as nidn_koordinator',
                'd_koordinator.nama_koordinator as nama_koordinator',
            )
            ->leftJoin('d_dosen', function($join) {
                $join->on('d_user.user_id', '=', 'd_dosen.user_id')
                     ->whereNull('d_dosen.deleted_at');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('d_user.user_id', '=', 'd_profesional.user_id')
                     ->whereNull('d_profesional.deleted_at');
            })
            ->leftJoin('d_mahasiswa', function($join) {
                $join->on('d_user.user_id', '=', 'd_mahasiswa.user_id')
                     ->whereNull('d_mahasiswa.deleted_at');
            })
            ->leftJoin('d_koordinator', function($join) {
                $join->on('d_user.user_id', '=', 'd_koordinator.user_id')
                     ->whereNull('d_koordinator.deleted_at');
            })
            ->whereNull('d_user.deleted_at');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d_user.email', 'like', "%{$search}%")
                  ->orWhere('d_user.role', 'like', "%{$search}%")
                  ->orWhere('d_dosen.nama_dosen', 'like', "%{$search}%")
                  ->orWhere('d_profesional.nama_profesional', 'like', "%{$search}%")
                  ->orWhere('d_mahasiswa.nama_mahasiswa', 'like', "%{$search}%");
            });
        }
        $user = $query->orderBy('d_user.created_at', 'desc')->paginate(10);
    
        return view('pages.Koordinator.data_user', compact('user', 'search'), [
            'titleSidebar' => 'Data User',
        ]);
    }

    public function updateStatusUser(Request $request, $id){
        try {
            $request->validate([
                'status' => 'required|in:Active,Rejected,Pending,Disabled',
            ]);
    
            // Get user data
            $user = DB::table('d_user')
                ->select(
                    'd_user.*',
                    'd_dosen.nama_dosen', 'd_dosen.nidn_dosen', 'd_dosen.telepon_dosen',
                    'd_profesional.nama_profesional', 'd_profesional.telepon_profesional',
                    'd_mahasiswa.nama_mahasiswa', 'd_mahasiswa.nim_mahasiswa', 'd_mahasiswa.telepon_mahasiswa',
                    'd_koordinator.nama_koordinator', 'd_koordinator.nidn_koordinator', 'd_koordinator.telepon_koordinator'
                )
                ->leftJoin('d_dosen', 'd_user.user_id', '=', 'd_dosen.user_id')
                ->leftJoin('d_profesional', 'd_user.user_id', '=', 'd_profesional.user_id')
                ->leftJoin('d_mahasiswa', 'd_user.user_id', '=', 'd_mahasiswa.user_id')
                ->leftJoin('d_koordinator', 'd_user.user_id', '=', 'd_koordinator.user_id')
                ->where('d_user.user_id', $id)
                ->first();
    
            if (!$user) {
                return redirect()->route('koordinator.dataUser')->with('error', 'User tidak ditemukan.');
            }
    
            // Begin transaction
            DB::beginTransaction();
    
            try {
                // Update user table - only the status is actually changeable in the form
                DB::table('d_user')
                    ->where('user_id', $id)
                    ->update([
                        'status' => $request->input('status'),
                        'updated_at' => now(),
                        'updated_by' => session('user_id'),
                    ]);
    
                // We don't need to update role-specific data since those fields are disabled
                // and no changes would be made to them anyway. The role itself cannot be changed
                // in this form due to the disabled input.
    
                DB::commit();
                return redirect()->route('koordinator.dataUser')->with('success', 'Status user berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('koordinator.dataUser')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataUser')->with('error', 'Validasi gagal: ' . $e->getMessage());
        }
    }

    public function deleteDataUser(Request $request, $id){
        try{
            $user = DB::table('d_user')
                ->where('user_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$user) {
                return redirect()->route('koordinator.dataUser')->with('error', 'User tidak ditemukan.');
            }

            DB::beginTransaction();

            try{
                $now = now();

                DB::table('d_user')
                    ->where('user_id', $id)
                    ->update([
                        'deleted_at' => $now,
                        'deleted_by' => session('user_id'),
                    ]);

                if($user->role == 'Dosen'){
                    DB::table('d_dosen')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                } elseif($user->role == 'Profesional'){
                    DB::table('d_profesional')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                } elseif($user->role == 'Mahasiswa'){
                    DB::table('d_mahasiswa')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                }elseif($user->role == 'Koordinator'){
                    DB::table('d_koordinator')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                }

                DB::commit();
                return redirect()->route('koordinator.dataUser')->with('success', 'User berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('koordinator.dataUser')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
            }
        }catch(\Exception $e){
            return redirect()->route('koordinator.dataUser')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
