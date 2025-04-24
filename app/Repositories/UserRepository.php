<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserRepository
{
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

    public function getProfesionalByUserId($userId)
    {
        return DB::table('d_profesional')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();
    }

    public function registerDosen($userData, $dosenData)
    {
        DB::beginTransaction();
        try {
            // Insert user data
            DB::table('d_user')->insert($userData);
            
            // Insert dosen data
            DB::table('d_dosen')->insert($dosenData);
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function registerProfesional($userData, $profesionalData)
    {
        DB::beginTransaction();
        
        try {
            // Insert user data
            DB::table('d_user')->insert($userData);
            
            // Insert profesional data
            DB::table('d_profesional')->insert($profesionalData);
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}