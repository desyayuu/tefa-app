<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KoordinatorController extends Controller
{
    public function dashboard()
    {
        $koordinator = DB::table('d_koordinator')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Koordinator.dashboard', compact('koordinator'));
    }
}
