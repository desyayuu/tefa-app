<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function getJenisProyek(){
        return DB::table('m_jenis_proyek')->get();
    }
    
    public function layananKami()
    {
        $jenisProyek = $this->getJenisProyek();
        return view('pages.layanan_kami', compact('jenisProyek'));
    }
    
    public function landingPage()
    {
        $jenisProyek = $this->getJenisProyek();
        return view('pages.landing_page', compact('jenisProyek'));
    }
}