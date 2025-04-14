<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AuthService
{
    /**
     * Register a new user
     * 
     * @param array $userData
     * @param array $roleIds
     * @return string|false
     */
    public function register(array $userData, array $roleIds = [3]) //default role dosen 
    {
        DB::beginTransaction();
        
        try {
            // Generate UUID for user
            $userId = Uuid::uuid4()->toString();
            $userData['user_id'] = $userId;
            
            // Hash password
            $userData['password'] = Hash::make($userData['password']);
            
            // Set timestamps
            $userData['created_at'] = now();
            
            // Insert user
            DB::table('d_user')->insert($userData);
            
            // Insert user roles
            foreach ($roleIds as $roleId) {
                DB::table('d_user_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                ]);
            }
            
            DB::commit();
            return $userId;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    
    /**
     * Attempt to login a user
     * 
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login(string $email, string $password)
    {
        // Get user by email
        $user = DB::table('d_user')
            ->where('email', $email)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }
        
        // Get user roles
        $roles = DB::table('r_role')
            ->join('d_user_role', 'r_role.role_id', '=', 'd_user_role.role_id')
            ->where('d_user_role.user_id', $user->user_id)
            ->select('r_role.role_id', 'r_role.nama_role')
            ->get();
        
        // Prepare user data for session
        $userData = [
            'user_id' => $user->user_id,
            'nama' => $user->nama,
            'email' => $user->email,
            'roles' => $roles->toArray(),
        ];
        
        return $userData;
    }
    
    /**
     * Get user by ID with roles
     * 
     * @param string $userId
     * @return object|null
     */
    public function getUserWithRoles(string $userId)
    {
        $user = DB::table('d_user')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();
            
        if (!$user) {
            return null;
        }
        
        $roles = DB::table('r_role')
            ->join('d_user_role', 'r_role.role_id', '=', 'd_user_role.role_id')
            ->where('d_user_role.user_id', $userId)
            ->select('r_role.role_id', 'r_role.nama_role')
            ->get();
            
        $user->roles = $roles;
        return $user;
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string $userId
     * @param int|array $roleIds
     * @return bool
     */
    public function hasRole(string $userId, $roleIds)
    {
        if (!is_array($roleIds)) {
            $roleIds = [$roleIds];
        }
        
        $count = DB::table('d_user_role')
            ->where('user_id', $userId)
            ->whereIn('role_id', $roleIds)
            ->count();
            
        return $count > 0;
    }
}