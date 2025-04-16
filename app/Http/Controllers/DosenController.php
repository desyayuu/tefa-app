<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function dashboard()
    {
        $dosen = DB::table('d_dosen')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Dosen.dashboard', compact('dosen'));
    }
}
