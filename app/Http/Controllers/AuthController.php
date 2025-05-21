<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    public function showLoginForm(){
        return redirect()->route('beranda');
    }
    
    public function showRegisterDosenForm(){
        return view('pages.register_dosen');
    }
    

    public function showRegisterProfesionalForm(){
        return view('pages.register_profesional');
    }
    

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        
        ], [
            'email.required' => 'Email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }
        
        $credentials = $request->only('email', 'password');
        
        // Panggil login dari service dengan pengecekan status
        $loginResult = $this->authService->login($credentials);
        
        if ($loginResult === true) {
            switch(session('role')) {
                case 'Dosen':
                    return redirect()->route('dosen.dashboard');
                case 'Mahasiswa':
                    return redirect()->route('mahasiswa.dashboard');
                case 'Koordinator':
                    return redirect()->route('koordinator.dashboard');
                case 'Profesional':
                    return redirect()->route('profesional.dashboard');
                default:
                    return redirect()->route('login-landing-page')->with('error', 'Role tidak valid.');
            }
        } elseif ($loginResult === 'pending') {
            return redirect()->back()
                ->with('error', 'Akun Anda menunggu aktivasi. Silakan coba lagi nanti.')
                ->withInput($request->except('password'));
        } elseif ($loginResult === 'rejected') {
            return redirect()->back()
                ->with('error', 'Akun Anda ditolak. Silakan hubungi administrator untuk informasi lebih lanjut.')
                ->withInput($request->except('password'));
        } elseif ($loginResult === 'disabled') {
            return redirect()->back()
                ->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator untuk informasi lebih lanjut.')
                ->withInput($request->except('password'));
        }
        
        return redirect()->back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->except('password'));
    }
    
    public function registerDosen(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_dosen' => 'required|string|max:255',
            'email' => 'required|email|unique:d_user,email',
            'password' => 'required|min:6',
            'jenis_kelamin_dosen' => 'nullable|in:Laki-Laki,Perempuan',
            'tanggal_lahir_dosen' => 'nullable|date',
            'telepon_dosen' => 'nullable|numeric',
            'nidn_dosen' => 'required|string|unique:d_dosen,nidn_dosen',
            'profile_img_dosen' => 'nullable|image|max:2048',
        ], 
        [
            'nama_dosen.required' => 'Name field is required.',
            'nama_dosen.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'jenis_kelamin_dosen.required' => 'Gender field is required.',
            'jenis_kelamin_dosen.in' => 'Please select a valid gender option.',
            'tanggal_lahir_dosen.required' => 'Date of birth is required.',
            'tanggal_lahir_dosen.date' => 'Please enter a valid date.',
            'telepon_dosen.required' => 'Phone number is required.',
            'telepon_dosen.numeric' => 'Phone number must contain only digits.',
            'nidn.required' => 'NIP/NIDN field is required.',
            'nidn.unique' => 'This NIP/NIDN is already registered.',
            'profile_img.image' => 'The uploaded file must be an image.',
            'profile_img.max' => 'The image size cannot exceed 2MB.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('register-dosen')
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
        $data['role'] = 'Dosen';
        $data['status'] = 'Pending'; 
        
        try {
            $this->authService->registerDosen($data);
            return redirect()->route('login-landing-page')
                ->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari Admin');
        } catch (\Exception $e) {
            return redirect()->route('register-dosen') 
                ->with('error', 'Terjadi kesalahan saat registrasi: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
    
    public function registerProfesional(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_profesional' => 'required|string|max:255',
            'email' => 'required|email|unique:d_user,email',
            'password' => 'required|min:6',
            'jenis_kelamin-profesional' => 'nullable|in:Laki-Laki,Perempuan',
            'tanggal_lahir_profesional' => 'nullable|date',
            'telepon_profesional' => 'nullable|numeric',
            'profile_img_profesional' => 'nullable|image|max:2048',
        ], 
        [
            'nama_profesional.required' => 'Name field is required.',
            'nama_profesional.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'jenis_kelamin-profesional.required' => 'Gender field is required.',
            'jenis_kelamin-profesional.in' => 'Please select a valid gender option.',
            'tanggal_lahir_profesional.required' => 'Date of birth is required.',
            'tanggal_lahir_profesional.date' => 'Please enter a valid date.',
            'telepon_profesional.required' => 'Phone number is required.',
            'telepon_profesional.numeric' => 'Phone number must contain only digits.',
            'profile_img_profesional.image' => 'The uploaded file must be an image.',
            'profile_img_profesional.max' => 'The image size cannot exceed 2MB.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('register-profesional')
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }
        
        // Handle profile image upload
        $profileImgPath = null;
        if ($request->hasFile('profile_img_profesional')) {
            $profileImgPath = $request->file('profile_img_profesional')->store('profile_images', 'public');
        }
        
        // Prepare data for registration
        $data = $request->all();
        $data['profile_img_profesional'] = $profileImgPath;
        $data['role'] = 'Profesional';
        $data['status'] = 'Pending'; // Set status ke pending
        
        try {
            $this->authService->registerProfesional($data);
            return redirect()->route('login-landing-page')
                ->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari Koordinator.');
        } catch (\Exception $e) {
            return redirect()->route('register-profesional') 
                ->with('error', 'Terjadi kesalahan saat registrasi: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
    
    public function logout(){
        $this->authService->logout();
        return redirect()->route('login');
    }
}