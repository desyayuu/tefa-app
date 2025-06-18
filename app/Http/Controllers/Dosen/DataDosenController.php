<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataDosenController extends Controller
{
    private function getDosenId()
    {
        $dosenId = session('dosen_id');
        
        if (!$dosenId) {
            return response()->json(['message' => 'Data dosen tidak ditemukan'], 404);
        }
        
        return $dosenId;
    }

    public function getProfil(Request $request)
    {
        $dosenId = $this->getDosenId();
        
        if (!$dosenId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        // Get data dosen yang sedang login
        $dosen = DB::table('d_dosen as dosen')
            ->join('d_user as user', 'dosen.user_id', '=', 'user.user_id')
            ->select('dosen.*', 'user.*')
            ->where('dosen.dosen_id', $dosenId)
            ->whereNull('dosen.deleted_at')
            ->first();
            
        if (!$dosen) {
            return redirect()->route('login')->with('error', 'Data dosen tidak ditemukan.');
        }

        // Query untuk proyek sebagai leader
        $proyekLeaderQuery = DB::table('t_project_leader as leader')
            ->join('m_proyek as proyek', 'leader.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'proyek.proyek_id',
                'proyek.nama_proyek',
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai',
                'proyek.status_proyek',
                DB::raw("'Project Leader' as peran")
            )
            ->where('leader.leader_id', $dosenId) 
            ->where('leader.leader_type', 'Dosen')
            ->where('proyek.status_proyek', 'Done')
            ->whereNull('leader.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Query untuk proyek sebagai member
        $proyekMemberQuery = DB::table('t_project_member_dosen as member')
            ->join('m_proyek as proyek', 'member.proyek_id', '=', 'proyek.proyek_id')
            ->select(
                'proyek.proyek_id',
                'proyek.nama_proyek', 
                'proyek.tanggal_mulai',
                'proyek.tanggal_selesai',
                'proyek.status_proyek',
                DB::raw("'Project Member' as peran")
            )
            ->where('member.dosen_id', $dosenId) 
            ->where('proyek.status_proyek', 'Done')
            ->whereNull('member.deleted_at')
            ->whereNull('proyek.deleted_at');

        // Gabungkan kedua query dengan UNION dan buat pagination
        $combinedQuery = $proyekLeaderQuery->union($proyekMemberQuery);
            
        $riwayatProyek = DB::table(DB::raw("({$combinedQuery->toSql()}) as riwayat"))
            ->mergeBindings($combinedQuery)
            ->orderBy('riwayat.tanggal_selesai', 'desc')
            ->paginate(5, ['*'], 'riwayat_page');

        // Append dosen_id ke pagination links
        $riwayatProyek->appends(['dosen_id' => $dosenId]);

        return view('pages.Dosen.Pengaturan.detail_data_dosen', compact(
            'dosen', 
            'riwayatProyek' 
        ), [
            'titleSidebar' => 'Profil Akun'
        ]);
    }


    public function updateProfil(Request $request)
    {
        try {
            $dosenId = $this->getDosenId();
            
            if (!$dosenId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Get current dosen data
            $dosen = DB::table('d_dosen')
                ->where('dosen_id', $dosenId)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$dosen) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan.'
                ], 404);
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $dosen->user_id)
                ->first();

            // Buat aturan validasi
            $rules = [
                'nama_dosen' => 'required|string|max:255',
                'nidn_dosen' => 'required|string|max:10|regex:/^\d{10}$/',
                'email_dosen' => 'required|email',
                'profile_img_dosen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_dosen' => 'nullable|string|max:20',
                'tanggal_lahir_dosen' => 'nullable|date',
                'jenis_kelamin_dosen' => 'nullable|in:Laki-Laki,Perempuan',
                'password_dosen' => 'nullable|min:6'
            ];
            
            // Tambahkan aturan unique hanya jika nilai berubah
            if ($dosen->nidn_dosen != $request->input('nidn_dosen')) {
                $rules['nidn_dosen'] .= '|unique:d_dosen,nidn_dosen,' . $dosenId . ',dosen_id';
            }
            
            if ($user->email != $request->input('email_dosen')) {
                $rules['email_dosen'] .= '|unique:d_user,email,' . $dosen->user_id . ',user_id';
            }
            
            // Custom pesan error
            $messages = [
                'nidn_dosen.regex' => 'NIDN harus berupa angka dan tepat 10 digit.',
                'nidn_dosen.max' => 'NIDN maksimal 10 digit.',
                'nidn_dosen.unique' => 'NIDN sudah terdaftar dalam sistem.',
                'email_dosen.email' => 'Format email tidak valid.',
                'email_dosen.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_dosen.required' => 'Nama dosen harus diisi.',
                'nidn_dosen.required' => 'NIDN harus diisi.',
                'email_dosen.required' => 'Email harus diisi.',
                'profile_img_dosen.image' => 'File harus berupa gambar.',
                'profile_img_dosen.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_dosen.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                'password_dosen.min' => 'Password minimal 6 karakter.'
            ];
            
            // Validasi dengan aturan kustom
            $validator = Validator::make($request->all(), $rules, $messages);
            
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
            
            DB::beginTransaction();
            
            try {
                // Update user data
                $userData = [
                    'email' => $request->input('email_dosen'),
                    'updated_at' => now(),
                    'updated_by' => $dosenId,
                ];
                
                // Update password if provided
                if ($request->filled('password_dosen')) {
                    $userData['password'] = bcrypt($request->input('password_dosen'));
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
                    'updated_by' => $dosenId,
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
                    ->where('dosen_id', $dosenId)
                    ->update($dosenData);
                
                // Update session data jika nama berubah
                if ($request->input('nama_dosen') !== $dosen->nama_dosen) {
                    session(['nama_dosen' => $request->input('nama_dosen')]);
                }
                
                DB::commit();
                
                Log::info('Dosen profile updated successfully', [
                    'dosen_id' => $dosenId,
                    'updated_by' => $dosenId
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data dosen berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()
                    ->with('success', 'Profil berhasil diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating dosen profile', [
                    'dosen_id' => $dosenId,
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
            Log::error('Exception in updateProfil', [
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


    public function checkEmailNidnExists(Request $request){
        \Log::info('Check email/nidn request:', $request->all());
        
        $email = $request->input('email_dosen');
        $nidn = $request->input('nidn_dosen');
        $dosenId = $request->input('dosen_id');
        
        $emailExists = false;
        $nidnExists = false;
        
        if ($email) {
            $query = DB::table('d_user')
                ->where('email', $email);
                
            // Exclude current dosen when checking for duplicates
            if ($dosenId) {
                $dosen = DB::table('d_dosen')
                    ->where('dosen_id', $dosenId)
                    ->first();
                    
                if ($dosen) {
                    $query->where('user_id', '!=', $dosen->user_id);
                }
            }
            
            $emailExists = $query->exists();
        }
        
        if ($nidn) {
            $query = DB::table('d_dosen')
                ->where('nidn_dosen', $nidn);
                
            // Exclude current dosen when checking for duplicates
            if ($dosenId) {
                $query->where('dosen_id', '!=', $dosenId);
            }
            
            $nidnExists = $query->exists();
        }
        
        // Log hasil untuk debugging
        \Log::info('Check result:', [
            'email' => $email,
            'nidn' => $nidn,
            'dosenId' => $dosenId,
            'emailExists' => $emailExists,
            'nidnExists' => $nidnExists
        ]);
        
        return response()->json([
            'emailExists' => $emailExists,
            'nidnExists' => $nidnExists
        ]);
    }
}