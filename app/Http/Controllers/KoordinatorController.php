<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KoordinatorController extends Controller
{
    public function dashboard()
    {
        $koordinator = DB::table('d_koordinator')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Koordinator.dashboard', compact('koordinator'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }

    //User 
    public function getDataUser(Request $request){
        $search = $request->input('search');
    
        $query = DB::table('d_user')
            ->select(
                'd_user.user_id', 'd_user.email', 'd_user.role', 'd_user.status as status', 'd_user.created_at', 'd_user.updated_at', 'd_user.deleted_at',
                'd_dosen.nama_dosen as nama_dosen', 'd_dosen.nidn_dosen as nidn_dosen', 'd_dosen.telepon_dosen as telepon_dosen',
                'd_profesional.nama_profesional as nama_profesional', 'd_profesional.telepon_profesional as telepon_profesional',
                'd_mahasiswa.nama_mahasiswa as nama_mahasiswa', 'd_mahasiswa.nim', 'd_mahasiswa.telepon_mahasiswa as telepon_mahasiswa',
                'd_koordinator.telepon_koordinator as telepon_koordinator', 'd_koordinator.nidn_koordinator as nidn_koordinator',
                'd_koordinator.nama_koordinator as nama_koordinator',
            )
            ->leftJoin('d_dosen', function($join) {
                $join->on('d_user.user_id', '=', 'd_dosen.user_id')
                     ->whereNull('d_dosen.deleted_at');
            })
            ->leftJoin('d_profesional', function($join) {
                $join->on('d_user.user_id', '=', 'd_profesional.user_id')
                     ->whereNull('d_profesional.deleted_at');
            })
            ->leftJoin('d_mahasiswa', function($join) {
                $join->on('d_user.user_id', '=', 'd_mahasiswa.user_id')
                     ->whereNull('d_mahasiswa.deleted_at');
            })
            ->leftJoin('d_koordinator', function($join) {
                $join->on('d_user.user_id', '=', 'd_koordinator.user_id')
                     ->whereNull('d_koordinator.deleted_at');
            })
            ->whereNull('d_user.deleted_at');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d_user.email', 'like', "%{$search}%")
                  ->orWhere('d_user.role', 'like', "%{$search}%")
                  ->orWhere('d_dosen.nama_dosen', 'like', "%{$search}%")
                  ->orWhere('d_profesional.nama_profesional', 'like', "%{$search}%")
                  ->orWhere('d_mahasiswa.nama_mahasiswa', 'like', "%{$search}%");
            });
        }
        $user = $query->orderBy('d_user.created_at', 'desc')->paginate(10);
    
        return view('pages.Koordinator.data_user', compact('user', 'search'), [
            'titleSidebar' => 'Data User',
        ]);
    }

    public function updateStatusUser(Request $request, $id){
        try {
            $request->validate([
                'status' => 'required|in:Active,Rejected,Pending,Disabled',
            ]);
    
            // Get user data
            $user = DB::table('d_user')
                ->select(
                    'd_user.*',
                    'd_dosen.nama_dosen', 'd_dosen.nidn_dosen', 'd_dosen.telepon_dosen',
                    'd_profesional.nama_profesional', 'd_profesional.telepon_profesional',
                    'd_mahasiswa.nama_mahasiswa', 'd_mahasiswa.nim', 'd_mahasiswa.telepon_mahasiswa',
                    'd_koordinator.nama_koordinator', 'd_koordinator.nidn_koordinator', 'd_koordinator.telepon_koordinator'
                )
                ->leftJoin('d_dosen', 'd_user.user_id', '=', 'd_dosen.user_id')
                ->leftJoin('d_profesional', 'd_user.user_id', '=', 'd_profesional.user_id')
                ->leftJoin('d_mahasiswa', 'd_user.user_id', '=', 'd_mahasiswa.user_id')
                ->leftJoin('d_koordinator', 'd_user.user_id', '=', 'd_koordinator.user_id')
                ->where('d_user.user_id', $id)
                ->first();
    
            if (!$user) {
                return redirect()->route('koordinator.dataUser')->with('error', 'User tidak ditemukan.');
            }
    
            // Begin transaction
            DB::beginTransaction();
    
            try {
                // Update user table - only the status is actually changeable in the form
                DB::table('d_user')
                    ->where('user_id', $id)
                    ->update([
                        'status' => $request->input('status'),
                        'updated_at' => now(),
                        'updated_by' => session('user_id'),
                    ]);
    
                // We don't need to update role-specific data since those fields are disabled
                // and no changes would be made to them anyway. The role itself cannot be changed
                // in this form due to the disabled input.
    
                DB::commit();
                return redirect()->route('koordinator.dataUser')->with('success', 'Status user berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('koordinator.dataUser')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataUser')->with('error', 'Validasi gagal: ' . $e->getMessage());
        }
    }

    public function deleteDataUser(Request $request, $id){
        try{
            $user = DB::table('d_user')
                ->where('user_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$user) {
                return redirect()->route('koordinator.dataUser')->with('error', 'User tidak ditemukan.');
            }

            DB::beginTransaction();

            try{
                $now = now();

                DB::table('d_user')
                    ->where('user_id', $id)
                    ->update([
                        'deleted_at' => $now,
                        'deleted_by' => session('user_id'),
                    ]);

                if($user->role == 'Dosen'){
                    DB::table('d_dosen')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                } elseif($user->role == 'Profesional'){
                    DB::table('d_profesional')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                } elseif($user->role == 'Mahasiswa'){
                    DB::table('d_mahasiswa')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                }elseif($user->role == 'Koordinator'){
                    DB::table('d_koordinator')
                        ->where('user_id', $id)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => session('user_id'),
                        ]);
                }

                DB::commit();
                return redirect()->route('koordinator.dataUser')->with('success', 'User berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('koordinator.dataUser')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
            }
        }catch(\Exception $e){
            return redirect()->route('koordinator.dataUser')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    

    //Mitra
    public function getDataMitra(Request $request){
        $search = $request->input('search');
    
        $query = DB::table('d_mitra_proyek')
            ->select('mitra_proyek_id', 'nama_mitra', 'telepon_mitra', 'email_mitra', 'alamat_mitra')
            ->whereNull('deleted_at');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('telepon_mitra', 'like', "%{$search}%");
            });
        }
        $mitra = $query->paginate(10);
        return view('pages.Koordinator.data_mitra', compact('mitra', 'search'), [
            'titleSidebar' => 'Data Mitra',
        ]);
    }
    
    
    public function storeDataMitra(Request $request){
        try{
            $request->validate([
                'nama_mitra' => 'required|string|max:255',
                'email_mitra' => 'nullable|email',
                'telepon_mitra' => 'nullable|string|max:20',
            ]);
    
            DB::table('d_mitra_proyek')->insert([
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra'     => $request->input('nama_mitra'),
                'email_mitra'    => $request->input('email_mitra'),
                'telepon_mitra'  => $request->input('telepon_mitra'),
                'alamat_mitra'   => $request->input('alamat_mitra'),
                'created_at'     => now(), 
                'created_by'     => session('user_id'),
            ]);
            return redirect()->route('koordinator.dataMitra')->with('success', 'Data mitra berhasil ditambahkan.');
        }catch(\Exception $e){
            return redirect()->route('koordinator.dataMitra')->with('error', 'Data mitra gagal ditambahkan. Silakan coba lagi.');
        }  
    }

    public function tambahMultipleDataMitra(Request $request){
        try {
            // Cek mode operasi (single atau multiple)
            $isSingle = $request->input('is_single') === '1';
            
            if ($isSingle) {
                // Validasi data single
                $request->validate([
                    'nama_mitra' => 'required|string|max:255',
                    'email_mitra' => 'required|email|unique:d_mitra_proyek,email_mitra',
                    'telepon_mitra' => 'required|string|max:20',
                    'alamat_mitra' => 'required|string',
                ]);
                
                // Insert single data
                DB::table('d_mitra_proyek')->insert([
                    'mitra_proyek_id' => Str::uuid(),
                    'nama_mitra'     => $request->input('nama_mitra'),
                    'email_mitra'    => $request->input('email_mitra'),
                    'telepon_mitra'  => $request->input('telepon_mitra'),
                    'alamat_mitra'   => $request->input('alamat_mitra'),
                    'created_at'     => now(), 
                    'created_by'     => session('user_id'),
                ]);
                
                return redirect()->route('koordinator.dataMitra')->with('success', 'Data mitra berhasil ditambahkan.');
            } else {
                // Mode multiple, ambil data dari JSON
                $mitraData = json_decode($request->input('mitra_data'), true);
                
                if (empty($mitraData)) {
                    return redirect()->route('koordinator.dataMitra')->with('error', 'Tidak ada data mitra untuk ditambahkan.');
                }
                
                // Validasi semua data
                foreach ($mitraData as $mitra) {
                    if (empty($mitra['nama_mitra']) || empty($mitra['email_mitra'])){
                        return redirect()->route('koordinator.dataMitra')->with('error', 'Field Nama, Email harus diisi');
                    }
                    
                    // Cek email duplikat di database
                    $emailExists = DB::table('d_mitra_proyek')
                        ->where('email_mitra', $mitra['email_mitra'])
                        ->exists();
                        
                    if ($emailExists) {
                        return redirect()->route('koordinator.dataMitra')
                            ->with('error', 'Email ' . $mitra['email_mitra'] . ' sudah terdaftar.');
                    }
                }
                
                // Insert multiple data
                foreach ($mitraData as $mitra) {
                    DB::table('d_mitra_proyek')->insert([
                        'mitra_proyek_id' => Str::uuid(),
                        'nama_mitra'     => $mitra['nama_mitra'],
                        'email_mitra'    => $mitra['email_mitra'],
                        'telepon_mitra'  => $mitra['telepon_mitra'],
                        'alamat_mitra'   => $mitra['alamat_mitra'],
                        'created_at'     => now(), 
                        'created_by'     => session('user_id'),
                    ]);
                }
                
                return redirect()->route('koordinator.dataMitra')
                    ->with('success', count($mitraData) . ' data mitra berhasil ditambahkan.');
            }
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataMitra')
                ->with('error', 'Gagal menambahkan data mitra: ' . $e->getMessage());
        }
    }

    public function checkEmailExists(Request $request){
        // Validasi request
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $email = $request->input('email');
        
        // Cek apakah email sudah ada di database
        $exists = DB::table('d_mitra_proyek')
            ->where('email_mitra', $email)
            ->exists();
        
        return response()->json([
            'exists' => $exists
        ]);
    }

    public function updateDataMitra(Request $request, $id){
        try {
            $request->validate([
                'nama_mitra' => 'required|string|max:255',
                'email_mitra' => 'nullable|email',
                'telepon_mitra' => 'nullable|regex:/^[0-9]+$/|max:20',
                'alamat_mitra' => 'nullable|string|max:255',
            ]);
            
    
            // Check if data exists
            $exists = DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->exists();
                
            if (!$exists) {
                return redirect()->route('koordinator.dataMitra')->with('error', 'Data mitra tidak ditemukan.');
            }

            // Check if email already exists (skip current record)
            if ($request->filled('email_mitra')) {
        $emailExists = DB::table('d_mitra_proyek')
            ->where('email_mitra', $request->input('email_mitra'))
            ->where('mitra_proyek_id', '!=', $id)
            ->exists();
                
        if ($emailExists) {
            return redirect()->route('koordinator.dataMitra')
                ->with('error', 'Email sudah digunakan oleh mitra lain.');
            }
        }

        DB::table('d_mitra_proyek')
            ->where('mitra_proyek_id', $id)
            ->update([
                'nama_mitra'     => $request->input('nama_mitra'),
                'email_mitra'    => $request->input('email_mitra'),
                'telepon_mitra'  => $request->input('telepon_mitra'),
                'alamat_mitra'   => $request->input('alamat_mitra'),
                'updated_at'     => now(), 
                'updated_by'     => session('user_id'),
            ]);
            return redirect()->route('koordinator.dataMitra')->with('success', 'Data mitra berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataMitra')->with('error', 'Gagal memperbarui data mitra: ' . $e->getMessage());
        }
    }

    public function deleteDataMitra($id){
        try {
            // Cek apakah data ada
            $mitra = DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->whereNull('deleted_at') // pastikan data belum dihapus
                ->first();
    
            if (!$mitra) {
                return redirect()->route('koordinator.dataMitra')->with('error', 'Data mitra tidak ditemukan.');
            }
    
            // Soft delete: update kolom deleted_at dan deleted_by
            DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => session('user_id')
                ]);
    
            return redirect()->route('koordinator.dataMitra')
                ->with('success', 'Data mitra "' . $mitra->nama_mitra . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataMitra')
                ->with('error', 'Gagal menghapus data mitra: ' . $e->getMessage());
        }
    }
    

    public function searchDataMitra(Request $request){
        $query = $request->input('query');

        $mitra = DB::table('m_mitra_proyek')
            ->where('nama_mitra', 'like', "%$query%")
            ->orWhere('email_mitra', 'like', "%$query%")
            ->orWhere('telepon_mitra', 'like', "%$query%")
            ->get();
    
        return response()->json([
            'data' => $mitra
        ]);
    }

    //Proyek
    public function getDataProyek(Request $request){

        $jenisProyek = DB::table('m_jenis_proyek')->get();
        $daftarMitra = DB::table('d_mitra_proyek')->get();
        $dataDosen = DB::table('d_dosen')->get();
        $query = DB::table('m_proyek')
        ->select(
            'm_proyek.proyek_id',
            'm_proyek.nama_proyek',
            'm_proyek.deskripsi_proyek',
            'm_proyek.tanggal_mulai',
            'm_proyek.tanggal_selesai',
            'm_proyek.status_proyek',
            'd_dosen.nama_dosen as nama_project_leader'
        )
        ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
        ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
        ->leftJoin('t_project_leader', 'm_proyek.proyek_id', '=', 't_project_leader.proyek_id')
        ->leftJoin('d_dosen', 't_project_leader.dosen_id', '=', 'd_dosen.dosen_id');
    
        $proyek = $query->get();
        
        return view('pages.Koordinator.data_proyek', compact('proyek'), [
            'titleSidebar' => 'Data Proyek',
            'jenisProyek' => $jenisProyek,
            'daftarMitra' => $daftarMitra,
            'dataDosen' => $dataDosen,
        ]);
    }

    public function tambahDataProyek(Request $request){
        $request->validate([
            'mitra_id'          => 'required|uuid',
            'jenis_proyek'      => 'required|uuid',
            'nama_proyek'       => 'required|string|max:255',
            'status_proyek'     => 'required|string|max:50',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'dana_pendanaan'    => 'nullable|numeric',
            'project_leader_id' => 'required|uuid'
        ]);
    
        // Buat UUID
        $proyek_id = Str::uuid()->toString();
        $project_leader_id = Str::uuid()->toString();
    
        DB::beginTransaction();
    
        try {
            // Simpan ke tabel m_proyek
            DB::table('m_proyek')->insert([
                'proyek_id'        => $proyek_id,
                'mitra_proyek_id'  => $request->input('mitra_id'),
                'jenis_proyek_id'  => $request->input('jenis_proyek'),
                'nama_proyek'      => $request->input('nama_proyek'),
                'deskripsi_proyek' => '-', // sementara, bisa diubah jadi input
                'status_proyek'    => $request->input('status_proyek'),
                'tanggal_mulai'    => $request->input('tanggal_mulai'),
                'tanggal_selesai'  => $request->input('tanggal_selesai'),
                'dana_pendanaan'   => $request->input('dana_pendanaan'),
                'created_at'       => now(),
                'created_by'       => auth()->user()->id ?? session('user_id'),
            ]);
    
            // Simpan ke tabel t_project_leader
            DB::table('t_project_leader')->insert([
                'project_leader_id' => $project_leader_id, // pastikan nama kolomnya benar
                'dosen_id'          => $request->input('project_leader_id'),
                'proyek_id'         => $proyek_id,
                'created_at'        => now(),
                'created_by'        => auth()->user()->id ?? session('user_id'),
            ]);
    
            DB::commit();
    
            return redirect()->back()->with('success', 'Data proyek berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan proyek: ' . $e->getMessage());
        }
    }

    public function getDataProyekById($id){
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $id)
            ->first();
        
        if (!$proyek) {
            return response()->json(['error' => 'Data proyek tidak ditemukan.'], 404);
        }
        
        return view('pages.Koordinator.detail_data_proyek', compact('proyek'), [
            'titleSidebar' => 'Detail Data Proyek',
        ]);
    }
    

    //Dosen 
    public function getDataDosen(Request $request){
        $search = $request->input('search');
        
        $query = DB::table('d_dosen')
        ->join('d_user', 'd_dosen.user_id', '=', 'd_user.user_id')
        ->select('d_dosen.dosen_id', 'd_dosen.nama_dosen', 
        'd_dosen.telepon_dosen', 'd_user.email', 'd_dosen.nidn_dosen', 
        'd_dosen.profile_img_dosen', 'd_dosen.jenis_kelamin_dosen', 'd_dosen.tanggal_lahir_dosen');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d_dosen.nama', 'like', "%{$search}%")
                ->orWhere('d_user.email', 'like', "%{$search}%")
                ->orWhere('d_dosen.nidn_dosen', 'like', "%{$search}%");
            });
        }
    
        
        $dosen = $query->paginate(3);
        
        return view('pages.Koordinator.data_dosen', compact('dosen', 'search'), [
            'titleSidebar' => 'Data Dosen',
        ]);
    }

    public function tambahDataDosen(Request $request) {
        try {
            \Log::info('Starting tambahDataDosen', [
                'has_file' => $request->hasFile('profile_img_dosen'),
                'single_mode' => $request->input('is_single') === '1',
                'request_keys' => $request->keys()
            ]);
            
            $isSingle = $request->input('is_single') === '1';
            
            if ($isSingle) {
                // Validasi data single - make image optional
                $request->validate([
                    'nama_dosen' => 'required|string|max:255',
                    'nidn_dosen' => 'required|string|unique:d_dosen,nidn_dosen',
                    'email_dosen' => 'required|email|unique:d_user,email',
                    'status' => 'required|in:Active,Rejected,Pending,Disabled',
                    'profile_img_dosen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Made optional
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
                    'status.required' => 'Status harus diisi.',
                    'email_dosen.email' => 'Format email tidak valid.',
                    'profile_img_dosen.image' => 'File harus berupa gambar.',
                    'profile_img_dosen.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                    'profile_img_dosen.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                ]);
    
                // Cek duplikat NIDN dan email
                $nidnExists = DB::table('d_dosen')->where('nidn_dosen', $request->input('nidn_dosen'))->exists();
                $emailExists = DB::table('d_user')->where('email', $request->input('email_dosen'))->exists();
                
                if ($nidnExists) {
                    return back()->withInput()->withErrors(['nidn_dosen' => 'NIDN sudah ada di daftar data dosen.']);
                } else if ($emailExists) {
                    return back()->withInput()->withErrors(['email_dosen' => 'Email sudah ada di daftar data dosen.']);
                }
                
                // Generate UUID
                $userId = Str::uuid();
                $dosenId = Str::uuid();
                
                // Begin transaction
                DB::beginTransaction();
                
                try {
                    // Insert user data
                    DB::table('d_user')->insert([
                        'user_id' => $userId,
                        'email' => $request->input('email_dosen'),
                        'password' => bcrypt($request->input('password') ?: $request->input('nidn_dosen')),
                        'role' => 'Dosen',
                        'status' => $request->input('status', 'Active'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ]);
                    
                    // Persiapkan data dosen
                    $dosenData = [
                        'dosen_id' => $dosenId,
                        'user_id' => $userId,
                        'nama_dosen' => $request->input('nama_dosen'),
                        'nidn_dosen' => $request->input('nidn_dosen'),
                        'tanggal_lahir_dosen' => $request->filled('tanggal_lahir_dosen') ? $request->input('tanggal_lahir_dosen') : null,
                        'jenis_kelamin_dosen' => $request->input('jenis_kelamin_dosen'),
                        'telepon_dosen' => $request->input('telepon_dosen'),
                        'created_at' => now(),
                        'created_by' => session('user_id'),
                    ];
                    
                    // Handle profile image upload - THE IMPORTANT PART
                    if ($request->hasFile('profile_img_dosen')) {
                        $file = $request->file('profile_img_dosen');
                        
                        \Log::info('File upload attempt', [
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getClientMimeType(),
                            'size' => $file->getSize(),
                            'error' => $file->getError(),
                            'is_valid' => $file->isValid()
                        ]);
                        
                        if ($file->isValid()) {
                            // Create directory if it doesn't exist
                            $uploadPath = public_path('uploads/profile_dosen');
                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }
                            
                            // Generate unique filename
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            
                            try {
                                // Use move method (more reliable than move_uploaded_file)
                                if ($file->move($uploadPath, $filename)) {
                                    // Store relative path in database
                                    $dosenData['profile_img_dosen'] = 'uploads/profile_dosen/' . $filename;
                                    
                                    \Log::info('File successfully uploaded', [
                                        'path' => $dosenData['profile_img_dosen'],
                                        'full_path' => $uploadPath . '/' . $filename
                                    ]);
                                } else {
                                    throw new \Exception("Failed to move uploaded file");
                                }
                            } catch (\Exception $e) {
                                \Log::error('File upload exception', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                throw $e;
                            }
                        } else {
                            \Log::error('File is not valid', ['error_code' => $file->getError()]);
                            throw new \Exception("Uploaded file is not valid");
                        }
                    } else {
                        \Log::info('No file in request for single mode', [
                            'has_file' => $request->hasFile('profile_img_dosen'),
                            'request_keys' => $request->keys()
                        ]);
                    }
                    
                    // Insert dosen data
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
                // Mode multiple
                $dosenData = json_decode($request->input('dosen_data'), true);
                
                \Log::info('Multiple mode data', [
                    'dosenData' => $dosenData,
                    'files' => $request->files->all() 
                ]);
                
                if (empty($dosenData)) {
                    return redirect()->route('koordinator.dataDosen')->with('error', 'Tidak ada data dosen untuk ditambahkan.');
                }
                
                DB::beginTransaction();
                
                try {
                    $insertedCount = 0;
                    $errors = [];
                    
                    foreach ($dosenData as $index => $dosen) {
                        // Validate required fields
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
                        
                        // Insert user data
                        DB::table('d_user')->insert([
                            'user_id' => $userId,
                            'email' => $dosen['email_dosen'],
                            'password' => bcrypt($dosen['password'] ?: $dosen['nidn_dosen']),
                            'role' => 'Dosen',
                            'status' => $dosen['status'] ?? 'Active',
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ]);
                        
                        $jenisKelamin = null;
                        if (!empty($dosen['jenis_kelamin_dosen'])) {
                            if ($dosen['jenis_kelamin_dosen'] === 'Laki-laki') {
                                $jenisKelamin = 'Laki-Laki';
                            } else if ($dosen['jenis_kelamin_dosen'] === 'Perempuan') {
                                $jenisKelamin = 'Perempuan';
                            } else {
                                $jenisKelamin = $dosen['jenis_kelamin_dosen'];
                            }
                        }
                        
                        // Prepare dosen record
                        $dosenRecord = [
                            'dosen_id' => $dosenId,
                            'user_id' => $userId,
                            'nama_dosen' => $dosen['nama_dosen'],
                            'nidn_dosen' => $dosen['nidn_dosen'],
                            'tanggal_lahir_dosen' => !empty($dosen['tanggal_lahir_dosen']) ? $dosen['tanggal_lahir_dosen'] : null,
                            'jenis_kelamin_dosen' => $jenisKelamin,
                            'telepon_dosen' => $dosen['telepon_dosen'] ?? null,
                            'created_at' => now(),
                            'created_by' => session('user_id'),
                        ];
                        
                        // Check if this entry has a file
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
                                // Create directory if it doesn't exist
                                $uploadPath = public_path('uploads/profile_dosen');
                                if (!is_dir($uploadPath)) {
                                    mkdir($uploadPath, 0777, true);
                                }
                                
                                // Generate unique filename
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                // Move the file
                                if ($file->move($uploadPath, $filename)) {
                                    // Set the file path in the record
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
                        
                        // Insert the dosen record
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

    public function checkEmailNidnExists(Request $request)
    {
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
}