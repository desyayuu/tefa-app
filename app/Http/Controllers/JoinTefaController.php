<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pesan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JoinTefaController extends Controller
{

    public function getPesanPengunjung(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 

        $query = Pesan::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('perusahaan_pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('email_pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('pesan_pengirim', 'LIKE', "%{$search}%");
            });
        }

        $pesanPengunjung = $query->orderBy('created_at', 'desc')
                                ->paginate($perPage)
                                ->withQueryString(); 
        
        return view('pages.Koordinator.data_pesan_pengunjung', [
            'pesanPengunjung' => $pesanPengunjung,
            'search' => $search,
            'perPage' => $perPage,
            'titleSidebar' => 'Data Pesan Pengunjung',
        ]);
    }


    public function tambahPesanPengunjung(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:100',
                'perusahaan' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'telepon' => 'nullable|string|max:15',
                'pesan' => 'required|string|max:2000',
            ], [
                'nama.required' => 'Nama wajib diisi',
                'nama.max' => 'Nama maksimal 100 karakter',
                'perusahaan.required' => 'Perusahaan wajib diisi',
                'perusahaan.max' => 'Perusahaan maksimal 100 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.max' => 'Email maksimal 100 karakter',
                'telepon.max' => 'Telepon maksimal 15 karakter',
                'pesan.required' => 'Pesan wajib diisi',
                'pesan.max' => 'Pesan maksimal 2000 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang Anda masukkan tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pesan = Pesan::create([
                'pesan_id' => Str::uuid()->toString(),
                'nama_pengirim' => $request->nama,
                'perusahaan_pengirim' => $request->perusahaan,
                'email_pengirim' => $request->email,
                'telepon_pengirim' => $request->telepon,
                'pesan_pengirim' => $request->pesan,
                'created_by' => 'pengunjung', 
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Pesan Anda berhasil dikirim. Tim kami akan menghubungi Anda segera.',
                'data' => [
                    'pesan_id' => $pesan->pesan_id,
                    'nama' => $pesan->nama_pengirim
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error saat menyimpan pesan join proyek: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function updatePesanPengunjung(Request $request, $id)
    {
        try {
            $pesan = Pesan::findOrFail($id);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'nama_pengirim' => 'required|string|max:100',
                'perusahaan_pengirim' => 'required|string|max:100',
                'email_pengirim' => 'required|email|max:100',
                'telepon_pengirim' => 'nullable|string|max:15',
                'pesan_pengirim' => 'required|string|max:2000',
            ], [
                'nama_pengirim.required' => 'Nama wajib diisi',
                'nama_pengirim.max' => 'Nama maksimal 100 karakter',
                'perusahaan_pengirim.required' => 'Perusahaan wajib diisi',
                'perusahaan_pengirim.max' => 'Perusahaan maksimal 100 karakter',
                'email_pengirim.required' => 'Email wajib diisi',
                'email_pengirim.email' => 'Format email tidak valid',
                'email_pengirim.max' => 'Email maksimal 100 karakter',
                'telepon_pengirim.max' => 'Telepon maksimal 15 karakter',
                'pesan_pengirim.required' => 'Pesan wajib diisi',
                'pesan_pengirim.max' => 'Pesan maksimal 2000 karakter',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Data yang Anda masukkan tidak valid');
            }

            $pesan->update([
                'nama_pengirim' => $request->nama_pengirim,
                'perusahaan_pengirim' => $request->perusahaan_pengirim,
                'email_pengirim' => $request->email_pengirim,
                'telepon_pengirim' => $request->telepon_pengirim,
                'pesan_pengirim' => $request->pesan_pengirim,
                'updated_by' => auth()->user()->user_id ?? 'admin',
                'updated_at' => now(),
            ]);

            $redirectUrl = route('koordinator.getPesanPengunjung');
            
            $queryParams = [];
            if ($request->has('search')) {
                $queryParams['search'] = $request->get('search');
            }
            if ($request->has('page')) {
                $queryParams['page'] = $request->get('page');
            }
            if ($request->has('per_page')) {
                $queryParams['per_page'] = $request->get('per_page');
            }
            
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }

            return redirect($redirectUrl)
                ->with('success', 'Pesan dari ' . $pesan->nama_pengirim . ' berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error saat update pesan: ' . $e->getMessage(), [
                'pesan_id' => $id,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui pesan');
        }
    }

    public function hapusPesanPengunjung(Request $request, $id)
    {
        try {
            $pesan = Pesan::findOrFail($id);
            $namaPengirim = $pesan->nama_pengirim;

            // Soft delete pesan
            $pesan->update([
                'deleted_by' => auth()->user()->user_id ?? 'admin',
                'deleted_at' => now(),
            ]);

            $redirectUrl = route('koordinator.getPesanPengunjung');
            
            // Preserve search dan pagination parameters
            $queryParams = [];
            if ($request->has('search')) {
                $queryParams['search'] = $request->get('search');
            }
            if ($request->has('page')) {
                $queryParams['page'] = $request->get('page');
            }
            if ($request->has('per_page')) {
                $queryParams['per_page'] = $request->get('per_page');
            }
            
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }

            return redirect($redirectUrl)
                ->with('success', 'Pesan dari ' . $namaPengirim . ' berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Error saat hapus pesan: ' . $e->getMessage(), [
                'pesan_id' => $id,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus pesan');
        }
    }
}