<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DataMahasiswaController extends Controller
{
    public function getDataMahasiswa(Request $request){
        $search = $request->input('search'); 

        // 1. query untuk get semua mahasiswa
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

        // TAMBAHAN: Ambil bidang keahlian untuk setiap mahasiswa
        $mahasiswaIds = $mahasiswa->pluck('mahasiswa_id')->toArray();
        
        $bidangKeahlianMahasiswa = [];
        if (!empty($mahasiswaIds)) {
            $bidangKeahlianData = DB::table('t_mahasiswa_bidang_keahlian as mbk')
                ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
                ->whereIn('mbk.mahasiswa_id', $mahasiswaIds)
                ->whereNull('mbk.deleted_at')
                ->whereNull('bk.deleted_at')
                ->select('mbk.mahasiswa_id', 'bk.nama_bidang_keahlian', 'bk.bidang_keahlian_id')
                ->orderBy('bk.nama_bidang_keahlian', 'asc')
                ->get();

            // Group bidang keahlian by mahasiswa_id
            $bidangKeahlianMahasiswa = $bidangKeahlianData->groupBy('mahasiswa_id');
        }

        // 2. Query untuk partisipasi mahasiswa
        $searchPartisipasi = $request->input('search_partisipasi');

        // Query untuk mendapatkan mahasiswa yang berperan sebagai project leader
        $mahasiswaLeaderQuery = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->join('t_project_leader as leader', 'mahasiswa.mahasiswa_id', '=', 'leader.leader_id')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'mahasiswa.mahasiswa_id',
                'mahasiswa.nama_mahasiswa',
                'mahasiswa.nim_mahasiswa',
                'user.email',
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.status_proyek',
                DB::raw("'Project Leader' as role_type"),
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai'
            )
            ->where('leader.leader_type', 'Mahasiswa')
            ->where('proyek.status_proyek', 'In Progress')
            ->whereNull('mahasiswa.deleted_at')
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Tambahkan pencarian untuk leader
        if ($searchPartisipasi) {
            $mahasiswaLeaderQuery->where(function($q) use ($searchPartisipasi) {
                $q->where('mahasiswa.nama_mahasiswa', 'like', "%$searchPartisipasi%")
                ->orWhere('mahasiswa.nim_mahasiswa', 'like', "%$searchPartisipasi%")
                ->orWhere('user.email', 'like', "%$searchPartisipasi%")
                ->orWhere('proyek.nama_proyek', 'like', "%$searchPartisipasi%");
            });
        }

        // Query untuk mendapatkan mahasiswa yang berperan sebagai project member
        $mahasiswaMemberQuery = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->join('t_project_member_mahasiswa as member', 'mahasiswa.mahasiswa_id', '=', 'member.mahasiswa_id')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'mahasiswa.mahasiswa_id',
                'mahasiswa.nama_mahasiswa',
                'mahasiswa.nim_mahasiswa',
                'user.email',
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.status_proyek',
                DB::raw("'Anggota' as role_type"),
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai'
            )
            ->where('proyek.status_proyek', 'In Progress')
            ->whereNull('mahasiswa.deleted_at')
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Tambahkan pencarian untuk member
        if ($searchPartisipasi) {
            $mahasiswaMemberQuery->where(function($q) use ($searchPartisipasi) {
                $q->where('mahasiswa.nama_mahasiswa', 'like', "%$searchPartisipasi%")
                ->orWhere('mahasiswa.nim_mahasiswa', 'like', "%$searchPartisipasi%")
                ->orWhere('user.email', 'like', "%$searchPartisipasi%")
                ->orWhere('proyek.nama_proyek', 'like', "%$searchPartisipasi%");
            });
        }

        // ALTERNATIF 1: Menggunakan Collection merge (lebih reliable)
        $leaderResults = $mahasiswaLeaderQuery->get();
        $memberResults = $mahasiswaMemberQuery->get();
        
        // Gabungkan hasil dan urutkan
        $allResults = $leaderResults->concat($memberResults)
            ->sortBy('nama_mahasiswa')
            ->sortByDesc('role_type'); // Project Leader dulu, baru Anggota

        // Manual pagination untuk collection
        $perPage = 5;
        $currentPage = request()->get('partisipasi_page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedResults = $allResults->slice($offset, $perPage)->values();
        
        // Buat custom pagination
        $partisipasiMahasiswa = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedResults,
            $allResults->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'partisipasi_page',
            ]
        );

        $bidangKeahlian = DB::table('m_bidang_keahlian')
            ->whereNull('deleted_at')
            ->orderBy('nama_bidang_keahlian', 'asc')
            ->get();

        return view('pages.Koordinator.DataMahasiswa.kelola_data_mahasiswa', compact(
            'mahasiswa', 
            'search', 
            'partisipasiMahasiswa', 
            'searchPartisipasi',
            'bidangKeahlian',
            'bidangKeahlianMahasiswa'  // TAMBAHAN: Pass data bidang keahlian mahasiswa
        ), [
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
                    'github' => 'nullable|string|max:255',
                    'linkedin' => 'nullable|string|max:255',
                    'doc_cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                    'doc_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'doc_ktm' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ], 
                [
                    'email_mahasiswa.unique' => 'Email sudah terdaftar dalam sistem.',
                    'nim_mahasiswa.unique' => 'NIDN sudah terdaftar dalam sistem.',
                    'nama_mahasiswa.required' => 'Nama mahasiswa harus diisi.',
                    'nim_mahasiswa.required' => 'NIDN harus diisi.',
                    'email_mahasiswa.required' => 'Email harus diisi.',
                    'email_mahasiswa.email' => 'Format email tidak valid.',
                    'status_akun_mahasiswa.required' => 'Status harus diisi.',
                    'profile_img_mahasiswa.image' => 'File harus berupa gambar.',
                    'profile_img_mahasiswa.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                    'profile_img_mahasiswa.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                    'doc_cv.mimes' => 'Format CV tidak valid. Hanya pdf, doc, docx yang diperbolehkan.',
                    'doc_cv.max' => 'Ukuran CV terlalu besar. Maksimal 2MB.',
                    'doc_ktp.mimes' => 'Format KTP tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                    'doc_ktp.max' => 'Ukuran KTP terlalu besar. Maksimal 2MB.',
                    'doc_ktm.mimes' => 'Format KTM tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                    'doc_ktm.max' => 'Ukuran KTM terlalu besar. Maksimal 2MB.',
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
                    if (!empty($request->input('tanggal_lahir_mahasiswa'))) {
                        $tanggalLahir = date('Y-m-d', strtotime($request->input('tanggal_lahir_mahasiswa')));
                    }
                    
                    $mahasiswaData = [
                        'mahasiswa_id' => $mahasiswaId,
                        'user_id' => $userId,
                        'nama_mahasiswa' => $request->input('nama_mahasiswa'),
                        'nim_mahasiswa' => $request->input('nim_mahasiswa'),
                        'tanggal_lahir_mahasiswa' => $tanggalLahir,
                        'jenis_kelamin_mahasiswa' => $request->input('jenis_kelamin_mahasiswa') ?: null,
                        'telepon_mahasiswa' => $request->input('telepon_mahasiswa') ?: null,
                        'github' => $request->input('github') ?: null,
                        'linkedin' => $request->input('linkedin') ?: null,
                        'status_akun_mahasiswa' => $request->input('status_akun_mahasiswa', 'Active'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ];
                    
                    // Handle file uploads
                    $fileFields = [
                        'profile_img_mahasiswa' => 'uploads/profile_mahasiswa',
                        'doc_cv' => 'uploads/doc_cv',
                        'doc_ktp' => 'uploads/doc_ktp',
                        'doc_ktm' => 'uploads/doc_ktm'
                    ];

                    foreach ($fileFields as $fieldName => $uploadPath) {
                        if ($request->hasFile($fieldName)) {
                            $file = $request->file($fieldName);
                            
                            if ($file->isValid()) {
                                $fullUploadPath = public_path($uploadPath);
                                if (!is_dir($fullUploadPath)) {
                                    mkdir($fullUploadPath, 0777, true);
                                }
                                
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                if ($file->move($fullUploadPath, $filename)) {
                                    $mahasiswaData[$fieldName] = $uploadPath . '/' . $filename;
                                } else {
                                    throw new \Exception("Failed to move uploaded file: $fieldName");
                                }
                            } else {
                                throw new \Exception("Uploaded file is not valid: $fieldName");
                            }
                        }
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
                // Multiple mode implementation remains the same
                // ... existing multiple mode code
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
                
            if ($mahasiswaId) {
                $query->where('mahasiswa_id', '!=', $mahasiswaId);
            }
            
            $nimExists = $query->exists();
        }
        
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

            $rules = [
                'nama_mahasiswa' => 'required|string|max:255',
                'nim_mahasiswa' => 'required|string|max:10|regex:/^\d{10}$/',
                'email_mahasiswa' => 'required|email',
                'status_akun_mahasiswa' => 'required|in:Active,Rejected,Pending,Disabled', 
                'profile_img_mahasiswa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_mahasiswa' => 'nullable|string|max:20',
                'tanggal_lahir_mahasiswa' => 'nullable|date',
                'jenis_kelamin_mahasiswa' => 'nullable|in:Laki-Laki,Perempuan',
                'github' => 'nullable|string|max:255',
                'linkedin' => 'nullable|string|max:255',
                'doc_cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'doc_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'doc_ktm' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ];
            
            if ($mahasiswa->nim_mahasiswa != $request->input('nim_mahasiswa')) {
                $rules['nim_mahasiswa'] .= '|unique:d_mahasiswa,nim_mahasiswa,' . $id . ',mahasiswa_id';
            }
            
            if ($user->email != $request->input('email_mahasiswa')) {
                $rules['email_mahasiswa'] .= '|unique:d_user,email,' . $mahasiswa->user_id . ',user_id';
            }
            
            $messages = [
                'nim_mahasiswa.regex' => 'NIM harus berupa angka dan tepat 10 digit.',
                'nim_mahasiswa.max' => 'NIM maksimal 10 digit.',
                'nim_mahasiswa.unique' => 'NIM sudah terdaftar dalam sistem.',
                'email_mahasiswa.email' => 'Format email tidak valid.',
                'email_mahasiswa.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_mahasiswa.required' => 'Nama mahasiswa harus diisi.',
                'nim_mahasiswa.required' => 'NIM harus diisi.',
                'email_mahasiswa.required' => 'Email harus diisi.',
                'status_akun_mahasiswa.required' => 'Status harus diisi.',
                'profile_img_mahasiswa.image' => 'File harus berupa gambar.',
                'profile_img_mahasiswa.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_mahasiswa.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                'doc_cv.mimes' => 'Format CV tidak valid. Hanya pdf, doc, docx yang diperbolehkan.',
                'doc_cv.max' => 'Ukuran CV terlalu besar. Maksimal 2MB.',
                'doc_ktp.mimes' => 'Format KTP tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                'doc_ktp.max' => 'Ukuran KTP terlalu besar. Maksimal 2MB.',
                'doc_ktm.mimes' => 'Format KTM tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                'doc_ktm.max' => 'Ukuran KTM terlalu besar. Maksimal 2MB.',
            ];
            
            $validator = \Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }
            
            DB::beginTransaction();
            
            try {
                // Update user data
                $userData = [
                    'email' => $request->input('email_mahasiswa'),
                    'status' => $request->input('status_akun_mahasiswa'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                if ($mahasiswa->nim_mahasiswa != $request->input('nim_mahasiswa') || $request->filled('password_mahasiswa')) {
                    $userData['password'] = bcrypt($request->input('password_mahasiswa') ?: $request->input('nim_mahasiswa'));
                }
                
                DB::table('d_user')
                    ->where('user_id', $mahasiswa->user_id)
                    ->update($userData);
                
                // Inisialisasi data mahasiswa dengan data yang sudah ada
                $mahasiswaData = [
                    'nama_mahasiswa' => $request->input('nama_mahasiswa'),
                    'nim_mahasiswa' => $request->input('nim_mahasiswa'),
                    'tanggal_lahir_mahasiswa' => $request->filled('tanggal_lahir_mahasiswa') ? $request->input('tanggal_lahir_mahasiswa') : null,
                    'jenis_kelamin_mahasiswa' => $request->input('jenis_kelamin_mahasiswa'),
                    'telepon_mahasiswa' => $request->input('telepon_mahasiswa'),
                    'github' => $request->input('github'),
                    'linkedin' => $request->input('linkedin'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // Handle file uploads - PERBAIKAN UTAMA
                $fileFields = [
                    'profile_img_mahasiswa' => 'uploads/profile_mahasiswa',
                    'doc_cv' => 'uploads/doc_cv',
                    'doc_ktp' => 'uploads/doc_ktp',
                    'doc_ktm' => 'uploads/doc_ktm'
                ];

                foreach ($fileFields as $fieldName => $uploadPath) {
                    if ($request->hasFile($fieldName)) {
                        $file = $request->file($fieldName);
                        
                        \Log::info("Processing file upload for field: $fieldName", [
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'is_valid' => $file->isValid()
                        ]);
                        
                        // Validasi file lebih detail
                        if (!$file->isValid()) {
                            throw new \Exception("File $fieldName tidak valid atau rusak");
                        }
                        
                        // Cek ukuran file
                        if ($file->getSize() > 2048 * 1024) { // 2MB
                            throw new \Exception("Ukuran file $fieldName terlalu besar (maksimal 2MB)");
                        }
                        
                        // Pastikan direktori ada
                        $fullUploadPath = public_path($uploadPath);
                        if (!is_dir($fullUploadPath)) {
                            if (!mkdir($fullUploadPath, 0755, true)) {
                                throw new \Exception("Gagal membuat direktori upload: $uploadPath");
                            }
                        }
                        
                        // Generate nama file yang aman
                        $extension = strtolower($file->getClientOriginalExtension());
                        $filename = 'doc_mahasiswa_' . time() . '.' . $extension;
                        
                        // Pastikan ekstensi file aman
                        $allowedExtensions = [
                            'profile_img_mahasiswa' => ['jpg', 'jpeg', 'png', 'gif'],
                            'doc_cv' => ['pdf', 'doc', 'docx'],
                            'doc_ktp' => ['pdf', 'jpg', 'jpeg', 'png'],
                            'doc_ktm' => ['pdf', 'jpg', 'jpeg', 'png']
                        ];
                        
                        if (!in_array($extension, $allowedExtensions[$fieldName])) {
                            throw new \Exception("Format file $fieldName tidak diizinkan. Ekstensi: $extension");
                        }
                        
                        try {
                            // Upload file baru
                            $moved = $file->move($fullUploadPath, $filename);
                            
                            if (!$moved) {
                                throw new \Exception("Gagal memindahkan file $fieldName ke direktori tujuan");
                            }
                            
                            // Verifikasi file berhasil diupload
                            $newFilePath = $fullUploadPath . '/' . $filename;
                            if (!file_exists($newFilePath)) {
                                throw new \Exception("File $fieldName tidak ditemukan setelah upload");
                            }
                            
                            // Hapus file lama jika ada
                            $oldFile = $mahasiswa->{$fieldName};
                            if ($oldFile && $oldFile != '') {
                                $oldFilePath = public_path($oldFile);
                                if (file_exists($oldFilePath)) {
                                    unlink($oldFilePath);
                                    \Log::info("Old file deleted: $oldFilePath");
                                }
                            }
                            
                            // Set path file baru ke database - PERBAIKAN UTAMA
                            $relativePath = $uploadPath . '/' . $filename;
                            $mahasiswaData[$fieldName] = $relativePath;
                            
                            \Log::info("File uploaded successfully", [
                                'field' => $fieldName,
                                'filename' => $filename,
                                'full_path' => $newFilePath,
                                'relative_path' => $relativePath,
                                'file_exists' => file_exists($newFilePath)
                            ]);
                            
                        } catch (\Exception $uploadError) {
                            \Log::error("File upload error for field: $fieldName", [
                                'error' => $uploadError->getMessage(),
                                'trace' => $uploadError->getTraceAsString()
                            ]);
                            throw new \Exception("Gagal mengupload $fieldName: " . $uploadError->getMessage());
                        }
                    } else {
                        // Jika tidak ada file baru, pertahankan file lama
                        \Log::info("No new file for field: $fieldName, keeping existing file");
                    }
                }
                
                // Debug: Log data yang akan diupdate
                \Log::info("Data yang akan diupdate ke database:", [
                    'mahasiswa_id' => $id,
                    'data' => $mahasiswaData
                ]);
                
                // Update data mahasiswa ke database
                $updated = DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $id)
                    ->update($mahasiswaData);
                
                if (!$updated) {
                    // Cek apakah record masih ada
                    $checkRecord = DB::table('d_mahasiswa')->where('mahasiswa_id', $id)->first();
                    if (!$checkRecord) {
                        throw new \Exception("Record mahasiswa dengan ID $id tidak ditemukan");
                    }
                    
                    \Log::warning("No rows updated, but record exists", [
                        'mahasiswa_id' => $id,
                        'existing_data' => $checkRecord,
                        'update_data' => $mahasiswaData
                    ]);
                }
                
                // Verifikasi update berhasil dengan mengambil data terbaru
                $updatedMahasiswa = DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $id)
                    ->first();
                    
                \Log::info("Data setelah update:", [
                    'mahasiswa_id' => $id,
                    'profile_img_mahasiswa' => $updatedMahasiswa->profile_img_mahasiswa ?? 'NULL',
                    'doc_cv' => $updatedMahasiswa->doc_cv ?? 'NULL',
                    'doc_ktp' => $updatedMahasiswa->doc_ktp ?? 'NULL',
                    'doc_ktm' => $updatedMahasiswa->doc_ktm ?? 'NULL'
                ]);
                
                DB::commit();
                
                \Log::info("Mahasiswa data updated successfully", [
                    'mahasiswa_id' => $id,
                    'updated_by' => session('user_id'),
                    'files_updated' => array_keys(array_filter($mahasiswaData, function($key) {
                        return in_array($key, ['profile_img_mahasiswa', 'doc_cv', 'doc_ktp', 'doc_ktm']);
                    }, ARRAY_FILTER_USE_KEY))
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data mahasiswa berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()
                    ->with('success', 'Data mahasiswa berhasil diperbarui.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::error('Error updating mahasiswa data', [
                    'mahasiswa_id' => $id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString(),
                    'request_files' => $request->hasFile() ? array_keys($request->allFiles()) : [],
                    'request_data' => $request->except(['password_mahasiswa', '_token'])
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
                'mahasiswa_id' => $id ?? 'unknown',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
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

    public function getDataMahasiswaById(Request $request, $id){
        // Get data mahasiswa utama
        $mahasiswa = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->select('mahasiswa.*', 'user.*')
            ->where('mahasiswa.mahasiswa_id', $id)
            ->first();
            
        if (!$mahasiswa) {
            return redirect()->route('koordinator.dataMahasiswa')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // TAMBAHAN: Ambil data bidang keahlian
        $bidangKeahlian = DB::table('m_bidang_keahlian')
            ->whereNull('deleted_at')
            ->orderBy('nama_bidang_keahlian', 'asc')
            ->get();

        // TAMBAHAN: Ambil bidang keahlian yang sudah dipilih mahasiswa
        $selectedBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian as mbk')
            ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
            ->where('mbk.mahasiswa_id', $id)
            ->whereNull('mbk.deleted_at')
            ->whereNull('bk.deleted_at')
            ->select('bk.bidang_keahlian_id', 'bk.nama_bidang_keahlian')
            ->get();

        // Get riwayat proyek dengan pagination menggunakan UNION
        // Query untuk proyek sebagai leader
        $proyekLeaderQuery = DB::table('t_project_leader as leader')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai',
                'proyek.status_proyek',
                DB::raw("'Project Leader' as peran"),
                'leader.created_at as tanggal_bergabung'
            )
            ->where('leader.leader_id', $id)
            ->where('leader.leader_type', 'Mahasiswa')
            ->where('proyek.status_proyek', 'Done') 
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Query untuk proyek sebagai member
        $proyekMemberQuery = DB::table('t_project_member_mahasiswa as member')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'proyek.proyek_id',
                'proyek.nama_proyek', 
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai',
                'proyek.status_proyek',
                DB::raw("'Project Member' as peran"),
                'member.created_at as tanggal_bergabung'
            )
            ->where('member.mahasiswa_id', $id)
            ->where('proyek.status_proyek', 'Done') 
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Gabungkan kedua query dengan UNION dan buat pagination
        $combinedQuery = $proyekLeaderQuery->union($proyekMemberQuery);
        
        $riwayatProyek = DB::table(DB::raw("({$combinedQuery->toSql()}) as riwayat"))
            ->mergeBindings($combinedQuery)
            ->orderBy('riwayat.tanggal_selesai', 'desc')
            ->paginate(5, ['*'], 'riwayat_page'); 

        // Append mahasiswa_id ke pagination links
        $riwayatProyek->appends(['mahasiswa_id' => $id]);

        return view('pages.Koordinator.DataMahasiswa.detail_data_mahasiswa', compact(
            'mahasiswa', 
            'riwayatProyek', 
            'bidangKeahlian', 
            'selectedBidangKeahlian'
        ), [
            'titleSidebar' => 'Detail Mahasiswa'
        ]);
    }

    public function updateBidangKeahlianMahasiswa(Request $request, $id)
    {
        try {
            // Validasi mahasiswa exists
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan.'
                ], 404);
            }

            // Validasi input bidang keahlian
            $request->validate([
                'bidang_keahlian' => 'nullable|array',
                'bidang_keahlian.*' => 'exists:m_bidang_keahlian,bidang_keahlian_id'
            ], [
                'bidang_keahlian.array' => 'Format bidang keahlian tidak valid.',
                'bidang_keahlian.*.exists' => 'Bidang keahlian yang dipilih tidak valid.'
            ]);

            DB::beginTransaction();
            
            try {
                // Ambil bidang keahlian yang sudah ada untuk mahasiswa ini
                $existingBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian')
                    ->where('mahasiswa_id', $id)
                    ->whereNull('deleted_at')
                    ->pluck('bidang_keahlian_id')
                    ->toArray();

                // Bidang keahlian yang baru dipilih
                $newBidangKeahlian = $request->input('bidang_keahlian', []);

                // ANALISIS PERUBAHAN:
                // 1. Yang perlu dihapus = ada di existing tapi tidak ada di new
                $toDelete = array_diff($existingBidangKeahlian, $newBidangKeahlian);
                
                // 2. Yang perlu ditambah = ada di new tapi tidak ada di existing  
                $toInsert = array_diff($newBidangKeahlian, $existingBidangKeahlian);
                
                // 3. Yang tetap sama = ada di both (tidak perlu diapa-apakan)
                $unchanged = array_intersect($existingBidangKeahlian, $newBidangKeahlian);

                $deletedCount = 0;
                $insertedCount = 0;

                // HAPUS yang tidak dipilih lagi
                if (!empty($toDelete)) {
                    $deletedCount = DB::table('t_mahasiswa_bidang_keahlian')
                        ->where('mahasiswa_id', $id)
                        ->whereIn('bidang_keahlian_id', $toDelete)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'deleted_by' => session('user_id', 'system')
                        ]);
                }

                // TAMBAH yang baru dipilih
                if (!empty($toInsert)) {
                    foreach ($toInsert as $bidangKeahlianId) {
                        // Double check apakah bidang keahlian valid
                        $bidangKeahlianExists = DB::table('m_bidang_keahlian')
                            ->where('bidang_keahlian_id', $bidangKeahlianId)
                            ->whereNull('deleted_at')
                            ->exists();
                            
                        if ($bidangKeahlianExists) {
                            DB::table('t_mahasiswa_bidang_keahlian')->insert([
                                'mahasiswa_bidang_keahlian_id' => Str::uuid(),
                                'mahasiswa_id' => $id,
                                'bidang_keahlian_id' => $bidangKeahlianId,
                                'created_at' => now(),
                                'created_by' => session('user_id', 'system')
                            ]);
                            $insertedCount++;
                        }
                    }
                }

                DB::commit();

                // Log activity dengan detail lengkap
                \Log::info('Bidang keahlian mahasiswa updated successfully', [
                    'mahasiswa_id' => $id,
                    'existing_count' => count($existingBidangKeahlian),
                    'new_count' => count($newBidangKeahlian),
                    'unchanged_count' => count($unchanged),
                    'deleted_count' => $deletedCount,
                    'inserted_count' => $insertedCount,
                    'to_delete' => $toDelete,
                    'to_insert' => $toInsert,
                    'unchanged' => $unchanged,
                    'updated_by' => session('user_id', 'system')
                ]);

                // Buat message yang lebih informatif
                $message = "Bidang keahlian berhasil diperbarui.";
                $details = [];
                
                if ($insertedCount > 0) {
                    $details[] = "{$insertedCount} bidang keahlian ditambahkan";
                }
                if ($deletedCount > 0) {
                    $details[] = "{$deletedCount} bidang keahlian dihapus";
                }
                if (count($unchanged) > 0) {
                    $details[] = count($unchanged) . " bidang keahlian tetap";
                }
                
                if (!empty($details)) {
                    $message .= " (" . implode(', ', $details) . ")";
                }

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'data' => [
                        'mahasiswa_id' => $id,
                        'total_bidang_keahlian' => count($newBidangKeahlian),
                        'changes' => [
                            'inserted' => $insertedCount,
                            'deleted' => $deletedCount,
                            'unchanged' => count($unchanged)
                        ]
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::error('Error updating bidang keahlian mahasiswa', [
                    'mahasiswa_id' => $id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui bidang keahlian: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Exception in updateBidangKeahlianMahasiswa', [
                'mahasiswa_id' => $id ?? 'unknown',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBidangKeahlianMahasiswa($id)
    {
        try {
            $bidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian as mbk')
                ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
                ->where('mbk.mahasiswa_id', $id)
                ->whereNull('mbk.deleted_at')
                ->whereNull('bk.deleted_at')
                ->select('bk.bidang_keahlian_id', 'bk.nama_bidang_keahlian')
                ->orderBy('bk.nama_bidang_keahlian', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $bidangKeahlian
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting bidang keahlian mahasiswa', [
                'mahasiswa_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data bidang keahlian.'
            ], 500);
        }
    }
}