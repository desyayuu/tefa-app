<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMitraController extends Controller
{
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
        $mitra = $query->paginate(2);
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
}
