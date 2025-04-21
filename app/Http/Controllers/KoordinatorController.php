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

    public function getDataMitra(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('d_mitra_proyek')
            ->select('mitra_proyek_id', 'nama_mitra', 'telepon_mitra', 'email_mitra', 'alamat_mitra');
        
        // Apply search if search parameter exists
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('email_mitra', 'like', "%{$search}%")
                  ->orWhere('telepon_mitra', 'like', "%{$search}%");
            });
        }
        
        $mitra = $query->get();
        
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

    public function updateDataMitra(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_mitra' => 'required|string|max:255',
                'email_mitra' => 'nullable|email',
                'telepon_mitra' => 'nullable|string|max:20',
            ]);
    
            // Check if data exists
            $exists = DB::table('d_mitra_proyek')
                ->where('mitra_proyek_id', $id)
                ->exists();
                
            if (!$exists) {
                return redirect()->route('koordinator.dataMitra')->with('error', 'Data mitra tidak ditemukan.');
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

    public function deleteDataMitra($id)
    {
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
}

