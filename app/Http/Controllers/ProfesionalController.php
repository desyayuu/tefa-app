<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProfesionalController extends Controller
{
    public function dashboard()
    {
        $profesional = DB::table('d_profesional')
            ->where('user_id', session('user_id'))
            ->first();
            
        return view('pages.Profesional.dashboard', compact('profesional'), [
            'titleSidebar' => 'Dashboard'
        ]);
    }
}