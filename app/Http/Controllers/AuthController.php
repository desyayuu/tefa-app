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
        return redirect()->route('beranda');
    }
    
    // Tampilkan form register
    public function showRegisterForm()
    {
        return view('pages.register');
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
            'telepon' => 'required|numeric',
            'nidn' => 'required|numeric|unique:d_dosen,nidn',
            'profile_img' => 'nullable|image|max:2048',
        ], 
        [
            'nama.required' => 'Name field is required.',
            'nama.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'jenis_kelamin.required' => 'Gender field is required.',
            'jenis_kelamin.in' => 'Please select a valid gender option.',
            'tanggal_lahir.required' => 'Date of birth is required.',
            'tanggal_lahir.date' => 'Please enter a valid date.',
            'telepon.required' => 'Phone number is required.',
            'telepon.numeric' => 'Phone number must contain only digits.',
            'nidn.required' => 'NIP/NIDN field is required.',
            'nidn.numeric' => 'NIP/NIDN must contain only digits.',
            'nidn.unique' => 'This NIP/NIDN is already registered.',
            'profile_img.image' => 'The uploaded file must be an image.',
            'profile_img.max' => 'The image size cannot exceed 2MB.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('register-landing-page')
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
            return redirect()->route('login-landing-page')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Exception $e) {
            return redirect()->route('register-landing-page') 
                ->with('error', 'Terjadi kesalahan saat registrasi: ' . $e->getMessage())
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