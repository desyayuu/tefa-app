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
        $summaryData = $this->countSummaryProyek();
        $proyekPoster = $this->getProyekPoster();
        
        return view('pages.landing_page', compact('jenisProyek', 'summaryData', 'proyekPoster'));
    }

    public function countSummaryProyek(){
        $summaryData = [];
        
        // Total Proyek (hanya yang belum dihapus)
        $summaryData['total_proyek'] = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->count();
            
        // Total Partisipasi Mahasiswa (distinct mahasiswa yang terlibat dalam proyek)
        $summaryData['total_mahasiswa'] = DB::table('t_project_member_mahasiswa')
            ->join('m_proyek', 't_project_member_mahasiswa.proyek_id', '=', 'm_proyek.proyek_id')
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->distinct()
            ->count('t_project_member_mahasiswa.mahasiswa_id');
            
        // Total Dosen yang terlibat (sebagai leader atau member)
        $dosenAsLeader = DB::table('t_project_leader')
            ->join('m_proyek', 't_project_leader.proyek_id', '=', 'm_proyek.proyek_id')
            ->where('t_project_leader.leader_type', 'Dosen')
            ->whereNull('t_project_leader.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_leader.leader_id');
            
        $dosenAsMember = DB::table('t_project_member_dosen')
            ->join('m_proyek', 't_project_member_dosen.proyek_id', '=', 'm_proyek.proyek_id')
            ->whereNull('t_project_member_dosen.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_member_dosen.dosen_id');
            
        $allDosen = $dosenAsLeader->merge($dosenAsMember)->unique();
        $summaryData['total_dosen'] = $allDosen->count();

        $profesionalAsLeader = DB::table('t_project_leader')
            ->join('m_proyek', 't_project_leader.proyek_id', '=', 'm_proyek.proyek_id')
            ->where('t_project_leader.leader_type', 'Profesional')
            ->whereNull('t_project_leader.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_leader.leader_id');
        
        $profesionalAsMember = DB::table('t_project_member_profesional')
            ->join('m_proyek', 't_project_member_profesional.proyek_id', '=', 'm_proyek.proyek_id')
            ->whereNull('t_project_member_profesional.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->pluck('t_project_member_profesional.profesional_id');
        $allProfesional = $profesionalAsLeader->merge($profesionalAsMember)->unique();
        $summaryData['total_profesional'] = $allProfesional->count();
        
        // Total Mitra Industri/Perusahaan
        $summaryData['total_mitra'] = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->distinct()
            ->count('mitra_proyek_id');
            
            
        return $summaryData;
    }

    public function getProyekPoster(){
        return DB::table('d_luaran_proyek')
            ->join('m_proyek', 'd_luaran_proyek.proyek_id', '=', 'm_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.dana_pendanaan',
                'd_luaran_proyek.luaran_proyek_id',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNotNull('d_luaran_proyek.poster_proyek')
            ->where('d_luaran_proyek.poster_proyek', '!=', '')
            ->whereNull('d_luaran_proyek.deleted_at')
            ->whereNull('m_proyek.deleted_at')
            ->orderBy('m_proyek.created_at', 'desc')
            ->limit(10) 
            ->get();
    }

    public function getAllProyek(Request $request)
    {
        $search = $request->get('search');
        $jenisProyekFilter = $request->get('jenis_proyek');
        
        // STEP 1: Get unique projects first (tanpa dokumentasi untuk menghindari duplikasi)
        $proyekQuery = DB::table('m_proyek')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.created_at',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNull('m_proyek.deleted_at');

        // Apply search filters
        if ($search) {
            $proyekQuery->where(function($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('m_proyek.deskripsi_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('m_jenis_proyek.nama_jenis_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('d_mitra_proyek.nama_mitra', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by jenis proyek
        if ($jenisProyekFilter) {
            $proyekQuery->where('m_proyek.jenis_proyek_id', $jenisProyekFilter);
        }

        // Get paginated projects
        $proyekList = $proyekQuery->orderBy('m_proyek.created_at', 'desc')
                                ->paginate(12)
                                ->appends($request->query());

        // STEP 2: Untuk setiap proyek, ambil luaran dan dokumentasi terpisah
        $proyekList->getCollection()->transform(function ($proyek) {
            // Get luaran proyek pertama untuk proyek ini
            $luaranProyek = DB::table('d_luaran_proyek')
                ->where('proyek_id', $proyek->proyek_id)
                ->whereNull('deleted_at')
                ->select('luaran_proyek_id', 'poster_proyek', 'link_proyek', 'deskripsi_luaran')
                ->first();

            // Set default values
            $proyek->luaran_proyek_id = $luaranProyek->luaran_proyek_id ?? null;
            $proyek->poster_proyek = $luaranProyek->poster_proyek ?? null;
            $proyek->link_proyek = $luaranProyek->link_proyek ?? null;
            $proyek->deskripsi_luaran = $luaranProyek->deskripsi_luaran ?? null;

            // Get latest documentation jika ada luaran proyek
            $latestDokumentasi = null;
            if ($proyek->luaran_proyek_id) {
                $latestDokumentasi = DB::table('d_dokumentasi_proyek')
                    ->where('luaran_proyek_id', $proyek->luaran_proyek_id)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->select('path_file', 'nama_file', 'created_at')
                    ->first();
            }
            
            // Determine display image with priority: latest documentation > poster > default
            $imageToUse = 'images/default-project-poster.jpg';
            $imageSource = 'default';
            
            // Priority 1: Latest documentation (if it's an image)
            if ($latestDokumentasi && $latestDokumentasi->path_file && file_exists(public_path($latestDokumentasi->path_file))) {
                $extension = strtolower(pathinfo($latestDokumentasi->path_file, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $imageToUse = $latestDokumentasi->path_file;
                    $imageSource = 'documentation';
                    $proyek->dokumentasi_date = $latestDokumentasi->created_at;
                    $proyek->dokumentasi_nama = $latestDokumentasi->nama_file;
                }
            }
            
            // Priority 2: Poster (fallback)
            if ($imageSource === 'default' && $proyek->poster_proyek && file_exists(public_path($proyek->poster_proyek))) {
                $imageToUse = $proyek->poster_proyek;
                $imageSource = 'poster';
            }
            
            $proyek->display_image = $imageToUse;
            $proyek->image_source = $imageSource;
            
            // Truncate description for card display
            $proyek->short_description = $proyek->deskripsi_luaran 
                ? (strlen($proyek->deskripsi_luaran) > 150 
                    ? substr($proyek->deskripsi_luaran, 0, 150) . '...' 
                    : $proyek->deskripsi_luaran)
                : (strlen($proyek->deskripsi_proyek) > 150 
                    ? substr($proyek->deskripsi_proyek, 0, 150) . '...' 
                    : $proyek->deskripsi_proyek);
            
            // Format dates
            $proyek->formatted_start_date = $proyek->tanggal_mulai ? date('d M Y', strtotime($proyek->tanggal_mulai)) : '-';
            $proyek->formatted_end_date = $proyek->tanggal_selesai ? date('d M Y', strtotime($proyek->tanggal_selesai)) : '-';
            $proyek->formatted_doc_date = isset($proyek->dokumentasi_date) ? date('d M Y', strtotime($proyek->dokumentasi_date)) : '-';
            
            return $proyek;
        });

        $jenisProyek = $this->getJenisProyek();
        
        return view('pages.portofolio_proyek', compact(
            'proyekList', 
            'jenisProyek', 
            'search', 
            'jenisProyekFilter', 
        ));
    }

    public function getAllProyekOptimized(Request $request)
    {
        $search = $request->get('search');
        $jenisProyekFilter = $request->get('jenis_proyek');
        
        // Single query dengan GROUP BY untuk menghindari duplikasi
        $query = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.created_at',
                // Ambil luaran proyek pertama saja
                DB::raw('MIN(d_luaran_proyek.luaran_proyek_id) as luaran_proyek_id'),
                DB::raw('MIN(d_luaran_proyek.poster_proyek) as poster_proyek'),
                DB::raw('MIN(d_luaran_proyek.link_proyek) as link_proyek'),
                DB::raw('MIN(d_luaran_proyek.deskripsi_luaran) as deskripsi_luaran'),
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNull('m_proyek.deleted_at')
            ->groupBy(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.created_at',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            );

        // Apply search filters
        if ($search) {
            $query->havingRaw("
                m_proyek.nama_proyek LIKE ? OR 
                m_proyek.deskripsi_proyek LIKE ? OR
                MIN(d_luaran_proyek.deskripsi_luaran) LIKE ? OR
                m_jenis_proyek.nama_jenis_proyek LIKE ? OR
                d_mitra_proyek.nama_mitra LIKE ?
            ", [
                '%' . $search . '%',
                '%' . $search . '%', 
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            ]);
        }

        // Filter by jenis proyek
        if ($jenisProyekFilter) {
            $query->where('m_proyek.jenis_proyek_id', $jenisProyekFilter);
        }

        $proyekList = $query->orderBy('m_proyek.created_at', 'desc')
                        ->paginate(12)
                        ->appends($request->query());

        // Process each project untuk dokumentasi terbaru
        $proyekList->getCollection()->transform(function ($proyek) {
            // Get latest documentation
            $latestDokumentasi = null;
            if ($proyek->luaran_proyek_id) {
                $latestDokumentasi = DB::table('d_dokumentasi_proyek')
                    ->where('luaran_proyek_id', $proyek->luaran_proyek_id)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->select('path_file', 'nama_file', 'created_at')
                    ->first();
            }
            
            // Set display image priority: latest documentation > poster > default
            $imageToUse = 'images/default-project-poster.jpg';
            $imageSource = 'default';
            
            if ($latestDokumentasi && $latestDokumentasi->path_file && file_exists(public_path($latestDokumentasi->path_file))) {
                $extension = strtolower(pathinfo($latestDokumentasi->path_file, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $imageToUse = $latestDokumentasi->path_file;
                    $imageSource = 'documentation';
                    $proyek->dokumentasi_date = $latestDokumentasi->created_at;
                    $proyek->dokumentasi_nama = $latestDokumentasi->nama_file;
                }
            }
            
            // Fallback to poster
            if ($imageSource === 'default' && $proyek->poster_proyek && file_exists(public_path($proyek->poster_proyek))) {
                $imageToUse = $proyek->poster_proyek;
                $imageSource = 'poster';
            }
            
            $proyek->display_image = $imageToUse;
            $proyek->image_source = $imageSource;
            
            // Process other fields (same as before)
            $proyek->short_description = $proyek->deskripsi_luaran 
                ? (strlen($proyek->deskripsi_luaran) > 150 
                    ? substr($proyek->deskripsi_luaran, 0, 150) . '...' 
                    : $proyek->deskripsi_luaran)
                : (strlen($proyek->deskripsi_proyek) > 150 
                    ? substr($proyek->deskripsi_proyek, 0, 150) . '...' 
                    : $proyek->deskripsi_proyek);
            
            $proyek->formatted_start_date = $proyek->tanggal_mulai ? date('d M Y', strtotime($proyek->tanggal_mulai)) : '-';
            $proyek->formatted_end_date = $proyek->tanggal_selesai ? date('d M Y', strtotime($proyek->tanggal_selesai)) : '-';
            $proyek->formatted_doc_date = isset($proyek->dokumentasi_date) ? date('d M Y', strtotime($proyek->dokumentasi_date)) : '-';
            
            return $proyek;
        });

        $jenisProyek = $this->getJenisProyek();
        
        return view('pages.portofolio_proyek', compact(
            'proyekList', 
            'jenisProyek', 
            'search', 
            'jenisProyekFilter', 
        ));
    }


    public function getAllProyekAlternative(Request $request)
    {
        $search = $request->get('search');
        $jenisProyekFilter = $request->get('jenis_proyek');
        $statusFilter = $request->get('status');
        
        $query = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'm_proyek.deskripsi_proyek',
                'm_proyek.status_proyek',
                'm_proyek.tanggal_mulai',
                'm_proyek.tanggal_selesai',
                'm_proyek.created_at',
                'd_luaran_proyek.luaran_proyek_id',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->whereNull('m_proyek.deleted_at');

        // Apply filters same as before...
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('m_proyek.nama_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('m_proyek.deskripsi_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('d_luaran_proyek.deskripsi_luaran', 'LIKE', '%' . $search . '%')
                ->orWhere('m_jenis_proyek.nama_jenis_proyek', 'LIKE', '%' . $search . '%')
                ->orWhere('d_mitra_proyek.nama_mitra', 'LIKE', '%' . $search . '%');
            });
        }

        if ($jenisProyekFilter) {
            $query->where('m_proyek.jenis_proyek_id', $jenisProyekFilter);
        }

        if ($statusFilter) {
            $query->where('m_proyek.status_proyek', $statusFilter);
        }

        $proyekList = $query->orderBy('m_proyek.created_at', 'desc')
                        ->paginate(12)
                        ->appends($request->query());

        // Process each project and get latest documentation
        $proyekList->getCollection()->transform(function ($proyek) {
            // Get latest documentation for this project
            $latestDokumentasi = null;
            if ($proyek->luaran_proyek_id) {
                $latestDokumentasi = DB::table('d_dokumentasi_proyek')
                    ->where('luaran_proyek_id', $proyek->luaran_proyek_id)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->select('path_file', 'nama_file', 'created_at')
                    ->first();
            }
            
            // Set display image priority: latest documentation > poster > default
            $imageToUse = 'images/default-project-poster.jpg';
            $imageSource = 'default';
            
            if ($latestDokumentasi && $latestDokumentasi->path_file && file_exists(public_path($latestDokumentasi->path_file))) {
                $extension = strtolower(pathinfo($latestDokumentasi->path_file, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $imageToUse = $latestDokumentasi->path_file;
                    $imageSource = 'documentation';
                    $proyek->dokumentasi_date = $latestDokumentasi->created_at;
                    $proyek->dokumentasi_nama = $latestDokumentasi->nama_file;
                }
            }
            
            // Fallback to poster if no documentation image
            if ($imageSource === 'default' && $proyek->poster_proyek && file_exists(public_path($proyek->poster_proyek))) {
                $imageToUse = $proyek->poster_proyek;
                $imageSource = 'poster';
            }
            
            $proyek->display_image = $imageToUse;
            $proyek->image_source = $imageSource;
            
            // Truncate description for card display
            $proyek->short_description = $proyek->deskripsi_luaran 
                ? (strlen($proyek->deskripsi_luaran) > 150 
                    ? substr($proyek->deskripsi_luaran, 0, 150) . '...' 
                    : $proyek->deskripsi_luaran)
                : (strlen($proyek->deskripsi_proyek) > 150 
                    ? substr($proyek->deskripsi_proyek, 0, 150) . '...' 
                    : $proyek->deskripsi_proyek);
            
            // Format dates
            $proyek->formatted_start_date = $proyek->tanggal_mulai ? date('d M Y', strtotime($proyek->tanggal_mulai)) : '-';
            $proyek->formatted_end_date = $proyek->tanggal_selesai ? date('d M Y', strtotime($proyek->tanggal_selesai)) : '-';
            $proyek->formatted_doc_date = isset($proyek->dokumentasi_date) ? date('d M Y', strtotime($proyek->dokumentasi_date)) : '-';
            
            return $proyek;
        });

        $jenisProyek = $this->getJenisProyek();
        $availableStatus = DB::table('m_proyek')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('status_proyek')
            ->filter()
            ->sort();
        
        return view('pages.portofolio_proyek', compact(
            'proyekList', 
            'jenisProyek', 
            'search', 
            'jenisProyekFilter', 
            'statusFilter',
        ));
    }

    public function getProyekDetail($proyekId)
    {
        $proyek = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->leftJoin('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->leftJoin('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->select(
                'm_proyek.*',
                'd_luaran_proyek.luaran_proyek_id',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.link_proyek',
                'd_luaran_proyek.deskripsi_luaran',
                'm_jenis_proyek.nama_jenis_proyek',
                'd_mitra_proyek.nama_mitra'
            )
            ->where('m_proyek.proyek_id', $proyekId)
            ->whereNull('m_proyek.deleted_at')
            ->first();

        if (!$proyek) {
            abort(404, 'Proyek tidak ditemukan');
        }

        // Get team members
        $mahasiswa = DB::table('t_project_member_mahasiswa')
            ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
            ->where('t_project_member_mahasiswa.proyek_id', $proyekId)
            ->whereNull('t_project_member_mahasiswa.deleted_at')
            ->select('d_mahasiswa.nama_mahasiswa')
            ->get();

        $dosenLeader = DB::table('t_project_member_dosen')
            ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
            ->where('t_project_member_dosen.proyek_id', $proyekId)
            ->whereNull('t_project_member_dosen.deleted_at')
            ->select('d_dosen.nama_dosen')->get();

        $dosenMember = DB::table('t_project_leader')
            ->join('d_dosen', 't_project_leader.leader_id', '=', 'd_dosen.dosen_id')
            ->where('t_project_leader.proyek_id', $proyekId)
            ->where('t_project_leader.leader_type', 'Dosen')
            ->whereNull('t_project_leader.deleted_at')
            ->select('d_dosen.nama_dosen')->get();
        $dosen = $dosenLeader->merge($dosenMember)->unique('nama_dosen');

        $profesionalLeader = DB::table('t_project_leader')
            ->join('d_profesional', 't_project_leader.leader_id', '=', 'd_profesional.profesional_id')
            ->where('t_project_leader.proyek_id', $proyekId)
            ->where('t_project_leader.leader_type', 'Profesional')
            ->whereNull('t_project_leader.deleted_at')
            ->select('d_profesional.nama_profesional')->get();
        
        $profesionalMember = DB::table('t_project_member_profesional')
            ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
            ->where('t_project_member_profesional.proyek_id', $proyekId)
            ->whereNull('t_project_member_profesional.deleted_at')
            ->select('d_profesional.nama_profesional')->get();

        $profesional = $profesionalLeader->merge($profesionalMember)->unique('nama_profesional');

        $leaders = DB::table('t_project_leader')
            ->where('proyek_id', $proyekId)
            ->whereNull('deleted_at')
            ->get();

        $teamSummary = [
            'total_members' => $mahasiswa->count() + $dosen->count(),
            'mahasiswa_count' => $mahasiswa->count(),
            'dosen_count' => $dosen->count(),
            'leaders_count' => $leaders->count(),
            'profesional_count' => $profesional->count(),
        ];

        // Improved documentation query with additional details
        $dokumentasi = collect();
        if ($proyek->luaran_proyek_id) {
            $dokumentasi = DB::table('d_dokumentasi_proyek')
                ->where('luaran_proyek_id', $proyek->luaran_proyek_id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->select(
                    'd_dokumentasi_proyek.dokumentasi_proyek_id',
                    'd_dokumentasi_proyek.path_file',
                    'd_dokumentasi_proyek.nama_file',
                    'd_dokumentasi_proyek.created_at'
                )
                ->get()
                ->map(function ($doc) {
                    // Add file info and validation
                    $doc->file_exists = file_exists(public_path($doc->path_file));
                    $doc->file_size = $doc->file_exists ? filesize(public_path($doc->path_file)) : 0;
                    $doc->is_image = in_array(strtolower(pathinfo($doc->path_file, PATHINFO_EXTENSION)), 
                        ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    return $doc;
                })
                ->filter(function ($doc) {
                    // Only show existing image files
                    return $doc->file_exists && $doc->is_image;
                });
        }

        // Get related projects (optional)
        $relatedProjects = DB::table('m_proyek')
            ->leftJoin('d_luaran_proyek', 'm_proyek.proyek_id', '=', 'd_luaran_proyek.proyek_id')
            ->where('m_proyek.jenis_proyek_id', $proyek->jenis_proyek_id)
            ->where('m_proyek.proyek_id', '!=', $proyekId)
            ->whereNull('m_proyek.deleted_at')
            ->whereNotNull('d_luaran_proyek.poster_proyek')
            ->select(
                'm_proyek.proyek_id',
                'm_proyek.nama_proyek',
                'd_luaran_proyek.poster_proyek',
                'd_luaran_proyek.deskripsi_luaran'
            )
            ->limit(3)
            ->get();

        return view('pages.detail_porto_proyek', compact(
            'proyek', 
            'mahasiswa', 
            'dosen',
            'profesional', 
            'leaders', 
            'teamSummary', 
            'dokumentasi',
            'relatedProjects'
        ));
    }
}