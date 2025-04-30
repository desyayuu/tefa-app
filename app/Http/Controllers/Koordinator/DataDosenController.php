<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class DataDosenController extends Controller{
    public function dashboard(){
        $koordinator = DB::table('d_koordinator')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Koordinator.dashboard', compact('koordinator'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    public function getDataDosen(Request $request){
        $search = $request->input('search'); 

        $query = DB::table ('d_dosen as dosen')
            ->join('d_user as user', 'dosen.user_id', '=', 'user.user_id')
            ->select('dosen.*', 'user.*')
            ->whereNull('dosen.deleted_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('dosen.nama_dosen', 'like', "%$search%")
                ->orWhere('dosen.nidn_dosen', 'like', "%$search%")
                ->orWhere('user.email', 'like', "%$search%");
            });
        }

        $dosen = $query->paginate(10); 
        return view('pages.Koordinator.data_dosen', compact('dosen', 'search'), [
            'titleSidebar' => 'Data Dosen'
        ]);
    }

    public function tambahDataDosen(Request $request){
        try {
            $isSingle = $request->input('is_single') === '1';
            
            if ($isSingle) {
                $request->validate([
                    'nama_dosen' => 'required|string|max:255',
                    'nidn_dosen' => 'required|string|unique:d_dosen,nidn_dosen',
                    'email_dosen' => 'required|email|unique:d_user,email',
                    'status_akun_dosen' => 'required|in:Active,Rejected,Pending,Disabled', 
                    'profile_img_dosen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                    'telepon_dosen' => 'nullable|string|max:20',
                    'tanggal_lahir_dosen' => 'nullable|date',
                    'jenis_kelamin_dosen' => 'nullable|in:Laki-Laki,Perempuan',
                ], 
                [
                    'email_dosen.unique' => 'Email sudah terdaftar dalam sistem.',
                    'nidn_dosen.unique' => 'NIDN sudah terdaftar dalam sistem.',
                    'nama_dosen.required' => 'Nama dosen harus diisi.',
                    'nidn_dosen.required' => 'NIDN harus diisi.',
                    'email_dosen.required' => 'Email harus diisi.',
                    'status_akun_dosen.required' => 'Status harus diisi.',
                    'email_dosen.email' => 'Format email tidak valid.',
                    'profile_img_dosen.image' => 'File harus berupa gambar.',
                    'profile_img_dosen.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                    'profile_img_dosen.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                ]);
    
                $nidnExists = DB::table('d_dosen')->where('nidn_dosen', $request->input('nidn_dosen'))->exists();
                $emailExists = DB::table('d_user')->where('email', $request->input('email_dosen'))->exists();
                
                if ($nidnExists) {
                    return back()->withInput()->withErrors(['nidn_dosen' => 'NIDN sudah ada di daftar data dosen.']);
                } else if ($emailExists) {
                    return back()->withInput()->withErrors(['email_dosen' => 'Email sudah ada di daftar data dosen.']);
                }
                
                $userId = Str::uuid();
                $dosenId = Str::uuid();
                DB::beginTransaction();
                
                try {
                    DB::table('d_user')->insert([
                        'user_id' => $userId,
                        'email' => $request->input('email_dosen'),
                        'password' => bcrypt($request->input('password_dosen') ?: 'password123'),
                        'role' => 'Dosen',
                        'status' => $request->input('status_akun_dosen', 'Active'), 
                        'created_by' => session('user_id'),
                    ]);
                    
                    $dosenData = [
                        'dosen_id' => $dosenId,
                        'user_id' => $userId,
                        'nama_dosen' => $request->input('nama_dosen'),
                        'nidn_dosen' => $request->input('nidn_dosen'),
                        'tanggal_lahir_dosen' => $request->filled('tanggal_lahir_dosen') ? $request->input('tanggal_lahir_dosen') : null,
                        'jenis_kelamin_dosen' => $request->input('jenis_kelamin_dosen') ? $request->input('jenis_kelamin_dosen') : null,
                        'telepon_dosen' => $request->input('telepon_dosen') ? $request->input('telepon_dosen') : null,                        'status_akun_dosen' => $request->input('status_akun_dosen', 'Active'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ];
                    
                    if ($request->hasFile('profile_img_dosen')) {
                        $file = $request->file('profile_img_dosen');
                        
                        if ($file->isValid()) {
                            $uploadPath = public_path('uploads/profile_dosen');
                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }
                            
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            
                            try {
                                if ($file->move($uploadPath, $filename)) {
                                    $dosenData['profile_img_dosen'] = 'uploads/profile_dosen/' . $filename;
                                } else {
                                    throw new \Exception("Failed to move uploaded file");
                                }
                            } catch (\Exception $e) {
                                throw $e;
                            }
                        } else {
                            throw new \Exception("Uploaded file is not valid");
                        }
                    } else {
                        \Log::info('No file in request for single mode', [
                            'has_file' => $request->hasFile('profile_img_dosen'),
                            'request_keys' => $request->keys()
                        ]);
                    }
                    
                    DB::table('d_dosen')->insert($dosenData);
                    DB::commit();
                    return redirect()->route('koordinator.dataDosen')->with('success', 'Data dosen berhasil ditambahkan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding dosen data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataDosen')->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
                }
            } else {
                $dosenData = json_decode($request->input('dosen_data'), true);
                if (empty($dosenData)) {
                    return redirect()->route('koordinator.dataDosen')->with('error', 'Tidak ada data dosen untuk ditambahkan.');
                }
                
                DB::beginTransaction();
                try {
                    $insertedCount = 0;
                    $errors = [];
                    
                    foreach ($dosenData as $index => $dosen) {
                        if (empty($dosen['nama_dosen']) || empty($dosen['nidn_dosen']) || empty($dosen['email_dosen'])) {
                            array_push($errors, 'Data dosen tidak lengkap: ' . ($dosen['nama_dosen'] ?? 'Unnamed'));
                            continue;
                        }
                        
                        $nidnExists = DB::table('d_dosen')->where('nidn_dosen', $dosen['nidn_dosen'])->exists();
                        $emailExists = DB::table('d_user')->where('email', $dosen['email_dosen'])->exists();
                        
                        if ($nidnExists) {
                            array_push($errors, 'NIDN ' . $dosen['nidn_dosen'] . ' sudah terdaftar.');
                            continue;
                        }
                        
                        if ($emailExists) {
                            array_push($errors, 'Email ' . $dosen['email_dosen'] . ' sudah terdaftar.');
                            continue;
                        }
                        
                        $userId = Str::uuid();
                        $dosenId = Str::uuid();
                        
                        DB::table('d_user')->insert([
                            'user_id' => $userId,
                            'email' => $dosen['email_dosen'],
                            'password' => bcrypt($dosen['password_dosen'] ?: 'password123'), // Default to password123
                            'role' => 'Dosen',
                            'status' => $dosen['status_akun_dosen'] ?? 'Active', // Fixed field name
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ]);
                        
                        $dosenRecord = [
                            'dosen_id' => $dosenId,
                            'user_id' => $userId,
                            'nama_dosen' => $dosen['nama_dosen'],
                            'nidn_dosen' => $dosen['nidn_dosen'],
                            'tanggal_lahir_dosen' => $dosen['tanggal_lahir_dosen'] ?? null,
                            'jenis_kelamin_dosen' => $dosen['jenis_kelamin_dosen'] ?? null,
                            'telepon_dosen' => $dosen['telepon_dosen'] ?? null,
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ];
                        
                        $fileKey = "profile_img_dosen_{$index}";
                        \Log::info("Checking for file {$fileKey}", [
                            'has_file' => $request->hasFile($fileKey),
                            'all_files' => array_keys($request->files->all())
                        ]);
                        
                        if ($request->hasFile($fileKey)) {
                            $file = $request->file($fileKey);
                            
                            \Log::info("Processing file for index {$index}", [
                                'file_key' => $fileKey,
                                'file_name' => $file->getClientOriginalName(),
                                'file_size' => $file->getSize()
                            ]);
                            
                            if ($file->isValid()) {
                                $uploadPath = public_path('uploads/profile_dosen');
                                if (!is_dir($uploadPath)) {
                                    mkdir($uploadPath, 0777, true);
                                }
                                
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                if ($file->move($uploadPath, $filename)) {
                                    $dosenRecord['profile_img_dosen'] = 'uploads/profile_dosen/' . $filename;
                                    
                                    \Log::info('File upload success for multiple mode', [
                                        'index' => $index,
                                        'path' => $dosenRecord['profile_img_dosen']
                                    ]);
                                } else {
                                    \Log::error('Failed to move file for multiple mode', [
                                        'index' => $index,
                                        'file' => $file->getClientOriginalName()
                                    ]);
                                }
                            } else {
                                \Log::error('Invalid file for multiple mode', [
                                    'index' => $index,
                                    'error' => $file->getError()
                                ]);
                            }
                        } else if (isset($dosen['has_profile_img']) && $dosen['has_profile_img']) {
                            \Log::warning("File flag set but no file found for index {$index}", [
                                'file_key' => $fileKey
                            ]);
                        }

                        DB::table('d_dosen')->insert($dosenRecord);       
                        $insertedCount++;
                    }
                    
                    DB::commit();
                    
                    if (count($errors) > 0) {
                        $errorMessage = implode('<br>', $errors);
                        return redirect()->route('koordinator.dataDosen')
                            ->with('warning', "$insertedCount data dosen berhasil ditambahkan.<br>Beberapa error terjadi:<br>$errorMessage");
                    }
                    
                    return redirect()->route('koordinator.dataDosen')
                        ->with('success', "$insertedCount data dosen berhasil ditambahkan.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding multiple dosen data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataDosen')
                        ->with('error', 'Gagal menambahkan data dosen: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Exception in tambahDataDosen', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataDosen')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function checkEmailNidnExists(Request $request){
        // Log input untuk debugging
        \Log::info('Check email/nidn request:', $request->all());
        
        $email = $request->input('email_dosen');
        $nidn = $request->input('nidn_dosen');
        
        $emailExists = false;
        $nidnExists = false;
        
        if ($email) {
            $emailExists = DB::table('d_user')
                ->where('email', $email)
                ->exists();
        }
        
        if ($nidn) {
            $nidnExists = DB::table('d_dosen')
                ->where('nidn_dosen', $nidn)
                ->exists();
        }
        
        // Log hasil untuk debugging
        \Log::info('Check result:', [
            'email' => $email,
            'nidn' => $nidn,
            'emailExists' => $emailExists,
            'nidnExists' => $nidnExists
        ]);
        
        return response()->json([
            'emailExists' => $emailExists,
            'nidnExists' => $nidnExists
        ]);
    }

    public function editDataDosen($id){
        $dosen = DB::table('d_dosen as dosen')
            ->join('d_user as user', 'dosen.user_id', '=', 'user.user_id')
            ->select('dosen.*', 'user.*')
            ->where('dosen.dosen_id', $id)
            ->first();
            
        if (!$dosen) {
            return redirect()->route('koordinator.dataDosen')
                ->with('error', 'Data dosen tidak ditemukan.');
        }
        
        return view('pages.Koordinator.edit_dosen', compact('dosen'), [
            'titleSidebar' => 'Edit Data Dosen'
        ]);
    }

    public function updateDataDosen(Request $request, $id){
        try {
            $request->validate([
                'nama_dosen' => 'required|string|max:255',
                'nidn_dosen' => 'required|string',
                'email_dosen' => 'required|email',
                'status_akun_dosen' => 'required|in:Active,Rejected,Pending,Disabled', 
                'profile_img_dosen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_dosen' => 'nullable|string|max:20',
                'tanggal_lahir_dosen' => 'nullable|date',
                'jenis_kelamin_dosen' => 'nullable|in:Laki-Laki,Perempuan',
            ], 
            [
                'email_dosen.email' => 'Format email tidak valid.',
                'nama_dosen.required' => 'Nama dosen harus diisi.',
                'nidn_dosen.required' => 'NIDN harus diisi.',
                'email_dosen.required' => 'Email harus diisi.',
                'status_akun_dosen.required' => 'Status harus diisi.',
                'profile_img_dosen.image' => 'File harus berupa gambar.',
                'profile_img_dosen.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_dosen.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
            ]);
            
            // Get current dosen data
            $dosen = DB::table('d_dosen')
                ->where('dosen_id', $id)
                ->first();
                
            if (!$dosen) {
                return redirect()->route('koordinator.dataDosen')
                    ->with('error', 'Data dosen tidak ditemukan.');
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $dosen->user_id)
                ->first();
                
            // Check if NIDN is being changed and if it's already taken
            if ($dosen->nidn_dosen != $request->input('nidn_dosen')) {
                $nidnExists = DB::table('d_dosen')
                    ->where('nidn_dosen', $request->input('nidn_dosen'))
                    ->where('dosen_id', '!=', $id)
                    ->exists();
                    
                if ($nidnExists) {
                    return back()->withInput()->withErrors(['nidn_dosen' => 'NIDN sudah terdaftar dalam sistem.']);
                }
            }
            
            // Check if email is being changed and if it's already taken
            if ($user->email != $request->input('email_dosen')) {
                $emailExists = DB::table('d_user')
                    ->where('email', $request->input('email_dosen'))
                    ->where('user_id', '!=', $dosen->user_id)
                    ->exists();
                    
                if ($emailExists) {
                    return back()->withInput()->withErrors(['email_dosen' => 'Email sudah terdaftar dalam sistem.']);
                }
            }
            
            DB::beginTransaction();
            
            try {
                // Update user data
                $userData = [
                    'email' => $request->input('email_dosen'),
                    'status' => $request->input('status_akun_dosen'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // If NIDN changed or password provided, update password
                if ($dosen->nidn_dosen != $request->input('nidn_dosen') || $request->filled('password_dosen')) {
                    $userData['password'] = bcrypt($request->input('password_dosen') ?: $request->input('nidn_dosen'));
                }
                
                DB::table('d_user')
                    ->where('user_id', $dosen->user_id)
                    ->update($userData);
                
                
                // Update dosen data
                $dosenData = [
                    'nama_dosen' => $request->input('nama_dosen'),
                    'nidn_dosen' => $request->input('nidn_dosen'),
                    'tanggal_lahir_dosen' => $request->filled('tanggal_lahir_dosen') ? $request->input('tanggal_lahir_dosen') : null,
                    'jenis_kelamin_dosen' => $request->input('jenis_kelamin_dosen'),
                    'telepon_dosen' => $request->input('telepon_dosen'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // Handle profile image upload
                if ($request->hasFile('profile_img_dosen')) {
                    $file = $request->file('profile_img_dosen');
                    
                    if ($file->isValid()) {
                        $uploadPath = public_path('uploads/profile_dosen');
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        
                        if ($file->move($uploadPath, $filename)) {
                            // Delete old image if exists
                            if ($dosen->profile_img_dosen) {
                                $oldImagePath = public_path($dosen->profile_img_dosen);
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            
                            $dosenData['profile_img_dosen'] = 'uploads/profile_dosen/' . $filename;
                        } else {
                            throw new \Exception("Failed to move uploaded file");
                        }
                    } else {
                        throw new \Exception("Uploaded file is not valid");
                    }
                }
                
                DB::table('d_dosen')
                    ->where('dosen_id', $id)
                    ->update($dosenData);
                
                DB::commit();
                
                return redirect()->route('koordinator.dataDosen')
                    ->with('success', 'Data dosen berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating dosen data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('koordinator.dataDosen')
                    ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Exception in updateDataDosen', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataDosen')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteDataDosen($id)
    {
        try {
            $dosen = DB::table('d_dosen')
                ->where('dosen_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$dosen) {
                return redirect()->route('koordinator.dataDosen')
                    ->with('error', 'Data dosen tidak ditemukan.');
            }
                
            DB::beginTransaction();
                
            try {
                DB::table('d_dosen')
                    ->where('dosen_id', $id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                    ]);
                    
                DB::table('d_user')
                    ->where('user_id', $dosen->user_id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                        'status' => 'Disabled'
                    ]);
                    
                    
                DB::commit();
                    
                return redirect()->route('koordinator.dataDosen')
                    ->with('success', 'Data dosen berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting dosen data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('koordinator.dataDosen')
                    ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Exception in deleteDataDosen', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataDosen')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
