<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataProfesionalController extends Controller
{
    private function getProfesionalId()
    {
        $profesionalId = session('profesional_id');
        
        if (!$profesionalId) {
            return null;
        }
        
        return $profesionalId;
    }

    public function getProfil(Request $request)
    {
        $profesionalId = $this->getProfesionalId();
        
        if (!$profesionalId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        // Get data profesional yang sedang login
        $profesional = DB::table('d_profesional as profesional')
            ->join('d_user as user', 'profesional.user_id', '=', 'user.user_id')
            ->select('profesional.*', 'user.*')
            ->where('profesional.profesional_id', $profesionalId)
            ->whereNull('profesional.deleted_at')
            ->first();
            
        if (!$profesional) {
            return redirect()->route('login')->with('error', 'Data profesional tidak ditemukan.');
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
            ->where('leader.leader_id', $profesionalId) 
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
                DB::raw("'Project Member' as peran")
            )
            ->where('member.profesional_id', $profesionalId) 
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
        $riwayatProyek->appends(['profesional_id' => $profesionalId]);

        return view('pages.Profesional.Pengaturan.detail_data_profesional', compact(
            'profesional', 
            'riwayatProyek' 
        ), [
            'titleSidebar' => 'Profil Akun'
        ]);
    }


    public function updateProfil(Request $request)
    {
        try {
            $profesionalId = $this->getProfesionalId();
            
            if (!$profesionalId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesi telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Get current profesional data
            $profesional = DB::table('d_profesional')
                ->where('profesional_id', $profesionalId)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$profesional) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data profesional tidak ditemukan.'
                ], 404);
            }
            
            $user = DB::table('d_user')
                ->where('user_id', $profesional->user_id)
                ->first();

            // Buat aturan validasi
            $rules = [
                'nama_profesional' => 'required|string|max:255',
                'email_profesional' => 'required|email',
                'profile_img_profesional' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'telepon_profesional' => 'nullable|string|max:20',
                'tanggal_lahir_profesional' => 'nullable|date',
                'jenis_kelamin_profesional' => 'nullable|in:Laki-Laki,Perempuan',
                'password_profesional' => 'nullable|min:6'
            ];
            
            
            if ($user->email != $request->input('email_profesional')) {
                $rules['email_profesional'] .= '|unique:d_user,email,' . $profesional->user_id . ',user_id';
            }
            
            // Custom pesan error
            $messages = [
                'email_profesional.email' => 'Format email tidak valid.',
                'email_profesional.unique' => 'Email sudah terdaftar dalam sistem.',
                'nama_profesional.required' => 'Nama profesional harus diisi.',
                'nidn_profesional.required' => 'NIDN harus diisi.',
                'email_profesional.required' => 'Email harus diisi.',
                'profile_img_profesional.image' => 'File harus berupa gambar.',
                'profile_img_profesional.mimes' => 'Format gambar tidak valid. Hanya jpeg, png, jpg, gif yang diperbolehkan.',
                'profile_img_profesional.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
                'password_profesional.min' => 'Password minimal 6 karakter.'
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
                    'email' => $request->input('email_profesional'),
                    'updated_at' => now(),
                    'updated_by' => $profesionalId,
                ];
                
                // Update password if provided
                if ($request->filled('password_profesional')) {
                    $userData['password'] = bcrypt($request->input('password_profesional'));
                }
                
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
                    'updated_by' => $profesionalId,
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
                    ->where('profesional_id', $profesionalId)
                    ->update($profesionalData);
                
                // Update session data jika nama berubah
                if ($request->input('nama_profesional') !== $profesional->nama_profesional) {
                    session(['nama_profesional' => $request->input('nama_profesional')]);
                }
                
                DB::commit();
                
                Log::info('Profesional profile updated successfully', [
                    'profesional_id' => $profesionalId,
                    'updated_by' => $profesionalId
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data profesional berhasil diperbarui.'
                    ]);
                }
                
                return redirect()->back()
                    ->with('success', 'Profil berhasil diperbarui.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating profesional profile', [
                    'profesional_id' => $profesionalId,
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
        
        $email = $request->input('email_profesional');
        $nidn = $request->input('nidn_profesional');
        $profesionalId = $request->input('profesional_id');
        
        $emailExists = false;
        $nidnExists = false;
        
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
        
        if ($nidn) {
            $query = DB::table('d_profesional')
                ->where('nidn_profesional', $nidn);
                
            // Exclude current profesional when checking for duplicates
            if ($profesionalId) {
                $query->where('profesional_id', '!=', $profesionalId);
            }
            
            $nidnExists = $query->exists();
        }
        
        // Log hasil untuk debugging
        \Log::info('Check result:', [
            'email' => $email,
            'nidn' => $nidn,
            'profesionalId' => $profesionalId,
            'emailExists' => $emailExists,
        ]);
        
        return response()->json([
            'emailExists' => $emailExists,
        ]);
    }
}