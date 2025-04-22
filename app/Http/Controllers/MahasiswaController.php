<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $mahasiswa = DB::table('d_mahasiswa')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Mahasiswa.dashboard', compact('mahasiswa'), 
        [
            'titleSidebar' => 'Dashboard'
        ]);
    }
}
