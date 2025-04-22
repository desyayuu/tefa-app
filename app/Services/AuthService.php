<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    // Service untuk register dosen
    public function registerDosen($data)
    {
        return $this->userRepository->registerDosen($data);
    }
    
    // Service untuk login untuk semua role
    public function login($credentials)
    {
        $user = $this->userRepository->findUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }
        
        // Ambil data detail berdasarkan role
        $userData = null;
        switch ($user->role) {
            case 'Dosen':
                $userData = $this->userRepository->getDosenByUserId($user->user_id);
                $role_id = 'dosen_id';
                break;
            case 'Mahasiswa':
                $userData = $this->userRepository->getMahasiswaByUserId($user->user_id);
                $role_id = 'mahasiswa_id';
                break;
            case 'Koordinator':
                $userData = $this->userRepository->getKoordinatorByUserId($user->user_id);
                $role_id = 'koordinator_id';
                break;
            default:
                return false;
        }
        
        if (!$userData) {
            return false;
        }
        
        // Set session data
        session([
            'user_id' => $user->user_id,
            $role_id => $userData->{$role_id},
            'email' => $user->email,
            'nama' => $userData->nama,
            'role' => $user->role,
        ]);
        
        return true;
    }
    
    // Service untuk logout
    public function logout()
    {
        session()->flush();
        return true;
    }
}