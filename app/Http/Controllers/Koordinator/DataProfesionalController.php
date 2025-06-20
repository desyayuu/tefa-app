<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProfesionalController extends Controller
{
    public function getDataProfesional(Request $request){
        $search = $request->input('search'); 

        // 1. query untuk get semua profesional
        $query = DB::table ('d_profesional as profesional')
            ->join('d_user as user', 'profesional.user_id', '=', 'user.user_id')
            ->select('profesional.*', 'user.*')
            ->whereNull('profesional.deleted_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('profesional.nama_profesional', 'like', "%$search%")
                ->orWhere('user.email', 'like', "%$search%");
            });
        }

        $profesional = $query->orderBy('user.created_at', 'desc')->paginate(10); 

        // 2. Query untuk partisipasi profesional
        $searchPartisipasi = $request->input('search_partisipasi');

        // Query untuk mendapatkan profesional yang berperan sebagai project leader
        $profesionalLeaderQuery = DB::table('d_profesional as profesional')
            ->join('d_user as user', 'profesional.user_id', '=', 'user.user_id')
            ->join('t_project_leader as leader', 'profesional.profesional_id', '=', 'leader.leader_id')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'profesional.profesional_id',
                'profesional.nama_profesional',
                'user.email',
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.status_proyek',
                DB::raw("'Project Leader' as role_type"),
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai'
            )
            ->where('leader.leader_type', 'Profesional')
            ->where('proyek.status_proyek', 'In Progress')
            ->whereNull('profesional.deleted_at')
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Tambahkan pencarian untuk leader
        if ($searchPartisipasi) {
            $profesionalLeaderQuery->where(function($q) use ($searchPartisipasi) {
                $q->where('profesional.nama_profesional', 'like', "%$searchPartisipasi%")
                ->orWhere('user.email', 'like', "%$searchPartisipasi%")
                ->orWhere('proyek.nama_proyek', 'like', "%$searchPartisipasi%");
            });
        }

        // Query untuk mendapatkan profesional yang berperan sebagai project member
        $profesionalMemberQuery = DB::table('d_profesional as profesional')
            ->join('d_user as user', 'profesional.user_id', '=', 'user.user_id')
            ->join('t_project_member_profesional as member', 'profesional.profesional_id', '=', 'member.profesional_id')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'profesional.profesional_id',
                'profesional.nama_profesional',
                'user.email',
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.status_proyek',
                DB::raw("'Anggota' as role_type"),
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai'
            )
            ->where('proyek.status_proyek', 'In Progress')
            ->whereNull('profesional.deleted_at')
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Tambahkan pencarian untuk member
        if ($searchPartisipasi) {
            $profesionalMemberQuery->where(function($q) use ($searchPartisipasi) {
                $q->where('profesional.nama_profesional', 'like', "%$searchPartisipasi%")
                ->orWhere('user.email', 'like', "%$searchPartisipasi%")
                ->orWhere('proyek.nama_proyek', 'like', "%$searchPartisipasi%");
            });
        }

        // ALTERNATIF 1: Menggunakan Collection merge (lebih reliable)
        $leaderResults = $profesionalLeaderQuery->get();
        $memberResults = $profesionalMemberQuery->get();
        
        // Gabungkan hasil dan urutkan
        $allResults = $leaderResults->concat($memberResults)
            ->sortBy('nama_profesional')
            ->sortByDesc('role_type'); // Project Leader dulu, baru Anggota

        // Manual pagination untuk collection
        $perPage = 5;
        $currentPage = request()->get('partisipasi_page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedResults = $allResults->slice($offset, $perPage)->values();
        
        // Buat custom pagination
        $partisipasiProfesional = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedResults,
            $allResults->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'partisipasi_page',
            ]
        );
        return view('pages.Koordinator.DataProfesional.kelola_data_profesional', compact(
            'profesional', 
            'search', 
            'partisipasiProfesional', 
            'searchPartisipasi'
        ), [
            'titleSidebar' => 'Data Profesional'
        ]);
    }

    public function getDataProfesionalById(Request $request, $id){
        // Get data profesional utama
        $profesional = DB::table('d_profesional as profesional')
            ->join('d_user as user', 'profesional.user_id', '=', 'user.user_id')
            ->select('profesional.*', 'user.*')
            ->where('profesional.profesional_id', $id)
            ->first();
            
        if (!$profesional) {
            return redirect()->route('koordinator.dataProfesional')->with('error', 'Data profesional tidak ditemukan.');
        }

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
            ->where('leader.leader_type', 'Profesional')
            ->where('proyek.status_proyek', 'Done') 
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Query untuk proyek sebagai member
        $proyekMemberQuery = DB::table('t_project_member_profesional as member')
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
            ->where('member.profesional_id', $id)
            ->where('proyek.status_proyek', 'Done') 
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Gabungkan kedua query dengan UNION dan buat pagination
        $combinedQuery = $proyekLeaderQuery->union($proyekMemberQuery);
        
        $riwayatProyek = DB::table(DB::raw("({$combinedQuery->toSql()}) as riwayat"))
            ->mergeBindings($combinedQuery)
            ->orderBy('riwayat.tanggal_selesai', 'desc')
            ->paginate(5, ['*'], 'riwayat_page'); 

        // Append profesional_id ke pagination links
        $riwayatProyek->appends(['profesional_id' => $id]);

        return view('pages.Koordinator.DataProfesional.detail_data_profesional', compact('profesional', 'riwayatProyek'), [
            'titleSidebar' => 'Detail Profesional'
        ]);
    }

    public function tambahDataProfesional(Request $request){
        try {
            $isSingle = $request->input('is_single') === '1';
            
            if ($isSingle) {
                $request->validate([
                    'nama_profesional' => 'required|string|max:255',
                    'email_profesional' => 'required|email|unique:d_user,email',
                    'status_akun_profesional' => 'required|in:Active,Rejected,Pending,Disabled', 
                    'profile_img_profesional' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                    'telepon_profesional' => 'nullable|string|max:20',
                    'tanggal_lahir_profesional' => 'nullable|date',
                    'jenis_kelamin_profesional' => 'nullable|in:Laki-Laki,Perempuan',
                ], 
                [
                    'email_profesional.unique' => 'Email sudah terdaftar dalam sistem.',
                    'nama_profesional.required' => 'Nama profesional harus diisi.',
                    'email_profesional.required' => 'Email harus diisi.',
                    'email_profesional.email' => 'Format email tidak valid.',
                    'status_akun_profesional.required' => 'Status harus diisi.',
                    'email_profesional.email' => 'Format email tidak valid.',
                    'profile_img_profesional.image' => 'File harus berupa gambar.',
                    'profile_img_profesional.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                    'profile_img_profesional.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                ]);
    
                $emailExists = DB::table('d_user')->where('email', $request->input('email_profesional'))->exists();
                
                if ($emailExists) {
                    return back()->withInput()->withErrors(['email_profesional' => 'Email sudah ada di daftar data profesional.']);
                }
                
                $userId = Str::uuid();
                $profesionalId = Str::uuid();
                DB::beginTransaction();
                
                try {
                    DB::table('d_user')->insert([
                        'user_id' => $userId,
                        'email' => $request->input('email_profesional'),
                        'password' => bcrypt($request->input('password_profesional') ?: 'password123'),
                        'role' => 'Profesional',
                        'status' => $request->input('status_akun_profesional', 'Active'), 
                        'created_by' => session('user_id'),
                    ]);

                    $tanggalLahir = null;
                    if (!empty($profesional['tanggal_lahir_profesional'])) {
                        $tanggalLahir = date('Y-m-d', strtotime($profesional['tanggal_lahir_profesional']));
                    }
                    
                    $profesionalData = [
                        'profesional_id' => $profesionalId,
                        'user_id' => $userId,
                        'nama_profesional' => $request->input('nama_profesional'),
                        'tanggal_lahir_profesional' => $tanggalLahir,
                        'jenis_kelamin_profesional' => $request->input('jenis_kelamin_profesional') ? $request->input('jenis_kelamin_profesional') : null,
                        'telepon_profesional' => $request->input('telepon_profesional') ? $request->input('telepon_profesional') : null,                        'status_akun_profesional' => $request->input('status_akun_profesional', 'Active'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ];
                    
                    if ($request->hasFile('profile_img_profesional')) {
                        $file = $request->file('profile_img_profesional');
                        
                        if ($file->isValid()) {
                            $uploadPath = public_path('uploads/profile_profesional');
                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }
                            
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            
                            try {
                                if ($file->move($uploadPath, $filename)) {
                                    $profesionalData['profile_img_profesional'] = 'uploads/profile_profesional/' . $filename;
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
                            'has_file' => $request->hasFile('profile_img_profesional'),
                            'request_keys' => $request->keys()
                        ]);
                    }
                    
                    DB::table('d_profesional')->insert($profesionalData);
                    DB::commit();
                    return redirect()->route('koordinator.dataProfesional')->with('success', 'Data profesional berhasil ditambahkan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding profesional data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataProfesional')->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
                }
            } else {
                $profesionalData = json_decode($request->input('profesional_data'), true);
                if (empty($profesionalData)) {
                    return redirect()->route('koordinator.dataProfesional')->with('error', 'Tidak ada data profesional untuk ditambahkan.');
                }
                
                DB::beginTransaction();
                try {
                    $insertedCount = 0;
                    $errors = [];
                    
                    foreach ($profesionalData as $index => $profesional) {
                        if (empty($profesional['nama_profesional']) || empty($profesional['email_profesional'])) {
                            array_push($errors, 'Data profesional tidak lengkap: ' . ($profesional['nama_profesional'] ?? 'Unnamed'));
                            continue;
                        }
                        
                        $emailExists = DB::table('d_user')->where('email', $profesional['email_profesional'])->exists();
                        
                        
                        if ($emailExists) {
                            array_push($errors, 'Email ' . $profesional['email_profesional'] . ' sudah terdaftar.');
                            continue;
                        }
                        
                        $userId = Str::uuid();
                        $profesionalId = Str::uuid();
                        
                        DB::table('d_user')->insert([
                            'user_id' => $userId,
                            'email' => $profesional['email_profesional'],
                            'password' => bcrypt($profesional['password_profesional'] ?: 'password123'), 
                            'role' => 'Profesional',
                            'status' => $profesional['status_akun_profesional'] ?? 'Active', 
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ]);

                        $tanggalLahir = null;
                        if (!empty($profesional['tanggal_lahir_profesional'])) {
                            $tanggalLahir = date('Y-m-d', strtotime($profesional['tanggal_lahir_profesional']));
                        }
                        
                        $profesionalRecord = [
                            'profesional_id' => $profesionalId,
                            'user_id' => $userId,
                            'nama_profesional' => $profesional['nama_profesional'],
                            'tanggal_lahir_profesional' => $tanggalLahir,
                            'jenis_kelamin_profesional' => $profesional['jenis_kelamin_profesional'] ?? null,
                            'telepon_profesional' => $profesional['telepon_profesional'] ?? null,
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ];
                        
                        $fileKey = "profile_img_profesional_{$index}";
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
                                $uploadPath = public_path('uploads/profile_profesional');
                                if (!is_dir($uploadPath)) {
                                    mkdir($uploadPath, 0777, true);
                                }
                                
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                if ($file->move($uploadPath, $filename)) {
                                    $profesionalRecord['profile_img_profesional'] = 'uploads/profile_profesional/' . $filename;
                                    
                                    \Log::info('File upload success for multiple mode', [
                                        'index' => $index,
                                        'path' => $profesionalRecord['profile_img_profesional']
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
                        } else if (isset($profesional['has_profile_img']) && $profesional['has_profile_img']) {
                            \Log::warning("File flag set but no file found for index {$index}", [
                                'file_key' => $fileKey
                            ]);
                        }

                        DB::table('d_profesional')->insert($profesionalRecord);       
                        $insertedCount++;
                    }
                    
                    DB::commit();
                    
                    if (count($errors) > 0) {
                        $errorMessage = implode('<br>', $errors);
                        return redirect()->route('koordinator.dataProfesional')
                            ->with('warning', "$insertedCount data profesional berhasil ditambahkan.<br>Beberapa error terjadi:<br>$errorMessage");
                    }
                    
                    return redirect()->route('koordinator.dataProfesional')
                        ->with('success', "$insertedCount data profesional berhasil ditambahkan.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error adding multiple profesional data', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('koordinator.dataProfesional')
                        ->with('error', 'Gagal menambahkan data profesional: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Exception in tambahDataProfesional', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('koordinator.dataProfesional')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

    }

    public function checkEmailProfesionalExists(Request $request){
        \Log::info('Check email request:', $request->all());
        
        $email = $request->input('email_profesional');
        $profesionalId = $request->input('profesional_id');
        
        $emailExists = false;
        
        if ($email) {
            $query = DB::table('d_user')
                ->where('email', $email);
                
            // Exclude current profesional when checking for duplicates
            if ($profesionalId) {
                $profesional = DB::table('d_profesional')
                    ->where('profesional_id', $profesionalId)
                    ->first();
                    
                if ($profesional) {
                    $query->where('user_id', '!=', $profesional->user_id);
                }
            }
            
            $emailExists = $query->exists();
        }
        
        // Log hasil untuk debugging
        \Log::info('Check result:', [
            'email' => $email,
            'profesionalId' => $profesionalId,
            'emailExists' => $emailExists,
        ]);
        
        return response()->json([
            'emailExists' => $emailExists,
        ]);
    }

    public function updateDataProfesional(Request $request, $id){
        try {
            // Get current profesional data
            $profesional = DB::table('d_profesional')
                ->where('profesional_id', $id)
                ->first();
                
            if (!$profesional) {
                return redirect()->route('koordinator.dataProfesional')
                    ->with('error', 'Data profesional tidak ditemukan.');
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $profesional->user_id)
                ->first();
    
            // Buat aturan validasi custom
            $rules = [
                'nama_profesional' => 'required|string|max:255',
                'email_profesional' => 'required|email',
                'status_akun_profesional' => 'required|in:Active,Rejected,Pending,Disabled', 
                'profile_img_profesional' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_profesional' => 'nullable|string|max:20',
                'tanggal_lahir_profesional' => 'nullable|date',
                'jenis_kelamin_profesional' => 'nullable|in:Laki-Laki,Perempuan',
            ];
            
            if ($user->email != $request->input('email_profesional')) {
                $rules['email_profesional'] .= '|unique:d_user,email,' . $profesional->user_id . ',user_id';
            }
            
            // Custom pesan error
            $messages = [
                'email_profesional.email' => 'Format email tidak valid.',
                'email_profesional.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_profesional.required' => 'Nama profesional harus diisi.',
                'email_profesional.required' => 'Email harus diisi.',
                'status_akun_profesional.required' => 'Status harus diisi.',
                'profile_img_profesional.image' => 'File harus berupa gambar.',
                'profile_img_profesional.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_profesional.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
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
                    'email' => $request->input('email_profesional'),
                    'status' => $request->input('status_akun_profesional'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                DB::table('d_user')
                    ->where('user_id', $profesional->user_id)
                    ->update($userData);
                
                // Update profesional data
                $profesionalData = [
                    'nama_profesional' => $request->input('nama_profesional'),
                    'tanggal_lahir_profesional' => $request->filled('tanggal_lahir_profesional') ? $request->input('tanggal_lahir_profesional') : null,
                    'jenis_kelamin_profesional' => $request->input('jenis_kelamin_profesional'),
                    'telepon_profesional' => $request->input('telepon_profesional'),
                    'updated_at' => now(),
                    'updated_by' => session('user_id'),
                ];
                
                // Handle profile image upload
                if ($request->hasFile('profile_img_profesional')) {
                    $file = $request->file('profile_img_profesional');
                    
                    if ($file->isValid()) {
                        $uploadPath = public_path('uploads/profile_profesional');
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }
                        
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        
                        if ($file->move($uploadPath, $filename)) {
                            // Delete old image if exists
                            if ($profesional->profile_img_profesional) {
                                $oldImagePath = public_path($profesional->profile_img_profesional);
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            
                            $profesionalData['profile_img_profesional'] = 'uploads/profile_profesional/' . $filename;
                        } else {
                            throw new \Exception("Failed to move uploaded file");
                        }
                    } else {
                        throw new \Exception("Uploaded file is not valid");
                    }
                }
                
                DB::table('d_profesional')
                    ->where('profesional_id', $id)
                    ->update($profesionalData);
                
                DB::commit();
                
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data profesional berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()
                    ->with('success', 'Data profesional berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating profesional data', [
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
            \Log::error('Exception in updateDataProfesional', [
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

    // Cek apkah profesional bisa dihapus atau ngga (berkaitan apa jadi project leader atau member)
    public function checkProfesionalDeletable($profesionalId)
    {
        $constraints = [];
        $blockStatus = ['Initiation', 'In Progress'];
        
        // 1. Cek apakah profesional masih menjadi project leader di proyek yang In Progress atau inisiasi
        $activeAsLeader = DB::table('t_project_leader as leader')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->where('leader.leader_id', $profesionalId)
            ->where('leader.leader_type', 'Profesional')
            ->whereIn('proyek.status_proyek', $blockStatus)
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at')
            ->select('proyek.nama_proyek', 'proyek.proyek_id')
            ->get();

        if ($activeAsLeader->isNotEmpty()) {
            $constraints['active_as_leader'] = [
                'status' => false,
                'message' => 'Profesional masih menjadi Project Leader di proyek yang akan atau sedang berjalan',
                'details' => $activeAsLeader->pluck('nama_proyek')->toArray(),
                'count' => $activeAsLeader->count()
            ];
        }

        // 2. Cek apakah profesional masih menjadi member di proyek yang In Progress atau inisiasi
        $activeAsMember = DB::table('t_project_member_profesional as member')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->where('member.profesional_id', $profesionalId)
            ->whereIn('proyek.status_proyek', $blockStatus)
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at')
            ->select('proyek.nama_proyek', 'proyek.proyek_id')
            ->get();

        
        if ($activeAsMember->isNotEmpty()) {
            $constraints['active_as_member'] = [
                'status' => false,
                'message' => 'Profesional masih menjadi anggota di proyek yang akan atau sedang berjalan',
                'details' => $activeAsMember->pluck('nama_proyek')->toArray(),
                'count' => $activeAsMember->count()
            ];
        }

        return [
            'deletable' => empty($constraints),
            'constraints' => $constraints
        ];
    }

    public function deleteDataProfesional($id)
    {
        try {
            // 1. Validasi profesional exists
            $profesional = DB::table('d_profesional')
                ->where('profesional_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$profesional) {
                return redirect()->route('koordinator.dataProfesional')
                    ->with('error', 'Data profesional tidak ditemukan.');
            }

            // 2. Check apakah profesional bisa dihapus
            $deletableCheck = $this->checkProfesionalDeletable($id);
            
            if (!$deletableCheck['deletable']) {
                $constraints = $deletableCheck['constraints'];
                $errorMessages = [];
                
                foreach ($constraints as $constraint) {
                    $errorMessages[] = $constraint['message'];
                    if (isset($constraint['details']) && !empty($constraint['details'])) {
                        $errorMessages[] = '- Proyek: ' . implode(', ', $constraint['details']);
                    }
                }
                
                return redirect()->route('koordinator.dataProfesional')
                    ->with('error', 'Tidak dapat menghapus profesional. ' . implode(' ', $errorMessages));
            }

            DB::beginTransaction();
                
            try {
                // 3. Soft delete profesional
                DB::table('d_profesional')
                    ->where('profesional_id', $id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                    ]);
                    
                // 4. Soft delete user account
                DB::table('d_user')
                    ->where('user_id', $profesional->user_id)
                    ->update([
                        'deleted_at' => now(),
                        'deleted_by' => session('user_id'),
                        'status' => 'Disabled'
                    ]);
                    
                DB::commit();

                // 5. Logging
                \Log::info('Profesional deleted successfully with cascade operations', [
                    'profesional_id' => $id,
                    'profesional_name' => $profesional->nama_profesional,
                ]);

                // 6. Success message
                $successMessage = "Data profesional {$profesional->nama_profesional} berhasil dihapus";
                return redirect()->route('koordinator.dataProfesional')
                    ->with('success', $successMessage);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error deleting profesional data with cascade', [
                    'profesional_id' => $id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->route('koordinator.dataProfesional')
                    ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            \Log::error('Exception in deleteDataProfesional', [
                'message' => $e->getMessage()
            ]);
            return redirect()->route('koordinator.dataProfesional')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
