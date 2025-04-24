<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService {
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function registerDosen($data)
    {
        $userData = [
            'user_id' => (string) Str::uuid(),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'Dosen',
            'status' => 'Pending',
            'created_at' => now()
        ];
        
        $dosenData = [
            'dosen_id' => (string) Str::uuid(),
            'user_id' => $userData['user_id'],
            'nama_dosen' => $data['nama_dosen'],
            'jenis_kelamin_dosen' => $data['jenis_kelamin_dosen'] ?? null,
            'tanggal_lahir_dosen' => $data['tanggal_lahir_dosen'] ?? null,
            'telepon_dosen' => $data['telepon_dosen'] ?? null,
            'profile_img_dosen' => $data['profile_img_dosen'] ?? null,
            'nidn_dosen' => $data['nidn_dosen'],
            'created_at' => now(), 
            'created_by' => 0
        ];
        
        return $this->userRepository->registerDosen($userData, $dosenData);
    }
    

    public function registerProfesional($data)
    {
        $userData = [
            'user_id' => (string) Str::uuid(),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'Profesional',
            'status' => 'Pending',
            'created_at' => now()
        ];
        
        $profesionalData = [
            'profesional_id' => (string) Str::uuid(),
            'user_id' => $userData['user_id'],
            'nama_profesional' => $data['nama_profesional'],
            'jenis_kelamin_profesional' => $data['jenis_kelaminprofesional'] ?? null,
            'tanggal_lahir_profesional' => $data['tanggal_lahir_profesional'] ?? null,
            'telepon_profesional' => $data['telepon_profesional'] ?? null,
            'profile_img_profesional' => $data['profile_img_profesional'] ?? null,
            'created_at' => now(), 
            'created_by' => 0
        ];
        
        return $this->userRepository->registerProfesional($userData, $profesionalData);
    }
    
    public function login($credentials){
        $user = $this->userRepository->findUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }
        
        // Cek status user
        if ($user->status === 'Pending') {
            return 'pending';
        }else if($user->status === 'Rejected'){
            return 'rejected';
        }else if ($user->status === 'Disabled') {
            return 'disabled';
        }else if ($user->status === 'Active') {
            $userData = null;
            $role_id = null;
            
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
                case 'Profesional':
                    $userData = $this->userRepository->getProfesionalByUserId($user->user_id);
                    $role_id = 'profesional_id';
                    break;
                default:
                    return false;
            }
            
            if (!$userData) {
                return false;
            }
            
            // Set session data
            $nama = '';
            $profile_img = '';
            
            if ($user->role == 'Dosen') {
                $nama = $userData->nama_dosen;
                $profile_img = $userData->profile_img_dosen;
            } elseif ($user->role == 'Mahasiswa') {
                $nama = $userData->nama_mahasiswa;
                $profile_img = $userData->profile_img_mahasiswa;
            } elseif ($user->role == 'Profesional') {
                $nama = $userData->nama_profesional;
                $profile_img = $userData->profile_img_profesional;
            } else {
                $nama = $userData->nama_koordinator;
                $profile_img = $userData->profile_img_koordinator;
            }
            
            session([
                'user_id' => $user->user_id,
                $role_id => $userData->{$role_id},
                'email' => $user->email,
                'role' => $user->role,
                'nama' => $nama,
                'profile_img' => $profile_img,
            ]);
            return true;

        }else {
            return false;
        }
    }
    
    // Service untuk logout
    public function logout()
    {
        session()->flush();
        return true;
    }
}