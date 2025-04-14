<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('pages.landing_page.login');
    }
    
    /**
     * Process login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        
        $userData = $this->authService->login(
            $request->email,
            $request->password
        );
        
        if (!$userData) {
            return redirect()->back()
                ->withErrors(['email' => 'Email atau password salah'])
                ->withInput($request->except('password'));
        }
        
        // Save user data to session
        $request->session()->put('user', $userData);
        
        // Redirect based on role
        $highestRoleId = min(array_column($userData['roles'], 'role_id'));
        
        switch ($highestRoleId) {
            case 1: // Koordinator TEFA
                return redirect()->route('koordinator.dashboard');
            case 2: // Project Leader
            case 3: // Project Member
                return redirect()->route('dosen.dashboard');
            default: // Mahasiswa
                return redirect()->route('mahasiswa.dashboard');
        }
    }
    
    /**
     * Show register form for dosen
     */
    public function showRegister()
    {
        return view('pages.landing_page.register');
    }
    
    /**
     * Process dosen registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nip' => 'required|numeric',
            'telepon' => 'required|numeric',
            'email' => 'required|email|unique:d_user,email',
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }
        
        $userData = [
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password,
            'telepon' => $request->telepon,
            'nip' => $request->nip,
            'created_at' => now(),
        ];
        
        $roleIds = [2, 3];
        
        $userId = $this->authService->register($userData, $roleIds);
        
        if (!$userId) {
            return redirect()->back()
                ->withErrors(['general' => 'Gagal melakukan registrasi. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
        
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }
    
    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->session()->forget('user');
        return redirect()->route('beranda');
    }
}