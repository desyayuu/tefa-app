<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DataProgresProyekMahasiswaController extends Controller
{
    private function checkMahasiswaRole($proyekId, $mahasiswaId)
    {
        $isMember = DB::table('t_project_member_mahasiswa')
            ->where('proyek_id', $proyekId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->exists();

        // Mahasiswa tidak pernah bisa jadi leader, hanya member
        return ['isMember' => $isMember, 'isLeader' => false];
    }

    public function getProgresByProyek($id, Request $request){
        $mahasiswaId = session('mahasiswa_id');
        
        if (!$mahasiswaId) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $mahasiswaInfo = DB::table('d_mahasiswa')
            ->where('mahasiswa_id', $mahasiswaId)
            ->select('mahasiswa_id', 'nama_mahasiswa')
            ->first();

        // Check role mahasiswa dalam proyek
        $roleCheck = $this->checkMahasiswaRole($id, $mahasiswaId);
        
        if (!$roleCheck['isMember']) {
            return redirect()->route('mahasiswa.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
        }

        $proyek = DB::table('m_proyek')
            ->join('d_mitra_proyek', 'm_proyek.mitra_proyek_id', '=', 'd_mitra_proyek.mitra_proyek_id')
            ->join('m_jenis_proyek', 'm_proyek.jenis_proyek_id', '=', 'm_jenis_proyek.jenis_proyek_id')
            ->where('m_proyek.proyek_id', $id)
            ->select(
                'm_proyek.*', 
                'd_mitra_proyek.nama_mitra',
                'm_jenis_proyek.nama_jenis_proyek'
            )
            ->first();
        
        if (!$proyek) {
            return redirect()->route('mahasiswa.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }
        
        // Get search parameter
        $search = $request->input('search');
        $perPageProgresProyek = $request->input('per_page_progres_proyek', 3);
        $page = $request->input('page', 1);
        
        $query = DB::table('t_progres_proyek')
            ->where('t_progres_proyek.proyek_id', $id)
            ->whereNull('t_progres_proyek.deleted_at')
            ->leftJoin('t_project_leader', 't_progres_proyek.project_leader_id', '=', 't_project_leader.project_leader_id')
            ->leftJoin('d_dosen as leader_dosen', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'leader_dosen.dosen_id')
                    ->where('t_project_leader.leader_type', '=', 'Dosen');
            })
            ->leftJoin('d_profesional as leader_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'leader_profesional.profesional_id')
                    ->where('t_project_leader.leader_type', '=', 'Profesional');
            })
            ->leftJoin('t_project_member_dosen', 't_progres_proyek.project_member_dosen_id', '=', 't_project_member_dosen.project_member_dosen_id')
            ->leftJoin('d_dosen as member_dosen', 't_project_member_dosen.dosen_id', '=', 'member_dosen.dosen_id')
            ->leftJoin('t_project_member_profesional', 't_progres_proyek.project_member_profesional_id', '=', 't_project_member_profesional.project_member_profesional_id')
            ->leftJoin('d_profesional as member_profesional', 't_project_member_profesional.profesional_id', '=', 'member_profesional.profesional_id')
            ->leftJoin('t_project_member_mahasiswa', 't_progres_proyek.project_member_mahasiswa_id', '=', 't_project_member_mahasiswa.project_member_mahasiswa_id')
            ->leftJoin('d_mahasiswa as member_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'member_mahasiswa.mahasiswa_id')
            ->select(
                't_progres_proyek.*',
                'leader_dosen.nama_dosen as leader_dosen_nama',
                'leader_profesional.nama_profesional as leader_profesional_nama',
                'member_dosen.nama_dosen as member_dosen_nama',
                'member_profesional.nama_profesional as member_profesional_nama',
                'member_mahasiswa.nama_mahasiswa as member_mahasiswa_nama'
            );
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t_progres_proyek.nama_progres', 'like', "%{$search}%")
                ->orWhere('t_progres_proyek.deskripsi_progres', 'like', "%{$search}%")
                ->orWhere('leader_dosen.nama_dosen', 'like', "%{$search}%")
                ->orWhere('leader_profesional.nama_profesional', 'like', "%{$search}%")
                ->orWhere('member_dosen.nama_dosen', 'like', "%{$search}%")
                ->orWhere('member_profesional.nama_profesional', 'like', "%{$search}%")
                ->orWhere('member_mahasiswa.nama_mahasiswa', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $progres = $query->orderBy('t_progres_proyek.created_at', 'desc')
            ->paginate($perPageProgresProyek, ['*'], 'page', $page);
        
        // Get current mahasiswa member ID for permission checking
        $currentMahasiswaMemberId = DB::table('t_project_member_mahasiswa')
            ->where('proyek_id', $id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->value('project_member_mahasiswa_id');
        
        $progres->getCollection()->transform(function ($item) use ($roleCheck, $currentMahasiswaMemberId, $mahasiswaId) {
            $assignedName = 'Not Assigned';
            
            if (!empty($item->project_leader_id)) {
                if (!empty($item->leader_dosen_nama)) {
                    $assignedName = $item->leader_dosen_nama;
                } elseif (!empty($item->leader_profesional_nama)) {
                    $assignedName = $item->leader_profesional_nama;
                } else {
                    $assignedName = 'Project Leader';
                }
            } 
            elseif (!empty($item->project_member_dosen_id) && !empty($item->member_dosen_nama)) {
                $assignedName = $item->member_dosen_nama;
            } 
            elseif (!empty($item->project_member_profesional_id) && !empty($item->member_profesional_nama)) {
                $assignedName = $item->member_profesional_nama;
            } 
            elseif (!empty($item->project_member_mahasiswa_id) && !empty($item->member_mahasiswa_nama)) {
                $assignedName = $item->member_mahasiswa_nama;
            }
            
            $item->assigned_name = $assignedName;
            
            // Mahasiswa hanya bisa edit jika progres ditugaskan kepada mereka
            $item->can_edit = ($item->project_member_mahasiswa_id === $currentMahasiswaMemberId);
            
            // Mahasiswa hanya bisa delete jika progres ditugaskan kepada mereka DAN mereka yang membuat progres tersebut
            $item->can_delete = ($item->project_member_mahasiswa_id === $currentMahasiswaMemberId) && 
                                ($item->created_by == $mahasiswaId);
            
            // Unset kolom tambahan yang tidak diperlukan di frontend
            unset($item->leader_dosen_nama);
            unset($item->leader_profesional_nama);
            unset($item->member_dosen_nama);
            unset($item->member_profesional_nama);
            unset($item->member_mahasiswa_nama);
            
            $item->is_overdue = false;
            if ($item->status_progres === 'In Progress' && $item->tanggal_selesai_progres) {
                $currentDate = Carbon::now();
                $endDate = Carbon::parse($item->tanggal_selesai_progres);
                $item->is_overdue = $currentDate->gt($endDate);
            }
            return $item;
        });
        
        $paginationHtml = ''; 
        if($progres->hasPages()) {
            $paginationHtml = view('vendor.pagination.custom', [
                'paginator' => $progres,
                'elements' => $progres->links()->elements,
            ])->render();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'isLeader' => false, // Mahasiswa tidak pernah leader
                'isMember' => $roleCheck['isMember'],
                'currentMahasiswaMemberId' => $currentMahasiswaMemberId,
                'proyek' => $proyek,
                'data' => $progres->items(),
                'pagination' => [
                    'current_page' => $progres->currentPage(),
                    'per_page_progres_proyek' => $perPageProgresProyek,
                    'total' => $progres->total(),
                    'last_page' => $progres->lastPage(),
                    'html' => $paginationHtml
                ]
            ]);
        }
        
        return view('pages.Mahasiswa.DataProyek.data_progres_proyek', [
            'proyek' => $proyek,
            'progres' => $progres,
            'search' => $search,
            'mahasiswaInfo' => $mahasiswaInfo,
            'isLeader' => false, // Mahasiswa tidak pernah leader
            'isMember' => $roleCheck['isMember'],
            'currentMahasiswaMemberId' => $currentMahasiswaMemberId, 
            'mahasiswaInfo' => $mahasiswaInfo,
        ]);
    }

    public function getMyProgresByProyek($id, Request $request)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            $mahasiswaInfo = DB::table('d_mahasiswa')
                ->where('mahasiswa_id', $mahasiswaId)
                ->select('mahasiswa_id', 'nama_mahasiswa')
                ->first();

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkMahasiswaRole($id, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Check if project exists
            $proyek = DB::table('m_proyek')
                ->where('proyek_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$proyek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proyek tidak ditemukan'
                ], 404);
            }

            // Get search parameter
            $search = $request->input('search_my_progres_proyek', '');
            $perPageMyProgres = $request->input('per_page_my_progres', 3);
            $page = $request->input('page', 1);

            // Get current mahasiswa member ID
            $currentMahasiswaMemberId = DB::table('t_project_member_mahasiswa')
                ->where('proyek_id', $id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->value('project_member_mahasiswa_id');

            // Build query untuk My Progres - progres yang dibuat atau ditugaskan ke mahasiswa ini
            $query = DB::table('t_progres_proyek')
                ->where('t_progres_proyek.proyek_id', $id)
                ->whereNull('t_progres_proyek.deleted_at')
                ->where(function($q) use ($mahasiswaId, $currentMahasiswaMemberId) {
                    // Progres yang dibuat oleh mahasiswa ini
                    $q->where('t_progres_proyek.created_by', $mahasiswaId);
                    
                    // ATAU progres yang ditugaskan ke mahasiswa ini sebagai member
                    if ($currentMahasiswaMemberId) {
                        $q->orWhere('t_progres_proyek.project_member_mahasiswa_id', $currentMahasiswaMemberId);
                    }
                })
                ->leftJoin('t_project_leader', 't_progres_proyek.project_leader_id', '=', 't_project_leader.project_leader_id')
                ->leftJoin('d_dosen as leader_dosen', function($join) {
                    $join->on('t_project_leader.leader_id', '=', 'leader_dosen.dosen_id')
                        ->where('t_project_leader.leader_type', '=', 'Dosen');
                })
                ->leftJoin('d_profesional as leader_profesional', function($join) {
                    $join->on('t_project_leader.leader_id', '=', 'leader_profesional.profesional_id')
                        ->where('t_project_leader.leader_type', '=', 'Profesional');
                })
                ->leftJoin('t_project_member_dosen', 't_progres_proyek.project_member_dosen_id', '=', 't_project_member_dosen.project_member_dosen_id')
                ->leftJoin('d_dosen as member_dosen', 't_project_member_dosen.dosen_id', '=', 'member_dosen.dosen_id')
                ->leftJoin('t_project_member_profesional', 't_progres_proyek.project_member_profesional_id', '=', 't_project_member_profesional.project_member_profesional_id')
                ->leftJoin('d_profesional as member_profesional', 't_project_member_profesional.profesional_id', '=', 'member_profesional.profesional_id')
                ->leftJoin('t_project_member_mahasiswa', 't_progres_proyek.project_member_mahasiswa_id', '=', 't_project_member_mahasiswa.project_member_mahasiswa_id')
                ->leftJoin('d_mahasiswa as member_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'member_mahasiswa.mahasiswa_id')
                ->select(
                    't_progres_proyek.*',
                    'leader_dosen.nama_dosen as leader_dosen_nama',
                    'leader_profesional.nama_profesional as leader_profesional_nama',
                    'member_dosen.nama_dosen as member_dosen_nama',
                    'member_profesional.nama_profesional as member_profesional_nama',
                    'member_mahasiswa.nama_mahasiswa as member_mahasiswa_nama'
                );

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('t_progres_proyek.nama_progres', 'like', "%{$search}%")
                    ->orWhere('t_progres_proyek.deskripsi_progres', 'like', "%{$search}%")
                    ->orWhere('leader_dosen.nama_dosen', 'like', "%{$search}%")
                    ->orWhere('leader_profesional.nama_profesional', 'like', "%{$search}%")
                    ->orWhere('member_dosen.nama_dosen', 'like', "%{$search}%")
                    ->orWhere('member_profesional.nama_profesional', 'like', "%{$search}%")
                    ->orWhere('member_mahasiswa.nama_mahasiswa', 'like', "%{$search}%");
                });
            }

            // Pagination
            $myProgres = $query->orderBy('t_progres_proyek.created_at', 'desc')
                ->paginate($perPageMyProgres, ['*'], 'page', $page);

            // Process each item dengan logic mahasiswa
            $myProgres->getCollection()->transform(function ($item) use ($mahasiswaId, $currentMahasiswaMemberId, $roleCheck) {
                // Determine assigned name
                $assignedName = 'Not Assigned';
                
                if (!empty($item->project_leader_id)) {
                    if (!empty($item->leader_dosen_nama)) {
                        $assignedName = $item->leader_dosen_nama;
                    } elseif (!empty($item->leader_profesional_nama)) {
                        $assignedName = $item->leader_profesional_nama;
                    } else {
                        $assignedName = 'Project Leader';
                    }
                } 
                elseif (!empty($item->project_member_dosen_id) && !empty($item->member_dosen_nama)) {
                    $assignedName = $item->member_dosen_nama;
                } 
                elseif (!empty($item->project_member_profesional_id) && !empty($item->member_profesional_nama)) {
                    $assignedName = $item->member_profesional_nama;
                } 
                elseif (!empty($item->project_member_mahasiswa_id) && !empty($item->member_mahasiswa_nama)) {
                    $assignedName = $item->member_mahasiswa_nama;
                }
                
                $item->assigned_name = $assignedName;

                // Determine progress type untuk badge (mahasiswa logic)
                $isCreated = ($item->created_by == $mahasiswaId);
                $isAssignedAsMember = ($item->project_member_mahasiswa_id === $currentMahasiswaMemberId);

                if ($isCreated && $isAssignedAsMember) {
                    $item->progress_type = 'created';
                } elseif (!$isCreated && $isAssignedAsMember) {
                    $item->progress_type = 'assigned';
                } else {
                    $item->progress_type = 'unknown';
                }

                $item->can_edit = ($item->project_member_mahasiswa_id === $currentMahasiswaMemberId);
                $item->can_delete = ($item->project_member_mahasiswa_id === $currentMahasiswaMemberId) && 
                                  ($item->created_by == $mahasiswaId);

                // Unset kolom tambahan
                unset($item->leader_dosen_nama);
                unset($item->leader_profesional_nama);
                unset($item->member_dosen_nama);
                unset($item->member_profesional_nama);
                unset($item->member_mahasiswa_nama);

                $item->is_overdue = false;
                if ($item->status_progres === 'In Progress' && $item->tanggal_selesai_progres) {
                    $currentDate = Carbon::now();
                    $endDate = Carbon::parse($item->tanggal_selesai_progres);
                    $item->is_overdue = $currentDate->gt($endDate);
                }

                return $item;
            });

            $paginationHtml = ''; 
            if($myProgres->hasPages()) {
                $paginationHtml = view('vendor.pagination.custom', [
                    'paginator' => $myProgres,
                    'elements' => $myProgres->links()->elements,
                ])->render();
            }

            return response()->json([
                'success' => true,
                'isLeader' => false, // Mahasiswa tidak pernah leader
                'isMember' => $roleCheck['isMember'],
                'data' => $myProgres->items(),
                'mahasiswaInfo' => $mahasiswaInfo, 
                'pagination' => [
                    'current_page' => $myProgres->currentPage(),
                    'per_page_my_progres' => $perPageMyProgres,
                    'total' => $myProgres->total(),
                    'last_page' => $myProgres->lastPage(),
                    'html' => $paginationHtml
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data my progres: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getTeamMembers($proyekId)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkMahasiswaRole($proyekId, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Check if project exists
            $proyek = DB::table('m_proyek')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$proyek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proyek tidak ditemukan'
                ], 404);
            }
            
            // Get project leader
            $leader = DB::table('t_project_leader')
                ->where('proyek_id', $proyekId)
                ->whereNull('deleted_at')
                ->first();
                
            $leaderDetail = null;
                
            if ($leader) {
                if ($leader->leader_type === 'Dosen') {
                    $leaderDetail = DB::table('d_dosen')
                        ->where('dosen_id', $leader->leader_id)
                        ->select(
                            'dosen_id',
                            DB::raw("'{$leader->project_leader_id}' as project_leader_id"),
                            'nama_dosen as nama'
                        )
                        ->first();
                } else if ($leader->leader_type === 'Profesional') {
                    $leaderDetail = DB::table('d_profesional')
                        ->where('profesional_id', $leader->leader_id)
                        ->select(
                            'profesional_id',
                            DB::raw("'{$leader->project_leader_id}' as project_leader_id"),
                            'nama_profesional as nama'
                        )
                        ->first();
                }
            }
            
            // Get team members - mahasiswa bisa lihat semua anggota untuk informasi
            $dosenMembers = DB::table('t_project_member_dosen')
                ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
                ->where('t_project_member_dosen.proyek_id', $proyekId)
                ->whereNull('t_project_member_dosen.deleted_at')
                ->select(
                    't_project_member_dosen.project_member_dosen_id',
                    'd_dosen.dosen_id',
                    'd_dosen.nama_dosen'
                )
                ->get();
                
            $profesionalMembers = DB::table('t_project_member_profesional')
                ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
                ->where('t_project_member_profesional.proyek_id', $proyekId)
                ->whereNull('t_project_member_profesional.deleted_at')
                ->select(
                    't_project_member_profesional.project_member_profesional_id',
                    'd_profesional.profesional_id',
                    'd_profesional.nama_profesional'
                )
                ->get();
                
            $mahasiswaMembers = DB::table('t_project_member_mahasiswa')
                ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
                ->where('t_project_member_mahasiswa.proyek_id', $proyekId)
                ->whereNull('t_project_member_mahasiswa.deleted_at')
                ->select(
                    't_project_member_mahasiswa.project_member_mahasiswa_id',
                    'd_mahasiswa.mahasiswa_id',
                    'd_mahasiswa.nama_mahasiswa'
                )
                ->get();
                
            return response()->json([
                'success' => true,
                'isLeader' => false, // Mahasiswa tidak pernah leader
                'isMember' => $roleCheck['isMember'],
                'data' => [
                    'leader' => $leaderDetail,
                    'dosen' => $dosenMembers,
                    'profesional' => $profesionalMembers,
                    'mahasiswa' => $mahasiswaMembers
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tim: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function validateProgressDates(Request $request, $proyekId)
    {
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $proyekId)
            ->first();
            
        if (!$proyek) {
            return ['success' => false, 'message' => 'Proyek tidak ditemukan'];
        }
        
        $errors = [];
        
        if ($request->status_progres === 'In Progress') {
            if (empty($request->tanggal_mulai_progres)) {
                $errors['tanggal_mulai_progres'] = ['Tanggal mulai harus diisi untuk status In Progress'];
            }
            if (empty($request->tanggal_selesai_progres)) {
                $errors['tanggal_selesai_progres'] = ['Tanggal selesai harus diisi untuk status In Progress'];
            }
        }
        
        // Validate with project date range
        if ($request->tanggal_mulai_progres || $request->tanggal_selesai_progres) {
            $proyekMulai = Carbon::parse($proyek->tanggal_mulai);
            $proyekSelesai = Carbon::parse($proyek->tanggal_selesai);
            
            if ($request->tanggal_mulai_progres) {
                $tanggalMulai = Carbon::parse($request->tanggal_mulai_progres);
                
                if ($tanggalMulai->lt($proyekMulai)) {
                    $errors['tanggal_mulai_progres'] = ['Tanggal mulai tidak boleh sebelum tanggal mulai proyek (' . $proyekMulai->format('d-m-Y') . ')'];
                }
                
                if ($tanggalMulai->gt($proyekSelesai)) {
                    $errors['tanggal_mulai_progres'] = ['Tanggal mulai tidak boleh setelah tanggal selesai proyek (' . $proyekSelesai->format('d-m-Y') . ')'];
                }
            }
            
            if ($request->tanggal_selesai_progres) {
                $tanggalSelesai = Carbon::parse($request->tanggal_selesai_progres);
                
                if ($tanggalSelesai->lt($proyekMulai)) {
                    $errors['tanggal_selesai_progres'] = ['Tanggal selesai tidak boleh sebelum tanggal mulai proyek (' . $proyekMulai->format('d-m-Y') . ')'];
                }
                
                if ($tanggalSelesai->gt($proyekSelesai)) {
                    $errors['tanggal_selesai_progres'] = ['Tanggal selesai tidak boleh setelah tanggal selesai proyek (' . $proyekSelesai->format('d-m-Y') . ')'];
                }
            }
            
            if ($request->tanggal_mulai_progres && $request->tanggal_selesai_progres) {
                $tanggalMulai = Carbon::parse($request->tanggal_mulai_progres);
                $tanggalSelesai = Carbon::parse($request->tanggal_selesai_progres);
                
                if ($tanggalMulai->gt($tanggalSelesai)) {
                    $errors['tanggal_selesai_progres'] = ['Tanggal selesai tidak boleh sebelum tanggal mulai'];
                }
            }
        }
        
        return ['success' => empty($errors), 'errors' => $errors];
    }

    public function getProgresDetail($id, Request $request = null)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Find the progress record
            $progres = DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$progres) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data progres tidak ditemukan'
                ], 404);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkMahasiswaRole($progres->proyek_id, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Get current mahasiswa member ID for permission checking
            $currentMahasiswaMemberId = DB::table('t_project_member_mahasiswa')
                ->where('proyek_id', $progres->proyek_id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->value('project_member_mahasiswa_id');
            
            // Get assigned name berdasarkan assignment
            $assignedName = 'Tidak ditugaskan';
            $assignedType = null;
            
            if (!empty($progres->project_leader_id)) {
                $leader = DB::table('t_project_leader')
                    ->where('project_leader_id', $progres->project_leader_id)
                    ->whereNull('deleted_at')
                    ->first();
                    
                if ($leader) {
                    if ($leader->leader_type === 'Dosen') {
                        $leaderDetail = DB::table('d_dosen')
                            ->where('dosen_id', $leader->leader_id)
                            ->first();
                        $assignedName = $leaderDetail ? $leaderDetail->nama_dosen : 'Project Leader';
                    } else if ($leader->leader_type === 'Profesional') {
                        $leaderDetail = DB::table('d_profesional')
                            ->where('profesional_id', $leader->leader_id)
                            ->first();
                        $assignedName = $leaderDetail ? $leaderDetail->nama_profesional : 'Project Leader';
                    }
                    $assignedType = 'leader';
                }
            } 
            else if (!empty($progres->project_member_dosen_id)) {
                $dosen = DB::table('t_project_member_dosen')
                    ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
                    ->where('t_project_member_dosen.project_member_dosen_id', $progres->project_member_dosen_id)
                    ->select('d_dosen.nama_dosen')
                    ->first();
                
                $assignedName = $dosen ? $dosen->nama_dosen : 'Unknown Dosen';
                $assignedType = 'dosen';
            } 
            else if (!empty($progres->project_member_profesional_id)) {
                $profesional = DB::table('t_project_member_profesional')
                    ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_member_profesional.project_member_profesional_id', $progres->project_member_profesional_id)
                    ->select('d_profesional.nama_profesional')
                    ->first();
                
                $assignedName = $profesional ? $profesional->nama_profesional : 'Unknown Profesional';
                $assignedType = 'profesional';
            } 
            else if (!empty($progres->project_member_mahasiswa_id)) {
                $mahasiswa = DB::table('t_project_member_mahasiswa')
                    ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
                    ->where('t_project_member_mahasiswa.project_member_mahasiswa_id', $progres->project_member_mahasiswa_id)
                    ->select('d_mahasiswa.nama_mahasiswa')
                    ->first();
                
                $assignedName = $mahasiswa ? $mahasiswa->nama_mahasiswa : 'Unknown Mahasiswa';
                $assignedType = 'mahasiswa';
            }
            
            // Add assigned name and type to response
            $progres->assigned_name = $assignedName;
            $progres->assigned_type = $assignedType;
            
            // Permission checking untuk mahasiswa
            $isCreatedByCurrentUser = ($progres->created_by == $mahasiswaId);
            $canEditThisProgress = ($progres->project_member_mahasiswa_id === $currentMahasiswaMemberId);
            
            // Field yang bisa diedit oleh mahasiswa
            $editableFields = [];
            if ($canEditThisProgress) {
                // Mahasiswa bisa edit field dasar
                $editableFields = ['status_progres', 'persentase_progres', 'deskripsi_progres'];
                
                // Tambahan: bisa edit nama jika dia yang buat progres
                if ($isCreatedByCurrentUser) {
                    $editableFields[] = 'nama_progres';
                }
            }
            
            $proyekData = DB::table('m_proyek')
                ->where('proyek_id', $progres->proyek_id)
                ->select('proyek_id', 'nama_proyek', 'tanggal_mulai', 'tanggal_selesai')
                ->first();

            return response()->json([
                'success' => true,
                'isLeader' => false, // Mahasiswa tidak pernah leader
                'isMember' => $roleCheck['isMember'],
                'canEdit' => $canEditThisProgress,
                'editableFields' => $editableFields,
                'isCreatedByCurrentUser' => $isCreatedByCurrentUser,
                'proyek' => $proyekData,
                'data' => $progres
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data progres: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeMyProgres(Request $request)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkMahasiswaRole($request->proyek_id, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambah progres di proyek ini'
                ], 403);
            }

            // Check if single or multiple progres entries
            $isSingle = $request->input('is_single', 1);
            
            if ($isSingle == 1) {
                // Validate single progres entry
                $validator = Validator::make($request->all(), [
                    'proyek_id' => 'required|string|exists:m_proyek,proyek_id',
                    'nama_progres' => 'required|string|max:255',
                    'deskripsi_progres' => 'nullable|string',
                    'status_progres' => 'required|in:To Do,In Progress,Done',
                    'persentase_progres' => 'required|integer|min:0|max:100',
                    'tanggal_mulai_progres' => 'nullable|date',
                    'tanggal_selesai_progres' => 'nullable|date|after_or_equal:tanggal_mulai_progres',
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $dateValidation = $this->validateProgressDates($request, $request->proyek_id);
                if (!$dateValidation['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi tanggal gagal',
                        'errors' => $dateValidation['errors']
                    ], 422);
                }

                // Generate UUID for progres ID
                $progresId = (string) Str::uuid();
                
                // Get mahasiswa member ID - auto assign to self
                $projectMemberMahasiswaId = DB::table('t_project_member_mahasiswa')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('mahasiswa_id', $mahasiswaId)
                        ->whereNull('deleted_at')
                        ->value('project_member_mahasiswa_id');
                        
                if(!$projectMemberMahasiswaId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak terdaftar sebagai anggota proyek ini'
                    ], 403);
                }

                $convertEmptyToNull = function($value) {
                    return empty($value) ? null : $value;
                };
                
                // Insert data into t_progres_proyek table - auto assign to self
                DB::table('t_progres_proyek')->insert([
                    'progres_proyek_id' => $progresId,
                    'proyek_id' => $request->proyek_id,
                    'project_leader_id' => null, // Mahasiswa tidak bisa jadi leader
                    'project_member_dosen_id' => null,
                    'project_member_profesional_id' => null,
                    'project_member_mahasiswa_id' => $projectMemberMahasiswaId, // Auto assign to self
                    'assigned_to' => $projectMemberMahasiswaId,
                    'nama_progres' => $request->nama_progres,
                    'deskripsi_progres' => $request->deskripsi_progres,
                    'tanggal_mulai_progres' => $convertEmptyToNull($request->tanggal_mulai_progres),
                    'tanggal_selesai_progres' => $convertEmptyToNull($request->tanggal_selesai_progres),
                    'status_progres' => $request->status_progres,
                    'persentase_progres' => $request->persentase_progres,
                    'created_at' => Carbon::now(),
                    'created_by' => $mahasiswaId,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'My Progres berhasil ditambahkan',
                    'data' => [
                        'progres_id' => $progresId
                    ]
                ]);
                
            } else {
                // Handle multiple progres entries
                $progresData = json_decode($request->input('progres_data'), true);
                
                if (empty($progresData) || !is_array($progresData)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data progres tidak valid'
                    ], 422);
                }
                
                $insertedIds = [];
                $errors = [];
                
                DB::beginTransaction();
                
                // Get mahasiswa member ID for auto assignment
                $projectMemberMahasiswaId = DB::table('t_project_member_mahasiswa')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('mahasiswa_id', $mahasiswaId)
                        ->whereNull('deleted_at')
                        ->value('project_member_mahasiswa_id');

                if(!$projectMemberMahasiswaId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak terdaftar sebagai anggota proyek ini'
                    ], 403);
                }

                $convertEmptyToNull = function($value) {
                    return empty($value) ? null : $value;
                };
                
                foreach ($progresData as $index => $progres) {
                    // Generate UUID for each progres
                    $progresId = (string) Str::uuid();
                    
                    try {
                        // Insert data - auto assign to self as mahasiswa member
                        DB::table('t_progres_proyek')->insert([
                            'progres_proyek_id' => $progresId,
                            'proyek_id' => $request->proyek_id,
                            'project_leader_id' => null, // Mahasiswa tidak bisa jadi leader
                            'project_member_profesional_id' => null,
                            'project_member_dosen_id' => null,
                            'project_member_mahasiswa_id' => $projectMemberMahasiswaId, // Auto assign to self
                            'assigned_to' => $projectMemberMahasiswaId,
                            'nama_progres' => $progres['nama_progres'],
                            'deskripsi_progres' => $progres['deskripsi_progres'] ?? null,
                            'tanggal_mulai_progres' => $convertEmptyToNull($progres['tanggal_mulai_progres'] ?? null),
                            'tanggal_selesai_progres' => $convertEmptyToNull($progres['tanggal_selesai_progres'] ?? null),
                            'status_progres' => $progres['status_progres'],
                            'persentase_progres' => $progres['persentase_progres'],
                            'created_at' => Carbon::now(),
                            'created_by' => $mahasiswaId,
                        ]);
                        
                        $insertedIds[] = $progresId;
                        
                    } catch (\Exception $e) {
                        $errors[] = "Error pada data ke-" . ($index + 1) . ": " . $e->getMessage();
                    }
                }
                
                if (count($errors) > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menambahkan beberapa data progres',
                        'errors' => $errors
                    ], 422);
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Semua My Progres berhasil ditambahkan',
                    'data' => [
                        'inserted_ids' => $insertedIds
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateProgresProyek($id, Request $request)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Check if record exists
            $progres = DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$progres) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data progres tidak ditemukan'
                ], 404);
            }

            $roleCheck = $this->checkMahasiswaRole($progres->proyek_id, $mahasiswaId);
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            $currentMahasiswaMemberId = DB::table('t_project_member_mahasiswa')
                ->where('proyek_id', $progres->proyek_id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->value('project_member_mahasiswa_id');

            $canEditThisProgress = ($progres->project_member_mahasiswa_id === $currentMahasiswaMemberId);
                                
            if (!$canEditThisProgress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat mengedit progres yang tidak ditugaskan kepada Anda'
                ], 403);
            }

            $isCreatedByCurrentUser = ($progres->created_by == $mahasiswaId);
            $rules = [
                'status_progres' => 'required|in:To Do,In Progress,Done',
                'persentase_progres' => 'required|integer|min:0|max:100',
                'deskripsi_progres' => 'nullable|string',
                'tanggal_mulai_progres' => 'nullable|date',
                'tanggal_selesai_progres' => 'nullable|date|after_or_equal:tanggal_mulai_progres',
            ];
            
            if ($isCreatedByCurrentUser) {
                $rules['nama_progres'] = 'nullable|string|max:255';
            }
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'status_progres' => $request->status_progres,
                'persentase_progres' => $request->persentase_progres,
                'deskripsi_progres' => $request->deskripsi_progres,
                'tanggal_mulai_progres' => $request->tanggal_mulai_progres ? Carbon::parse($request->tanggal_mulai_progres) : null,
                'tanggal_selesai_progres' => $request->tanggal_selesai_progres ? Carbon::parse($request->tanggal_selesai_progres) : null,
                'updated_at' => Carbon::now(),
                'updated_by' => $mahasiswaId
            ];
            
            if ($isCreatedByCurrentUser && $request->has('nama_progres')) {
                $updateData['nama_progres'] = $request->nama_progres;
            }
            
            // Update the record
            DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->update($updateData);
                
            return response()->json([
                'success' => true,
                'message' => 'Data progres berhasil diperbarui',
                'data' => [
                    'progres_id' => $id
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProgresProyek($id)
    {
        try {
            $mahasiswaId = session('mahasiswa_id');
            
            if (!$mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 401);
            }

            // Check if progres exists
            $progres = DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->whereNull('deleted_at')
                ->first();
                
            if (!$progres) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data progres tidak ditemukan'
                ], 404);
            }

            // Check role mahasiswa dalam proyek
            $roleCheck = $this->checkMahasiswaRole($progres->proyek_id, $mahasiswaId);
            
            if (!$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Validasi untuk delete progres - mahasiswa hanya bisa delete progres mereka sendiri
            $mahasiswaMemberId = DB::table('t_project_member_mahasiswa')
                ->where('proyek_id', $progres->proyek_id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNull('deleted_at')
                ->value('project_member_mahasiswa_id');
            
            // Cek apakah progres di-assign ke mahasiswa ini
            if ($progres->project_member_mahasiswa_id !== $mahasiswaMemberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat menghapus progres yang ditugaskan kepada Anda'
                ], 403);
            }
            
            // Cek apakah progres dibuat oleh mahasiswa ini sendiri
            if ($progres->created_by != $mahasiswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus progres yang dibuat oleh project leader'
                ], 403);
            }
            
            // Soft delete the progres
            DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => $mahasiswaId
                ]);
                
            return response()->json([
                'success' => true,
                'message' => 'Data progres berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }
}