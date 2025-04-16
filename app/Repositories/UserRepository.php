<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserRepository
{
    // Fungsi register untuk dosen
    public function registerDosen($data)
    {
        try {
            DB::beginTransaction();
            
            // Generate UUID untuk user_id dan dosen_id
            $userId = (string) Str::uuid();
            $dosenId = (string) Str::uuid();
            $now = Carbon::now();
            
            // Insert ke tabel d_user
            DB::table('d_user')->insert([
                'user_id' => $userId,
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'Dosen',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            
            // Insert ke tabel d_dosen
            DB::table('d_dosen')->insert([
                'dosen_id' => $dosenId,
                'user_id' => $userId,
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'profile_img' => $data['profile_img'] ?? null,
                'nidn' => $data['nidn'],
                'created_at' => $now,
                'created_by' => 0 // System
            ]);
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    // Fungsi login
    public function findUserByEmail($email)
    {
        return DB::table('d_user')
            ->select('d_user.*')
            ->where('d_user.email', $email)
            ->whereNull('d_user.deleted_at')
            ->first();
    }
    
    // Fungsi untuk mendapatkan detail dosen berdasarkan user_id
    public function getDosenByUserId($userId)
    {
        return DB::table('d_dosen')
            ->select('d_dosen.*')
            ->where('d_dosen.user_id', $userId)
            ->whereNull('d_dosen.deleted_at')
            ->first();
    }

    // Fungsi untuk mendapatkan detail mahasiswa berdasarkan user_id
    public function getMahasiswaByUserId($userId)
    {
        return DB::table('d_mahasiswa')
            ->select('d_mahasiswa.*')
            ->where('d_mahasiswa.user_id', $userId)
            ->whereNull('d_mahasiswa.deleted_at')
            ->first();
    }

    // Fungsi untuk mendapatkan detail koordinator berdasarkan user_id
    public function getKoordinatorByUserId($userId)
    {
        return DB::table('d_koordinator')
            ->select('d_koordinator.*')
            ->where('d_koordinator.user_id', $userId)
            ->whereNull('d_koordinator.deleted_at')
            ->first();
    }
}