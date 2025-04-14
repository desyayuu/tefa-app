<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show koordinator dashboard
     */
    public function koordinatorDashboard()
    {
        return view('pages.koordinator.dashboard');
    }
    
    /**
     * Show dosen dashboard
     */
    public function dosenDashboard()
    {
        return view('pages.dosen.dashboard');
    }
    
    /**
     * Show mahasiswa dashboard
     */
    public function mahasiswaDashboard()
    {
        return view('pages.mahasiswa.dashboard');
    }
}