<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataMahasiswaController extends Controller
{
    private function getMahasiswaId()
    {
        $mahasiswaId = session('mahasiswa_id');
        
        if (!$mahasiswaId) {
            return null;
        }
        return $mahasiswaId;
    }

    public function getProfil(Request $request)
    {
        $mahasiswaId = $this->getMahasiswaId();
        
        if (!$mahasiswaId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        // Get data mahasiswa yang sedang login
        $mahasiswa = DB::table('d_mahasiswa as mahasiswa')
            ->join('d_user as user', 'mahasiswa.user_id', '=', 'user.user_id')
            ->select('mahasiswa.*', 'user.*')
            ->where('mahasiswa.mahasiswa_id', $mahasiswaId)
            ->whereNull('mahasiswa.deleted_at')
            ->first();
            
        if (!$mahasiswa) {
            return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan.');
        }


        // Query untuk proyek sebagai member
        $proyekMemberQuery = DB::table('t_project_member_mahasiswa as member')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'proyek.proyek_id',
                'proyek.nama_proyek', 
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai',
                'proyek.status_proyek',
                DB::raw("'Project Member' as peran")
            )
            ->where('member.mahasiswa_id', $mahasiswaId) 
            ->where('proyek.status_proyek', 'Done')
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');
            
        $riwayatProyek = $proyekMemberQuery
            ->paginate(5, ['*'], 'riwayat_page');

        // Append mahasiswa_id ke pagination links
        $riwayatProyek->appends(['mahasiswa_id' => $mahasiswaId]);

        return view('pages.Mahasiswa.Pengaturan.detail_data_mahasiswa', compact(
            'mahasiswa', 
            'riwayatProyek' 
        ), [
            'titleSidebar' => 'Profil Akun'
        ]);
    }

    public function updateProfil(Request $request)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Get current mahasiswa data
            $mahasiswa = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan.'
                ], 404);
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $mahasiswa->user_id)
                ->first();

            // Buat aturan validasi
            $rules = [
                'nama_mahasiswa' => 'required|string|max:255',
                'nim_mahasiswa' => 'required|string|max:10|regex:/^\d{10}$/',
                'email_mahasiswa' => 'required|email',
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
                'password_mahasiswa' => 'nullable|min:6'
            ];
            
            // Tambahkan aturan unique hanya jika nilai berubah
            if ($mahasiswa->nim_mahasiswa != $request->input('nim_mahasiswa')) {
                $rules['nim_mahasiswa'] .= '|unique:d_mahasiswa,nim_mahasiswa,' . $mahasiswaId . ',mahasiswa_id';
            }
            
            if ($user->email != $request->input('email_mahasiswa')) {
                $rules['email_mahasiswa'] .= '|unique:d_user,email,' . $mahasiswa->user_id . ',user_id';
            }
            
            // Custom pesan error
            $messages = [
                'nim_mahasiswa.regex' => 'NIM harus berupa angka dan tepat 10 digit.',
                'nim_mahasiswa.max' => 'NIM maksimal 10 digit.',
                'nim_mahasiswa.unique' => 'NIM sudah terdaftar dalam sistem.',
                'email_mahasiswa.email' => 'Format email tidak valid.',
                'email_mahasiswa.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_mahasiswa.required' => 'Nama mahasiswa harus diisi.',
                'nim_mahasiswa.required' => 'NIM harus diisi.',
                'email_mahasiswa.required' => 'Email harus diisi.',
                'profile_img_mahasiswa.image' => 'File harus berupa gambar.',
                'profile_img_mahasiswa.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_mahasiswa.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                'doc_cv.mimes' => 'Format CV tidak valid. Hanya pdf, doc, docx yang diperbolehkan.',
                'doc_cv.max' => 'Ukuran CV terlalu besar. Maksimal 2MB.',
                'doc_ktp.mimes' => 'Format KTP tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                'doc_ktp.max' => 'Ukuran KTP terlalu besar. Maksimal 2MB.',
                'doc_ktm.mimes' => 'Format KTM tidak valid. Hanya pdf, jpg, jpeg, png yang diperbolehkan.',
                'doc_ktm.max' => 'Ukuran KTM terlalu besar. Maksimal 2MB.',
                'password_mahasiswa.min' => 'Password minimal 6 karakter.'
            ];
            
            // Validasi dengan aturan kustom
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            try {
                // Update user data
                $userData = [
                    'email' => $request->input('email_mahasiswa'),
                    'updated_at' => now(),
                    'updated_by' => $mahasiswaId,
                ];
                
                // Update password if provided
                if ($request->filled('password_mahasiswa')) {
                    $userData['password'] = bcrypt($request->input('password_mahasiswa'));
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
                    'deskripsi_diri' => $request->input('deskripsi_diri'),
                    'kelebihan_diri' => $request->input('kelebihan_diri'),
                    'kekurangan_diri' => $request->input('kekurangan_diri'),
                    'github' => $request->input('github'),
                    'linkedin' => $request->input('linkedin'),
                    'updated_at' => now(),
                    'updated_by' => $mahasiswaId,
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
                            // Pastikan direktori ada
                            $fullUploadPath = public_path($uploadPath);
                            if (!is_dir($fullUploadPath)) {
                                mkdir($fullUploadPath, 0755, true);
                            }
                            
                            // Generate nama file yang aman
                            $extension = strtolower($file->getClientOriginalExtension());
                            $filename = 'doc_mahasiswa_' . $mahasiswaId . '_' . time() . '.' . $extension;
                            
                            // Upload file baru
                            if ($file->move($fullUploadPath, $filename)) {
                                // Hapus file lama jika ada
                                $oldFile = $mahasiswa->{$fieldName};
                                if ($oldFile && $oldFile != '') {
                                    $oldFilePath = public_path($oldFile);
                                    if (file_exists($oldFilePath)) {
                                        unlink($oldFilePath);
                                    }
                                }
                                
                                // Set path file baru ke database
                                $mahasiswaData[$fieldName] = $uploadPath . '/' . $filename;
                            } else {
                                throw new \Exception("Gagal mengupload $fieldName");
                            }
                        } else {
                            throw new \Exception("File $fieldName tidak valid");
                        }
                    }
                }
                
                DB::table('d_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->update($mahasiswaData);
                
                // Update session data jika nama berubah
                if ($request->input('nama_mahasiswa') !== $mahasiswa->nama_mahasiswa) {
                    session(['nama_mahasiswa' => $request->input('nama_mahasiswa')]);
                }
                
                DB::commit();
                
                Log::info('Mahasiswa profile updated successfully', [
                    'mahasiswa_id' => $mahasiswaId,
                    'updated_by' => $mahasiswaId
                ]);
                
                // Check if request is AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Profil berhasil diperbarui.',
                        'redirect' => route('mahasiswa.getProfilMahasiswa')
                    ]);
                }
                
                // Fallback for non-AJAX requests
                return redirect()->back()
                    ->with('success', 'Profil berhasil diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating mahasiswa profile', [
                    'mahasiswa_id' => $mahasiswaId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                    ], 500);
                }
                
                return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Exception in updateProfil', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function checkEmailNimExistsForProfile(Request $request)
    {
        try {
            $mahasiswaId = $this->getMahasiswaId();
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir',
                    'redirect' => route('mahasiswa.getProfilMahasiswa')
                ], 401);
            }

            $response = [
                'emailExists' => false,
                'nimExists' => false
            ];

            // Check email
            if ($request->has('email_mahasiswa')) {
                $email = $request->input('email_mahasiswa');
                
                $emailExists = DB::table('d_user as u')
                    ->join('d_mahasiswa as m', 'u.user_id', '=', 'm.user_id')
                    ->where('u.email', $email)
                    ->where('m.mahasiswa_id', '!=', $mahasiswaId)
                    ->whereNull('m.deleted_at')
                    ->exists();
                    
                $response['emailExists'] = $emailExists;
            }

            // Check NIM
            if ($request->has('nim_mahasiswa')) {
                $nim = $request->input('nim_mahasiswa');
                
                $nimExists = DB::table('d_mahasiswa')
                    ->where('nim_mahasiswa', $nim)
                    ->where('mahasiswa_id', '!=', $mahasiswaId)
                    ->whereNull('deleted_at')
                    ->exists();
                    
                $response['nimExists'] = $nimExists;
            }

            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Error checking email/NIM existence for profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If this is an AJAX request, return JSON with redirect instruction
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memeriksa data',
                    'redirect' => route('mahasiswa.getProfilMahasiswa')
                ], 500);
            }
            
            // If not AJAX, redirect directly
            return redirect()->route('mahasiswa.getProfilMahasiswa')->with('error', 'Terjadi kesalahan saat memeriksa data. Silakan coba lagi.');
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
}