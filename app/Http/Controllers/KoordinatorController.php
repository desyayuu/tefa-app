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
            ->leftJoin('d_dosen', 'd_user.user_id', '=', 'd_dosen.user_id')
            ->leftJoin('d_profesional', 'd_user.user_id', '=', 'd_profesional.user_id')
            ->leftJoin('d_mahasiswa', 'd_user.user_id', '=', 'd_mahasiswa.user_id')
            ->leftJoin('d_koordinator', 'd_user.user_id', '=', 'd_koordinator.user_id');
    
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

    public function updateStatusUser(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:Active,Rejected,Pending,Disabled',
                'role' => 'required|in:Dosen,Mahasiswa,Profesional,Koordinator',
            ]);
    
            // Get user data
            $user = DB::table('d_user')
                ->where('user_id', $id)
                ->first();
    
            if (!$user) {
                return redirect()->route('koordinator.dataUser')->with('error', 'User tidak ditemukan.');
            }
    
            // Begin transaction
            DB::beginTransaction();
    
            try {
                // Update user table
                DB::table('d_user')
                    ->where('user_id', $id)
                    ->update([
                        'role' => $request->input('role'),
                        'status' => $request->input('status'),
                        'updated_at' => now(),
                        'updated_by' => session('user_id'),
                    ]);
    
                // Update role-specific data
                if ($request->input('role') == 'Dosen') {
                    DB::table('d_dosen')
                        ->updateOrInsert(
                            ['user_id' => $id],
                            [
                                'nama_dosen' => $request->input('nama_dosen'),
                                'nidn_dosen' => $request->input('nidn_dosen'),
                                'telepon_dosen' => $request->input('telepon_dosen'),
                                'updated_at' => now(),
                                'updated_by' => session('user_id'),
                            ]
                        );
                } elseif ($request->input('role') == 'Mahasiswa') {
                    DB::table('d_mahasiswa')
                        ->updateOrInsert(
                            ['user_id' => $id],
                            [
                                'nama_mahasiswa' => $request->input('nama_dosen'),
                                'nim' => $request->input('nim'),
                                'telepon_mahasiswa' => $request->input('telepon_mahasiswa'),
                                'updated_at' => now(),
                                'updated_by' => session('user_id'),
                            ]
                        );
                } elseif ($request->input('role') == 'Profesional') {
                    DB::table('d_profesional')
                        ->updateOrInsert(
                            ['user_id' => $id],
                            [
                                'nama_profesional' => $request->input('nama_dosen'),
                                'telepon_profesional' => $request->input('telepon_profesional'),
                                'updated_at' => now(),
                                'updated_by' => session('user_id'),
                            ]
                        );
                } elseif ($request->input('role') == 'Koordinator') {
                    DB::table('d_koordinator')
                        ->updateOrInsert(
                            ['user_id' => $id],
                            [
                                'nama_koordinator' => $request->input('nama_dosen'),
                                'nidn_koordinator' => $request->input('nidn_koordinator'),
                                'telepon_koordinator' => $request->input('telepon_koordinator'),
                                'updated_at' => now(),
                                'updated_by' => session('user_id'),
                            ]
                        );
                }
    
                DB::commit();
                return redirect()->route('koordinator.dataUser')->with('success', 'Data user berhasil diperbarui.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('koordinator.dataUser')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->route('koordinator.dataUser')->with('error', 'Validasi gagal: ' . $e->getMessage());
        }
    }
    

    public function getDataMitra(Request $request)
    {
        $search = $request->input('search');
    
        $query = DB::table('d_mitra_proyek')
            ->select('mitra_proyek_id', 'nama_mitra', 'telepon_mitra', 'email_mitra', 'alamat_mitra');
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('telepon_mitra', 'like', "%{$search}%");
            });
        }
        $mitra = $query->paginate(3);
        return view('pages.Koordinator.data_mitra', compact('mitra', 'search'), [
            'titleSidebar' => 'Data Mitra',
        ]);
    }
    
    
    public function storeDataMitra(Request $request)
    {
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
            // Check if data exists
            $mitra = DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->first();
                
            if (!$mitra) {
                return redirect()->route('koordinator.dataMitra')->with('error', 'Data mitra tidak ditemukan.');
            }
    
            // Delete data
            DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->delete();
    
            return redirect()->route('koordinator.dataMitra')
                ->with('success', 'Data mitra "' . $mitra->nama_mitra . '" berhasil dihapus.');
        } catch (\Exception $e) {
            // Handle database or other errors
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
    public function getDataDosen(Request $request)
    {
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
                ->orWhere('d_dosen.nidn', 'like', "%{$search}%");
            });
        }
    
        
        $dosen = $query->paginate(3);
        
        return view('pages.Koordinator.data_dosen', compact('dosen', 'search'), [
            'titleSidebar' => 'Data Dosen',
        ]);
    }
}