<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMahasiswaController extends Controller
{
    public function getDataMahasiswa(Request $request){
        $search = $request->input('search'); 

        $query = DB::table ('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->select('mahasiswa.*', 'user.*')
            ->whereNull('mahasiswa.deleted_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mahasiswa.nama_mahasiswa', 'like', "%$search%")
                ->orWhere('mahasiswa.nim_mahasiswa', 'like', "%$search%")
                ->orWhere('user.email', 'like', "%$search%");
            });
        }

        $mahasiswa = $query->orderBy('user.created_at', 'desc')->paginate(10); 
        return view('pages.Koordinator.data_mahasiswa', compact('mahasiswa', 'search'), [
            'titleSidebar' => 'Data Mahasiswa'
        ]);
    }

    public function tambahDataMahasiswa(Request $request){
        try {
            $isSingle = $request->input('is_single') === '1';
            
            if ($isSingle) {
                $request->validate([
                    'nama_mahasiswa' => 'required|string|max:255',
                    'nim_mahasiswa' => 'required|string|unique:d_mahasiswa,nim_mahasiswa',
                    'email_mahasiswa' => 'required|email|unique:d_user,email',
                    'status_akun_mahasiswa' => 'required|in:Active,Rejected,Pending,Disabled', 
                    'profile_img_mahasiswa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                    'telepon_mahasiswa' => 'nullable|string|max:20',
                    'tanggal_lahir_mahasiswa' => 'nullable|date',
                    'jenis_kelamin_mahasiswa' => 'nullable|in:Laki-Laki,Perempuan',
                ], 
                [
                    'email_mahasiswa.unique' => 'Email sudah terdaftar dalam sistem.',
                    'nim_mahasiswa.unique' => 'NIDN sudah terdaftar dalam sistem.',
                    'nama_mahasiswa.required' => 'Nama mahasiswa harus diisi.',
                    'nim_mahasiswa.required' => 'NIDN harus diisi.',
                    'email_mahasiswa.required' => 'Email harus diisi.',
                    'email_mahasiswa.email' => 'Format email tidak valid.',
                    'status_akun_mahasiswa.required' => 'Status harus diisi.',
                    'email_mahasiswa.email' => 'Format email tidak valid.',
                    'profile_img_mahasiswa.image' => 'File harus berupa gambar.',
                    'profile_img_mahasiswa.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                    'profile_img_mahasiswa.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                ]);
    
                $nimExists = DB::table('d_mahasiswa')->where('nim_mahasiswa', $request->input('nim_mahasiswa'))->exists();
                $emailExists = DB::table('d_user')->where('email', $request->input('email_mahasiswa'))->exists();
                
                if ($nimExists) {
                    return back()->withInput()->withErrors(['nim_mahasiswa' => 'NIDN sudah ada di daftar data mahasiswa.']);
                } else if ($emailExists) {
                    return back()->withInput()->withErrors(['email_mahasiswa' => 'Email sudah ada di daftar data mahasiswa.']);
                }
                
                $userId = Str::uuid();
                $mahasiswaId = Str::uuid();
                DB::beginTransaction();
                
                try {
                    DB::table('d_user')->insert([
                        'user_id' => $userId,
                        'email' => $request->input('email_mahasiswa'),
                        'password' => bcrypt($request->input('password_mahasiswa') ?: $request->input('nim_mahasiswa')),
                        'role' => 'Mahasiswa',
                        'status' => $request->input('status_akun_mahasiswa', 'Active'), 
                        'created_by' => session('user_id'),
                    ]);

                    $tanggalLahir = null;
                    if (!empty($mahasiswa['tanggal_lahir_mahasiswa'])) {
                        $tanggalLahir = date('Y-m-d', strtotime($mahasiswa['tanggal_lahir_mahasiswa']));
                    }
                    
                    $mahasiswaData = [
                        'mahasiswa_id' => $mahasiswaId,
                        'user_id' => $userId,
                        'nama_mahasiswa' => $request->input('nama_mahasiswa'),
                        'nim_mahasiswa' => $request->input('nim_mahasiswa'),
                        'tanggal_lahir_mahasiswa' => $tanggalLahir,
                        'jenis_kelamin_mahasiswa' => $request->input('jenis_kelamin_mahasiswa') ? $request->input('jenis_kelamin_mahasiswa') : null,
                        'telepon_mahasiswa' => $request->input('telepon_mahasiswa') ? $request->input('telepon_mahasiswa') : null,                        'status_akun_mahasiswa' => $request->input('status_akun_mahasiswa', 'Active'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ];
                    
                    if ($request->hasFile('profile_img_mahasiswa')) {
                        $file = $request->file('profile_img_mahasiswa');
                        
                        if ($file->isValid()) {
                            $uploadPath = public_path('uploads/profile_mahasiswa');
                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }
                            
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            
                            try {
                                if ($file->move($uploadPath, $filename)) {
                                    $mahasiswaData['profile_img_mahasiswa'] = 'uploads/profile_mahasiswa/' . $filename;
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
                            'has_file' => $request->hasFile('profile_img_mahasiswa'),
                            'request_keys' => $request->keys()
                        ]);
                    }
                    
                    DB::table('d_mahasiswa')->insert($mahasiswaData);
                    DB::commit();
                    return redirect()->route('koordinator.dataMahasiswa')->with('success', 'Data mahasiswa berhasil ditambahkan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding mahasiswa data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataMahasiswa')->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
                }
            } else {
                $mahasiswaData = json_decode($request->input('mahasiswa_data'), true);
                if (empty($mahasiswaData)) {
                    return redirect()->route('koordinator.dataMahasiswa')->with('error', 'Tidak ada data mahasiswa untuk ditambahkan.');
                }
                
                DB::beginTransaction();
                try {
                    $insertedCount = 0;
                    $errors = [];
                    
                    foreach ($mahasiswaData as $index => $mahasiswa) {
                        if (empty($mahasiswa['nama_mahasiswa']) || empty($mahasiswa['nim_mahasiswa']) || empty($mahasiswa['email_mahasiswa'])) {
                            array_push($errors, 'Data mahasiswa tidak lengkap: ' . ($mahasiswa['nama_mahasiswa'] ?? 'Unnamed'));
                            continue;
                        }
                        
                        $nimExists = DB::table('d_mahasiswa')->where('nim_mahasiswa', $mahasiswa['nim_mahasiswa'])->exists();
                        $emailExists = DB::table('d_user')->where('email', $mahasiswa['email_mahasiswa'])->exists();
                        
                        if ($nimExists) {
                            array_push($errors, 'NIDN ' . $mahasiswa['nim_mahasiswa'] . ' sudah terdaftar.');
                            continue;
                        }
                        
                        if ($emailExists) {
                            array_push($errors, 'Email ' . $mahasiswa['email_mahasiswa'] . ' sudah terdaftar.');
                            continue;
                        }
                        
                        $userId = Str::uuid();
                        $mahasiswaId = Str::uuid();
                        
                        DB::table('d_user')->insert([
                            'user_id' => $userId,
                            'email' => $mahasiswa['email_mahasiswa'],
                            'password' => bcrypt($mahasiswa['password_mahasiswa'] ?: $request->input('nim_mahasiswa')), 
                            'role' => 'Mahasiswa',
                            'status' => $mahasiswa['status_akun_mahasiswa'] ?? 'Active', 
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ]);

                        $tanggalLahir = null;
                        if (!empty($mahasiswa['tanggal_lahir_mahasiswa'])) {
                            $tanggalLahir = date('Y-m-d', strtotime($mahasiswa['tanggal_lahir_mahasiswa']));
                        }
                        
                        $mahasiswaRecord = [
                            'mahasiswa_id' => $mahasiswaId,
                            'user_id' => $userId,
                            'nama_mahasiswa' => $mahasiswa['nama_mahasiswa'],
                            'nim_mahasiswa' => $mahasiswa['nim_mahasiswa'],
                            'tanggal_lahir_mahasiswa' => $tanggalLahir,
                            'jenis_kelamin_mahasiswa' => $mahasiswa['jenis_kelamin_mahasiswa'] ?? null,
                            'telepon_mahasiswa' => $mahasiswa['telepon_mahasiswa'] ?? null,
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ];
                        
                        $fileKey = "profile_img_mahasiswa_{$index}";
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
                                $uploadPath = public_path('uploads/profile_mahasiswa');
                                if (!is_dir($uploadPath)) {
                                    mkdir($uploadPath, 0777, true);
                                }
                                
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                if ($file->move($uploadPath, $filename)) {
                                    $mahasiswaRecord['profile_img_mahasiswa'] = 'uploads/profile_mahasiswa/' . $filename;
                                    
                                    \Log::info('File upload success for multiple mode', [
                                        'index' => $index,
                                        'path' => $mahasiswaRecord['profile_img_mahasiswa']
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
                        } else if (isset($mahasiswa['has_profile_img']) && $mahasiswa['has_profile_img']) {
                            \Log::warning("File flag set but no file found for index {$index}", [
                                'file_key' => $fileKey
                            ]);
                        }

                        DB::table('d_mahasiswa')->insert($mahasiswaRecord);       
                        $insertedCount++;
                    }
                    
                    DB::commit();
                    
                    if (count($errors) > 0) {
                        $errorMessage = implode('<br>', $errors);
                        return redirect()->route('koordinator.dataMahasiswa')
                            ->with('warning', "$insertedCount data mahasiswa berhasil ditambahkan.<br>Beberapa error terjadi:<br>$errorMessage");
                    }
                    
                    return redirect()->route('koordinator.dataMahasiswa')
                        ->with('success', "$insertedCount data mahasiswa berhasil ditambahkan.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding multiple mahasiswa data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataMahasiswa')
                        ->with('error', 'Gagal menambahkan data mahasiswa: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Exception in tambahDataMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataMahasiswa')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function checkEmailNimExists(Request $request){
        \Log::info('Check email/nim request:', $request->all());
        
        $email = $request->input('email_mahasiswa');
        $nim = $request->input('nim_mahasiswa');
        $mahasiswaId = $request->input('mahasiswa_id');
        
        $emailExists = false;
        $nimExists = false;
        
        if ($email) {
            $query = DB::table('d_user')
                ->where('email', $email);
                
            // Exclude current mahasiswa when checking for duplicates
            if ($mahasiswaId) {
                $mahasiswa = DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->first();
                    
                if ($mahasiswa) {
                    $query->where('user_id', '!=', $mahasiswa->user_id);
                }
            }
            
            $emailExists = $query->exists();
        }
        
        if ($nim) {
            $query = DB::table('d_mahasiswa')
                ->where('nim_mahasiswa', $nim);
                
            // Exclude current mahasiswa when checking for duplicates
            if ($mahasiswaId) {
                $query->where('mahasiswa_id', '!=', $mahasiswaId);
            }
            
            $nimExists = $query->exists();
        }
        
        // Log hasil untuk debugging
        \Log::info('Check result:', [
            'email' => $email,
            'nim' => $nim,
            'mahasiswaId' => $mahasiswaId,
            'emailExists' => $emailExists,
            'nimExists' => $nimExists
        ]);
        
        return response()->json([
            'emailExists' => $emailExists,
            'nimExists' => $nimExists
        ]);
    }

    public function updateDataMahasiswa(Request $request, $id){
        try {
            // Get current mahasiswa data
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $id)
                ->first();
                
            if (!$mahasiswa) {
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Data mahasiswa tidak ditemukan.');
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $mahasiswa->user_id)
                ->first();
    
            // Buat aturan validasi custom
            $rules = [
                'nama_mahasiswa' => 'required|string|max:255',
                'nim_mahasiswa' => 'required|string|max:10|regex:/^\d{10}$/',
                'email_mahasiswa' => 'required|email',
                'status_akun_mahasiswa' => 'required|in:Active,Rejected,Pending,Disabled', 
                'profile_img_mahasiswa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_mahasiswa' => 'nullable|string|max:20',
                'tanggal_lahir_mahasiswa' => 'nullable|date',
                'jenis_kelamin_mahasiswa' => 'nullable|in:Laki-Laki,Perempuan',
            ];
            
            // Tambahkan aturan unique hanya jika nilai berubah
            if ($mahasiswa->nim_mahasiswa != $request->input('nim_mahasiswa')) {
                $rules['nim_mahasiswa'] .= '|unique:d_mahasiswa,nim_mahasiswa,' . $id . ',mahasiswa_id';
            }
            
            if ($user->email != $request->input('email_mahasiswa')) {
                $rules['email_mahasiswa'] .= '|unique:d_user,email,' . $mahasiswa->user_id . ',user_id';
            }
            
            // Custom pesan error
            $messages = [
                'nim_mahasiswa.regex' => 'NIDN harus berupa angka dan tepat 10 digit.',
                'nim_mahasiswa.max' => 'NIDN maksimal 10 digit.',
                'nim_mahasiswa.unique' => 'NIDN sudah terdaftar dalam sistem.',
                'email_mahasiswa.email' => 'Format email tidak valid.',
                'email_mahasiswa.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_mahasiswa.required' => 'Nama mahasiswa harus diisi.',
                'nim_mahasiswa.required' => 'NIDN harus diisi.',
                'email_mahasiswa.required' => 'Email harus diisi.',
                'status_akun_mahasiswa.required' => 'Status harus diisi.',
                'profile_img_mahasiswa.image' => 'File harus berupa gambar.',
                'profile_img_mahasiswa.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_mahasiswa.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
            ];
            
            // Validasi dengan aturan kustom
            $validator = \Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                // Penting: Kembalikan response dengan format JSON untuk AJAX
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }
            
            // Proses selanjutnya tetap sama
            DB::beginTransaction();
            
            try {
                // Update user data
                $userData = [
                    'email' => $request->input('email_mahasiswa'),
                    'status' => $request->input('status_akun_mahasiswa'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // If NIDN changed or password provided, update password
                if ($mahasiswa->nim_mahasiswa != $request->input('nim_mahasiswa') || $request->filled('password_mahasiswa')) {
                    $userData['password'] = bcrypt($request->input('password_mahasiswa') ?: $request->input('nim_mahasiswa'));
                }
                
                DB::table('d_user')
                    ->where('user_id', $mahasiswa->user_id)
                    ->update($userData);
                
                // Update mahasiswa data
                $mahasiswaData = [
                    'nama_mahasiswa' => $request->input('nama_mahasiswa'),
                    'nim_mahasiswa' => $request->input('nim_mahasiswa'),
                    'tanggal_lahir_mahasiswa' => $request->filled('tanggal_lahir_mahasiswa') ? $request->input('tanggal_lahir_mahasiswa') : null,
                    'jenis_kelamin_mahasiswa' => $request->input('jenis_kelamin_mahasiswa'),
                    'telepon_mahasiswa' => $request->input('telepon_mahasiswa'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // Handle profile image upload
                if ($request->hasFile('profile_img_mahasiswa')) {
                    $file = $request->file('profile_img_mahasiswa');
                    
                    if ($file->isValid()) {
                        $uploadPath = public_path('uploads/profile_mahasiswa');
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        
                        if ($file->move($uploadPath, $filename)) {
                            // Delete old image if exists
                            if ($mahasiswa->profile_img_mahasiswa) {
                                $oldImagePath = public_path($mahasiswa->profile_img_mahasiswa);
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            
                            $mahasiswaData['profile_img_mahasiswa'] = 'uploads/profile_mahasiswa/' . $filename;
                        } else {
                            throw new \Exception("Failed to move uploaded file");
                        }
                    } else {
                        throw new \Exception("Uploaded file is not valid");
                    }
                }
                
                DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $id)
                    ->update($mahasiswaData);
                
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data mahasiswa berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('success', 'Data mahasiswa berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating mahasiswa data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                    ], 500);
                }
                
                return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Exception in updateDataMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    public function deleteDataMahasiswa($id){
        try {
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$mahasiswa) {
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Data mahasiswa tidak ditemukan.');
            }
                
            DB::beginTransaction();
                
            try {
                DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                    ]);
                    
                DB::table('d_user')
                    ->where('user_id', $mahasiswa->user_id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                        'status' => 'Disabled'
                    ]);
                    
                    
                DB::commit();
                    
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('success', 'Data mahasiswa berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting mahasiswa data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Exception in deleteDataMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataMahasiswa')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDataMahasiswaById($id){
        $mahasiswa = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->select('mahasiswa.*', 'user.*')
            ->where('mahasiswa.mahasiswa_id', $id)
            ->first();
            
        if (!$mahasiswa) {
            return redirect()->route('koordinator.dataMahasiswa')->with('error', 'Data mahasiswa tidak ditemukan.');
        }
        
        return view('pages.Koordinator.DataMahasiswa.detail_data_mahasiswa', compact('mahasiswa'), [
            'titleSidebar' => 'Detail Mahasiswa'
        ]);
    }
}
