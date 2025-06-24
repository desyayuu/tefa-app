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


        $memberResults = $mahasiswaMemberQuery->get();
        
        // Manual pagination untuk collection
        $perPage = 5;
        $currentPage = request()->get('partisipasi_page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedResults = $memberResults->slice($offset, $perPage)->values();
        
        // Buat custom pagination
        $partisipasiMahasiswa = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedResults,
            $memberResults->count(),
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
            'bidangKeahlianMahasiswa' 
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
                'deskripsi_diri' => 'nullable|string',
                'kelebihan_diri' => 'nullable|string',
                'kekurangan_diri' => 'nullable|string',
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
                    'deskripsi_diri' => $request->input('deskripsi_diri'),
                    'kelebihan_diri' => $request->input('kelebihan_diri'),
                    'kekurangan_diri' => $request->input('kekurangan_diri'),
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

    public function checkMahasiswaDeletable($mahasiswaId)
    {
        $constraints = [];
        $blockStatus = ['Initiation', 'In Progress'];

        // 1. Cek apakah mahasiswa masih menjadi project leader di proyek yang In Progress
        $activeAsLeader = DB::table('t_project_leader as leader')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->where('leader.leader_id', $mahasiswaId)
            ->where('leader.leader_type', 'Mahasiswa')
            ->whereIn('proyek.status_proyek', $blockStatus)
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at')
            ->select('proyek.nama_proyek', 'proyek.proyek_id')
            ->get();

        if ($activeAsLeader->isNotEmpty()) {
            $constraints['active_as_leader'] = [
                'status' => false,
                'message' => 'Mahasiswa masih menjadi Project Leader di proyek yang sedang berjalan',
                'details' => $activeAsLeader->pluck('nama_proyek')->toArray(),
                'count' => $activeAsLeader->count()
            ];
        }

        // 2. Cek apakah mahasiswa masih menjadi member di proyek yang In Progress
        $activeAsMember = DB::table('t_project_member_mahasiswa as member')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->where('member.mahasiswa_id', $mahasiswaId)
            ->whereIn('proyek.status_proyek', $blockStatus)
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at')
            ->select('proyek.nama_proyek', 'proyek.proyek_id')
            ->get();

        if ($activeAsMember->isNotEmpty()) {
            $constraints['active_as_member'] = [
                'status' => false,
                'message' => 'Mahasiswa masih menjadi anggota di proyek yang sedang berjalan',
                'details' => $activeAsMember->pluck('nama_proyek')->toArray(),
                'count' => $activeAsMember->count()
            ];
        }

        return [
            'deletable' => empty($constraints),
            'constraints' => $constraints
        ];
    }

    public function getRelatedDataSummary($mahasiswaId)
    {
        $summary = [];

        // 1. Portofolio mahasiswa
        $portofolioCount = DB::table('d_portofolio')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->count();

        if ($portofolioCount > 0) {
            $summary['portofolio'] = [
                'count' => $portofolioCount,
                'description' => 'data portofolio'
            ];
        }

        // 2. Bidang Keahlian mahasiswa
        $bidangKeahlianCount = DB::table('t_mahasiswa_bidang_keahlian')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->count();

        if ($bidangKeahlianCount > 0) {
            $summary['bidang_keahlian'] = [
                'count' => $bidangKeahlianCount,
                'description' => 'bidang keahlian'
            ];
        }

        // 3. Bahasa yang dikuasai mahasiswa
        $bahasaPemrogramanCount = DB::table('t_mahasiswa_bahasa_pemrograman')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->count();
        
        if ($bahasaPemrogramanCount > 0) {
            $summary['bahasa_pemrograman'] = [
                'count' => $bahasaPemrogramanCount,
                'description' => 'bahasa pemrograman'
            ];
        }


        // 4. Tools yang dikuasai mahasiswa
        $toolsCount = DB::table('t_mahasiswa_tools')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->count();
        if ($toolsCount > 0) {
            $summary['tools'] = [
                'count' => $toolsCount,
                'description' => 'tools'
            ];
        }
        return $summary;
    }

    protected function cascadeDeleteRelatedData($mahasiswaId, $deletedBy)
    {
        $deletedData = [];

        try {
            // 1. Soft delete portofolio mahasiswa
            $portofolioDeleted = DB::table('d_portofolio')
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $deletedBy
                ]);

            if ($portofolioDeleted > 0) {
                $deletedData['portofolio'] = $portofolioDeleted;
            }

            // 2. Soft delete bidang keahlian mahasiswa
            $bidangKeahlianDeleted = DB::table('t_mahasiswa_bidang_keahlian')
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $deletedBy
                ]);

            if ($bidangKeahlianDeleted > 0) {
                $deletedData['bidang_keahlian'] = $bidangKeahlianDeleted;
            }

            return $deletedData;

        } catch (\Exception $e) {
            \Log::error('Error in cascade delete related data', [
                'mahasiswa_id' => $mahasiswaId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteDataMahasiswa($id)
    {
        try {
            // 1. Validasi mahasiswa exists
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$mahasiswa) {
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Data mahasiswa tidak ditemukan.');
            }

            // 2. Check apakah mahasiswa bisa dihapus
            $deletableCheck = $this->checkMahasiswaDeletable($id);
            
            if (!$deletableCheck['deletable']) {
                $constraints = $deletableCheck['constraints'];
                $errorMessages = [];
                
                foreach ($constraints as $constraint) {
                    $errorMessages[] = $constraint['message'];
                    if (isset($constraint['details']) && !empty($constraint['details'])) {
                        $errorMessages[] = '- Proyek: ' . implode(', ', $constraint['details']);
                    }
                }
                
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Tidak dapat menghapus mahasiswa. ' . implode(' ', $errorMessages));
            }

            // 3. Get summary data yang akan terhapus
            $relatedDataSummary = $this->getRelatedDataSummary($id);

            DB::beginTransaction();
                
            try {
                // 4. Cascade delete related data
                $deletedRelatedData = $this->cascadeDeleteRelatedData($id, session('user_id'));

                // 5. Soft delete mahasiswa
                DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                    ]);
                    
                // 6. Soft delete user account
                DB::table('d_user')
                    ->where('user_id', $mahasiswa->user_id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                        'status' => 'Disabled'
                    ]);
                    
                DB::commit();

                // 7. Logging
                \Log::info('Mahasiswa deleted successfully with cascade operations', [
                    'mahasiswa_id' => $id,
                    'mahasiswa_name' => $mahasiswa->nama_mahasiswa,
                    'deleted_related_data' => $deletedRelatedData
                ]);

                // 8. Success message
                $successMessage = "Data mahasiswa {$mahasiswa->nama_mahasiswa} berhasil dihapus";
                $additionalInfo = [];
                
                if (isset($deletedRelatedData['portofolio']) && $deletedRelatedData['portofolio'] > 0) {
                    $additionalInfo[] = "{$deletedRelatedData['portofolio']} portofolio";
                }
                if (isset($deletedRelatedData['bidang_keahlian']) && $deletedRelatedData['bidang_keahlian'] > 0) {
                    $additionalInfo[] = "{$deletedRelatedData['bidang_keahlian']} bidang keahlian";
                }
                
                if (!empty($additionalInfo)) {
                    $successMessage .= " beserta " . implode(' dan ', $additionalInfo);
                }
                $successMessage .= ".";
                    
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('success', $successMessage);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting mahasiswa data with cascade', [
                    'mahasiswa_id' => $id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->route('koordinator.dataMahasiswa')
                    ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in deleteDataMahasiswa', [
                'message' => $e->getMessage()
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

        // Ambil data bidang keahlian
        $bidangKeahlian = DB::table('m_bidang_keahlian')
            ->whereNull('deleted_at')
            ->orderBy('nama_bidang_keahlian', 'asc')
            ->get();

        // Ambil bidang keahlian yang sudah dipilih mahasiswa
        $selectedBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian as mbk')
            ->join('m_bidang_keahlian as bk', 'mbk.bidang_keahlian_id', '=', 'bk.bidang_keahlian_id')
            ->where('mbk.mahasiswa_id', $id)
            ->whereNull('mbk.deleted_at')
            ->whereNull('bk.deleted_at')
            ->select('bk.bidang_keahlian_id', 'bk.nama_bidang_keahlian')
            ->get();

        $bahasaPemrograman = DB::table('m_bahasa_pemrograman')
            ->whereNull('deleted_at')
            ->orderBy('nama_bahasa_pemrograman', 'asc')
            ->get();
        
        $selectedBahasaPemrograman = DB::table('t_mahasiswa_bahasa_pemrograman as mbp')
            ->join('m_bahasa_pemrograman as bp', 'mbp.bahasa_pemrograman_id', '=', 'bp.bahasa_pemrograman_id')
            ->where('mbp.mahasiswa_id', $id)
            ->whereNull('mbp.deleted_at')
            ->whereNull('bp.deleted_at')
            ->select('bp.bahasa_pemrograman_id', 'bp.nama_bahasa_pemrograman')
            ->get();

        $tools = DB::table('m_tools')
            ->whereNull('deleted_at')
            ->orderBy('nama_tool', 'asc')
            ->get();

        // Ambil tools yang sudah dipilih mahasiswa (termasuk custom tools)
        $selectedToolsQuery = DB::table('t_mahasiswa_tools as mt')
            ->where('mt.mahasiswa_id', $id)
            ->whereNull('mt.deleted_at')
            ->select(
                'mt.tool_id',
                'mt.custom_nama_tool',
                'mt.custom_deskripsi_tool',
                'm_tools.nama_tool'
            )
            ->leftJoin('m_tools', 'mt.tool_id', '=', 'm_tools.tool_id')
            ->get();

        // Transform selected tools untuk frontend
        $selectedTools = $selectedToolsQuery->map(function($tool) {
            if ($tool->tool_id) {
                // Tool dari master data
                return [
                    'tool_id' => $tool->tool_id,
                    'nama_tool' => $tool->nama_tool,
                    'is_custom' => false
                ];
            } else {
                // Custom tool
                return [
                    'tool_id' => null,
                    'nama_tool' => $tool->custom_nama_tool,
                    'custom_nama_tool' => $tool->custom_nama_tool,
                    'custom_deskripsi_tool' => $tool->custom_deskripsi_tool,
                    'is_custom' => true
                ];
            }
        });

        // TAMBAHAN: Get riwayat proyek (existing code)
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

        $combinedQuery = $proyekLeaderQuery->union($proyekMemberQuery);
        $riwayatProyek = DB::table(DB::raw("({$combinedQuery->toSql()}) as riwayat"))
            ->mergeBindings($combinedQuery)
            ->orderBy('riwayat.tanggal_selesai', 'desc')
            ->paginate(5, ['*'], 'riwayat_page'); 

        $riwayatProyek->appends(['mahasiswa_id' => $id]);

        //Get data portofolio mahasiswa dengan pagination
        $searchPortofolio = $request->input('search_portofolio');
        
        $portofolioQuery = DB::table('d_portofolio')
            ->where('mahasiswa_id', $id)
            ->whereNull('deleted_at');
            
        if ($searchPortofolio) {
            $portofolioQuery->where(function($q) use ($searchPortofolio) {
                $q->where('nama_kegiatan', 'like', "%$searchPortofolio%")
                ->orWhere('jenis_kegiatan', 'like', "%$searchPortofolio%")
                ->orWhere('penyelenggara', 'like', "%$searchPortofolio%")
                ->orWhere('peran_dalam_kegiatan', 'like', "%$searchPortofolio%");
            });
        }
        
        $portofolioMahasiswa = $portofolioQuery
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'portofolio_page');
            
        $portofolioMahasiswa->appends(['mahasiswa_id' => $id, 'search_portofolio' => $searchPortofolio]);

        return view('pages.Koordinator.DataMahasiswa.detail_data_mahasiswa', compact(
            'mahasiswa', 
            'riwayatProyek', 
            'bidangKeahlian', 
            'selectedBidangKeahlian',
            'portofolioMahasiswa',  
            'searchPortofolio', 
            'bahasaPemrograman',
            'selectedBahasaPemrograman', 
            'tools',
            'selectedTools'
        ), [
            'titleSidebar' => 'Detail Mahasiswa'
        ]);
    }


    public function tambahPortofolioMahasiswa(Request $request)
    {
        try {
            $request->validate([
                'mahasiswa_id' => 'required|exists:d_mahasiswa,mahasiswa_id',
                'nama_kegiatan' => 'required|string|max:255',
                'jenis_kegiatan' => 'required|in:Magang,Pelatihan,Lomba,Penelitian,Pengabdian,Lainnya',
                'deskripsi_kegiatan' => 'nullable|string',
                'penyelenggara' => 'nullable|string|max:255',
                'tingkat_kegiatan' => 'required|in:Internasional,Nasional,Regional,Lainnya',
                'link_kegiatan' => 'nullable|url|max:255',
                'peran_dalam_kegiatan' => 'nullable|string|max:255',
            ], [
                'mahasiswa_id.required' => 'Data mahasiswa tidak valid.',
                'mahasiswa_id.exists' => 'Data mahasiswa tidak ditemukan.',
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi.',
                'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih.',
                'tingkat_kegiatan.required' => 'Tingkat kegiatan harus dipilih.',
                'link_kegiatan.url' => 'Format link tidak valid.',
            ]);

            DB::beginTransaction();
            
            try {
                $portofolioId = Str::uuid();
                
                $portofolioData = [
                    'portofolio_id' => $portofolioId,
                    'mahasiswa_id' => $request->input('mahasiswa_id'),
                    'nama_kegiatan' => $request->input('nama_kegiatan'),
                    'jenis_kegiatan' => $request->input('jenis_kegiatan'),
                    'deskripsi_kegiatan' => $request->input('deskripsi_kegiatan'),
                    'penyelenggara' => $request->input('penyelenggara'),
                    'tingkat_kegiatan' => $request->input('tingkat_kegiatan'),
                    'link_kegiatan' => $request->input('link_kegiatan'),
                    'peran_dalam_kegiatan' => $request->input('peran_dalam_kegiatan'),
                    'created_at' => now(),
                    'created_by' => session('user_id'),
                ];

                DB::table('d_portofolio')->insert($portofolioData);
                
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data portofolio berhasil ditambahkan.'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Data portofolio berhasil ditambahkan.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error adding portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in tambahPortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function updatePortofolioMahasiswa(Request $request, $id)
    {
        try {
            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$portofolio) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data portofolio tidak ditemukan.'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Data portofolio tidak ditemukan.');
            }

            $request->validate([
                'nama_kegiatan' => 'required|string|max:255',
                'jenis_kegiatan' => 'required|in:Magang,Pelatihan,Lomba,Penelitian,Pengabdian,Lainnya',
                'deskripsi_kegiatan' => 'nullable|string',
                'penyelenggara' => 'nullable|string|max:255',
                'tingkat_kegiatan' => 'required|in:Internasional,Nasional,Regional,Lainnya',
                'link_kegiatan' => 'nullable|url|max:255',
                'peran_dalam_kegiatan' => 'nullable|string|max:255',
            ], [
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi.',
                'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih.',
                'tingkat_kegiatan.required' => 'Tingkat kegiatan harus dipilih.',
                'link_kegiatan.url' => 'Format link tidak valid.'
            ]);

            DB::beginTransaction();
            
            try {
                $portofolioData = [
                    'nama_kegiatan' => $request->input('nama_kegiatan'),
                    'jenis_kegiatan' => $request->input('jenis_kegiatan'),
                    'deskripsi_kegiatan' => $request->input('deskripsi_kegiatan'),
                    'penyelenggara' => $request->input('penyelenggara'),
                    'tingkat_kegiatan' => $request->input('tingkat_kegiatan'),
                    'link_kegiatan' => $request->input('link_kegiatan'),
                    'peran_dalam_kegiatan' => $request->input('peran_dalam_kegiatan'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];

                DB::table('d_portofolio')
                    ->where('portofolio_id', $id)
                    ->update($portofolioData);
                    
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data portofolio berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Data portofolio berhasil diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in updatePortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deletePortofolioMahasiswa($id)
    {
        try {
            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$portofolio) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data portofolio tidak ditemukan.'
                ], 404);
            }

            DB::beginTransaction();
            
            try {
                DB::table('d_portofolio')
                    ->where('portofolio_id', $id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                    ]);
                    
                DB::commit();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data portofolio berhasil dihapus.'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting portofolio data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in deletePortofolioMahasiswa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPortofolioMahasiswaById($id)
    {
        try {
            $portofolio = DB::table('d_portofolio')
                ->where('portofolio_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$portofolio) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data portofolio tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $portofolio
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Exception in getPortofolioMahasiswaById', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }



    public function updateKeahlianBahasaDanTools(Request $request, $id)
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

            // Validasi input
            $request->validate([
                'bidang_keahlian' => 'nullable|array',
                'bidang_keahlian.*' => 'exists:m_bidang_keahlian,bidang_keahlian_id',
                'bahasa_pemrograman' => 'nullable|array',
                'bahasa_pemrograman.*' => 'exists:m_bahasa_pemrograman,bahasa_pemrograman_id',
                'tools' => 'nullable|array',
                'tools.*' => 'exists:m_tools,tool_id',
                'custom_tools' => 'nullable|array',
                'custom_tools.*' => 'json'
            ], [
                'bidang_keahlian.array' => 'Format bidang keahlian tidak valid.',
                'bidang_keahlian.*.exists' => 'Bidang keahlian yang dipilih tidak valid.',
                'bahasa_pemrograman.array' => 'Format bahasa pemrograman tidak valid.',
                'bahasa_pemrograman.*.exists' => 'Bahasa pemrograman yang dipilih tidak valid.',
                'tools.array' => 'Format tools tidak valid.',
                'tools.*.exists' => 'Tools yang dipilih tidak valid.',
                'custom_tools.array' => 'Format custom tools tidak valid.',
                'custom_tools.*.json' => 'Format custom tools tidak valid.'
            ]);

            DB::beginTransaction();
            
            try {
                $userId = session('user_id', 'system');
                $summary = [
                    'bidang_keahlian' => ['inserted' => 0, 'deleted' => 0, 'unchanged' => 0],
                    'bahasa_pemrograman' => ['inserted' => 0, 'deleted' => 0, 'unchanged' => 0],
                    'tools' => ['inserted' => 0, 'deleted' => 0, 'unchanged' => 0]
                ];

                // === PROSES BIDANG KEAHLIAN === (existing code)
                $existingBidangKeahlian = DB::table('t_mahasiswa_bidang_keahlian')
                    ->where('mahasiswa_id', $id)
                    ->whereNull('deleted_at')
                    ->pluck('bidang_keahlian_id')
                    ->toArray();

                $newBidangKeahlian = $request->input('bidang_keahlian', []);

                $bidangToDelete = array_diff($existingBidangKeahlian, $newBidangKeahlian);
                $bidangToInsert = array_diff($newBidangKeahlian, $existingBidangKeahlian);
                $bidangUnchanged = array_intersect($existingBidangKeahlian, $newBidangKeahlian);

                if (!empty($bidangToDelete)) {
                    $summary['bidang_keahlian']['deleted'] = DB::table('t_mahasiswa_bidang_keahlian')
                        ->where('mahasiswa_id', $id)
                        ->whereIn('bidang_keahlian_id', $bidangToDelete)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'deleted_by' => $userId
                        ]);
                }

                if (!empty($bidangToInsert)) {
                    foreach ($bidangToInsert as $bidangKeahlianId) {
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
                                'created_by' => $userId
                            ]);
                            $summary['bidang_keahlian']['inserted']++;
                        }
                    }
                }
                $summary['bidang_keahlian']['unchanged'] = count($bidangUnchanged);

                // === PROSES BAHASA PEMROGRAMAN === (existing code)
                $existingBahasaPemrograman = DB::table('t_mahasiswa_bahasa_pemrograman')
                    ->where('mahasiswa_id', $id)
                    ->whereNull('deleted_at')
                    ->pluck('bahasa_pemrograman_id')
                    ->toArray();

                $newBahasaPemrograman = $request->input('bahasa_pemrograman', []);

                $bahasaToDelete = array_diff($existingBahasaPemrograman, $newBahasaPemrograman);
                $bahasaToInsert = array_diff($newBahasaPemrograman, $existingBahasaPemrograman);
                $bahasaUnchanged = array_intersect($existingBahasaPemrograman, $newBahasaPemrograman);

                if (!empty($bahasaToDelete)) {
                    $summary['bahasa_pemrograman']['deleted'] = DB::table('t_mahasiswa_bahasa_pemrograman')
                        ->where('mahasiswa_id', $id)
                        ->whereIn('bahasa_pemrograman_id', $bahasaToDelete)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'deleted_by' => $userId
                        ]);
                }

                if (!empty($bahasaToInsert)) {
                    foreach ($bahasaToInsert as $bahasaPemrogramanId) {
                        $bahasaPemrogramanExists = DB::table('m_bahasa_pemrograman')
                            ->where('bahasa_pemrograman_id', $bahasaPemrogramanId)
                            ->whereNull('deleted_at')
                            ->exists();
                            
                        if ($bahasaPemrogramanExists) {
                            DB::table('t_mahasiswa_bahasa_pemrograman')->insert([
                                'mahasiswa_bahasa_pemrograman_id' => Str::uuid(),
                                'mahasiswa_id' => $id,
                                'bahasa_pemrograman_id' => $bahasaPemrogramanId,
                                'created_at' => now(),
                                'created_by' => $userId
                            ]);
                            $summary['bahasa_pemrograman']['inserted']++;
                        }
                    }
                }
                $summary['bahasa_pemrograman']['unchanged'] = count($bahasaUnchanged);

                // 1. Ambil existing tools yang sudah tersimpan
                $existingTools = DB::table('t_mahasiswa_tools')
                    ->where('mahasiswa_id', $id)
                    ->whereNull('deleted_at')
                    ->get();

                // 2. Buat array untuk comparison
                $existingToolsArray = [];
                foreach ($existingTools as $tool) {
                    if ($tool->tool_id) {
                        // Tool dari master data
                        $existingToolsArray[] = [
                            'type' => 'master',
                            'id' => $tool->tool_id,
                            'db_id' => $tool->mahasiswa_tool_id
                        ];
                    } else {
                        // Custom tool
                        $existingToolsArray[] = [
                            'type' => 'custom',
                            'nama' => $tool->custom_nama_tool,
                            'deskripsi' => $tool->custom_deskripsi_tool ?? '',
                            'db_id' => $tool->mahasiswa_tool_id
                        ];
                    }
                }

                // 3. Prepare new tools data
                $newMasterTools = $request->input('tools', []);
                $newCustomTools = [];

                $customToolsInput = $request->input('custom_tools', []);
                foreach ($customToolsInput as $customToolJson) {
                    $customToolData = json_decode($customToolJson, true);
                    if ($customToolData && isset($customToolData['nama']) && !empty($customToolData['nama'])) {
                        $newCustomTools[] = [
                            'nama' => $customToolData['nama'],
                            'deskripsi' => $customToolData['deskripsi'] ?? ''
                        ];
                    }
                }

                // 4. Find tools to delete
                $toolsToDelete = [];
                foreach ($existingToolsArray as $existingTool) {
                    $shouldKeep = false;
                    
                    if ($existingTool['type'] === 'master') {
                        // Check if master tool still selected
                        $shouldKeep = in_array($existingTool['id'], $newMasterTools);
                    } else {
                        // Check if custom tool still exists (by name and description)
                        foreach ($newCustomTools as $newCustom) {
                            if ($newCustom['nama'] === $existingTool['nama'] && 
                                $newCustom['deskripsi'] === $existingTool['deskripsi']) {
                                $shouldKeep = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$shouldKeep) {
                        $toolsToDelete[] = $existingTool['db_id'];
                    }
                }

                // 5. Find master tools to insert
                $masterToolsToInsert = [];
                foreach ($newMasterTools as $newMasterTool) {
                    $alreadyExists = false;
                    foreach ($existingToolsArray as $existingTool) {
                        if ($existingTool['type'] === 'master' && $existingTool['id'] === $newMasterTool) {
                            $alreadyExists = true;
                            break;
                        }
                    }
                    if (!$alreadyExists) {
                        $masterToolsToInsert[] = $newMasterTool;
                    }
                }

                // 6. Find custom tools to insert
                $customToolsToInsert = [];
                foreach ($newCustomTools as $newCustom) {
                    $alreadyExists = false;
                    foreach ($existingToolsArray as $existingTool) {
                        if ($existingTool['type'] === 'custom' && 
                            $existingTool['nama'] === $newCustom['nama'] &&
                            $existingTool['deskripsi'] === $newCustom['deskripsi']) {
                            $alreadyExists = true;
                            break;
                        }
                    }
                    if (!$alreadyExists) {
                        $customToolsToInsert[] = $newCustom;
                    }
                }

                // 7. Execute deletions
                if (!empty($toolsToDelete)) {
                    $summary['tools']['deleted'] = DB::table('t_mahasiswa_tools')
                        ->whereIn('mahasiswa_tool_id', $toolsToDelete)
                        ->whereNull('deleted_at')
                        ->update([
                            'deleted_at' => now(),
                            'deleted_by' => $userId
                        ]);
                }

                // 8. Execute insertions for master tools
                foreach ($masterToolsToInsert as $toolId) {
                    $toolExists = DB::table('m_tools')
                        ->where('tool_id', $toolId)
                        ->whereNull('deleted_at')
                        ->exists();
                        
                    if ($toolExists) {
                        DB::table('t_mahasiswa_tools')->insert([
                            'mahasiswa_tool_id' => Str::uuid(),
                            'mahasiswa_id' => $id,
                            'tool_id' => $toolId,
                            'custom_nama_tool' => null,
                            'custom_deskripsi_tool' => null,
                            'created_at' => now(),
                            'created_by' => $userId
                        ]);
                        $summary['tools']['inserted']++;
                    }
                }

                // 9. Execute insertions for custom tools
                foreach ($customToolsToInsert as $customTool) {
                    DB::table('t_mahasiswa_tools')->insert([
                        'mahasiswa_tool_id' => Str::uuid(),
                        'mahasiswa_id' => $id,
                        'tool_id' => null,
                        'custom_nama_tool' => $customTool['nama'],
                        'custom_deskripsi_tool' => $customTool['deskripsi'],
                        'created_at' => now(),
                        'created_by' => $userId
                    ]);
                    $summary['tools']['inserted']++;
                }

                // 10. Calculate unchanged count
                $summary['tools']['unchanged'] = count($existingToolsArray) - ($summary['tools']['deleted'] ?? 0);

                // 11. Debug logging
                \Log::info('Tools update details', [
                    'mahasiswa_id' => $id,
                    'existing_tools_count' => count($existingToolsArray),
                    'new_master_tools' => $newMasterTools,
                    'new_custom_tools' => $newCustomTools,
                    'master_tools_to_insert' => $masterToolsToInsert,
                    'custom_tools_to_insert' => $customToolsToInsert,
                    'tools_to_delete' => $toolsToDelete,
                    'summary' => $summary['tools']
                ]);

                DB::commit();

                // Log activity dengan detail lengkap
                \Log::info('Keahlian, bahasa pemrograman, dan tools mahasiswa updated successfully', [
                    'mahasiswa_id' => $id,
                    'bidang_keahlian_summary' => $summary['bidang_keahlian'],
                    'bahasa_pemrograman_summary' => $summary['bahasa_pemrograman'],
                    'tools_summary' => $summary['tools'],
                    'updated_by' => $userId
                ]);

                // Buat message yang informatif
                $messages = [];
                
                // Message untuk bidang keahlian
                $bidangDetails = [];
                if ($summary['bidang_keahlian']['inserted'] > 0) {
                    $bidangDetails[] = "{$summary['bidang_keahlian']['inserted']} ditambahkan";
                }
                if ($summary['bidang_keahlian']['deleted'] > 0) {
                    $bidangDetails[] = "{$summary['bidang_keahlian']['deleted']} dihapus";
                }
                if ($summary['bidang_keahlian']['unchanged'] > 0) {
                    $bidangDetails[] = "{$summary['bidang_keahlian']['unchanged']} tetap";
                }
                
                if (!empty($bidangDetails)) {
                    $messages[] = "Bidang keahlian: " . implode(', ', $bidangDetails);
                }

                // Message untuk bahasa pemrograman
                $bahasaDetails = [];
                if ($summary['bahasa_pemrograman']['inserted'] > 0) {
                    $bahasaDetails[] = "{$summary['bahasa_pemrograman']['inserted']} ditambahkan";
                }
                if ($summary['bahasa_pemrograman']['deleted'] > 0) {
                    $bahasaDetails[] = "{$summary['bahasa_pemrograman']['deleted']} dihapus";
                }
                if ($summary['bahasa_pemrograman']['unchanged'] > 0) {
                    $bahasaDetails[] = "{$summary['bahasa_pemrograman']['unchanged']} tetap";
                }
                
                if (!empty($bahasaDetails)) {
                    $messages[] = "Bahasa pemrograman: " . implode(', ', $bahasaDetails);
                }

                // Message untuk tools
                if ($summary['tools']['inserted'] > 0) {
                    $messages[] = "Tools: {$summary['tools']['inserted']} ditambahkan";
                }

                $finalMessage = "Data keahlian, bahasa pemrograman, dan tools berhasil diperbarui";
                if (!empty($messages)) {
                    $finalMessage .= ". " . implode('. ', $messages) . ".";
                } else {
                    $finalMessage .= ".";
                }

                return response()->json([
                    'status' => 'success',
                    'message' => $finalMessage,
                    'data' => [
                        'mahasiswa_id' => $id,
                        'total_bidang_keahlian' => count($newBidangKeahlian),
                        'total_bahasa_pemrograman' => count($newBahasaPemrograman),
                        'total_tools' => count($newMasterTools) + count($newCustomTools),
                        'summary' => $summary
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                \Log::error('Error updating keahlian, bahasa pemrograman, dan tools mahasiswa', [
                    'mahasiswa_id' => $id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Exception in updateKeahlianBahasaDanTools', [
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

    
}