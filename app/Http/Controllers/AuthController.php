<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('pages.landing_page');
    }
    
    // Tampilkan form register
    public function showRegisterForm()
    {
        return view('pages.landing_page');
    }
    
    // Proses login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        
        $credentials = $request->only('email', 'password');
        
        if ($this->authService->login($credentials)) {
            // Redirect berdasarkan role
            switch(session('role')) {
                case 'Dosen':
                    return redirect()->route('dosen.dashboard');
                case 'Mahasiswa':
                    return redirect()->route('mahasiswa.dashboard');
                case 'Koordinator':
                    return redirect()->route('koordinator.dashboard');
                default:
                    return redirect()->route('login-landing-page')->with('error', 'Role tidak valid.');
            }
        }
        
        return redirect()->back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->except('password'));
    }
    
    // Proses register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:d_user,email',
            'password' => 'required|min:6',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'telepon' => 'required|string',
            'nidn' => 'required|string|unique:d_dosen,nidn',
            'profile_img' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }
        
        // Handle profile image upload
        $profileImgPath = null;
        if ($request->hasFile('profile_img')) {
            $profileImgPath = $request->file('profile_img')->store('profile_images', 'public');
        }
        
        // Prepare data for registration
        $data = $request->all();
        $data['profile_img'] = $profileImgPath;
        
        try {
            $this->authService->registerDosen($data);
            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
    
    // Proses logout
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}