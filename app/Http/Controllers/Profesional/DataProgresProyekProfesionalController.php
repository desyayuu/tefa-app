<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DataProgresProyekProfesionalController extends Controller
{
    private function checkProfesionalRole($proyekId, $profesionalId)
    {
        $isLeader = DB::table('t_project_leader')
            ->where('proyek_id', $proyekId)
            ->where('leader_type', 'Profesional')
            ->where('leader_id', $profesionalId)
            ->exists();

        $isMember = DB::table('t_project_member_profesional')
            ->where('proyek_id', $proyekId)
            ->where('profesional_id', $profesionalId)
            ->whereNull('deleted_at')
            ->exists();

        return ['isLeader' => $isLeader, 'isMember' => $isMember];
    }

    public function getTeamMembers($proyekId)
    {
        try {
            $profesionalId = session(('profesional_id'));
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profesional tidak ditemukan'
                ], 401);
            }

            //Check role profesional dalam proyek
            $roleCheck = $this->checkProfesionalRole($proyekId, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
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
                if ($leader->leader_type === 'Profesional') {
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
            
            // Get team members 
            $dosenMembers = collect();
            $profesionalMembers = collect();
            $mahasiswaMembers = collect();
            
            if ($roleCheck['isLeader']) {
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
            } else {
                // Member hanya bisa lihat diri sendiri
                $profesionalMembers = DB::table('t_project_member_profesional')
                    ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_member_profesional.proyek_id', $proyekId)
                    ->where('t_project_member_profesional.profesional_id', $profesionalId)
                    ->whereNull('t_project_member_profesional.deleted_at')
                    ->select(
                        't_project_member_profesional.project_member_profesional_id',
                        'd_profesional.profesional_id',
                        'd_profesional.nama_profesional'
                    )
                    ->get();
            }
                
            return response()->json([
                'success' => true,
                'isLeader' => $roleCheck['isLeader'], 
                'isMember' => $roleCheck['isMember'], 
                'proyek' => $proyek,
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

    public function getProgresByProyek($id, Request $request){
        $profesionalId = session('profesional_id');
        
        if (!$profesionalId) {
            return redirect()->route('profesional.dashboard')->with('error', 'Data profesional tidak ditemukan');
        }

        $profesionalInfo = DB::table('d_profesional')
            ->where('profesional_id', $profesionalId)
            ->select('profesional_id', 'nama_profesional')
            ->first();

        // Check role profesional dalam proyek
        $roleCheck = $this->checkProfesionalRole($id, $profesionalId);
        
        if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
            return redirect()->route('profesional.dataProyek')->with('error', 'Anda tidak memiliki akses ke proyek ini');
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
            return redirect()->route('profesional.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }
        
        // Get search parameter
        $search = $request->input('search');
        $perPageProgresProyek = $request->input('per_page_progres_proyek', 3);
        $page = $request->input('page', 1);
        
        // Search
        $query = DB::table('t_progres_proyek')
            ->where('t_progres_proyek.proyek_id', $id)
            ->whereNull('t_progres_proyek.deleted_at')
            ->leftJoin('t_project_leader', 't_progres_proyek.project_leader_id', '=', 't_project_leader.project_leader_id')
            ->leftJoin('d_profesional as leader_profesional', function($join) {
                $join->on('t_project_leader.leader_id', '=', 'leader_profesional.profesional_id')
                    ->where('t_project_leader.leader_type', '=', 'Profesional');
            })
            // LEFT JOIN untuk mendapatkan nama dosen member
            ->leftJoin('t_project_member_dosen', 't_progres_proyek.project_member_dosen_id', '=', 't_project_member_dosen.project_member_dosen_id')
            ->leftJoin('d_dosen as member_dosen', 't_project_member_dosen.dosen_id', '=', 'member_dosen.dosen_id')
            // LEFT JOIN untuk mendapatkan nama profesional member
            ->leftJoin('t_project_member_profesional', 't_progres_proyek.project_member_profesional_id', '=', 't_project_member_profesional.project_member_profesional_id')
            ->leftJoin('d_profesional as member_profesional', 't_project_member_profesional.profesional_id', '=', 'member_profesional.profesional_id')
            // LEFT JOIN untuk mendapatkan nama mahasiswa member
            ->leftJoin('t_project_member_mahasiswa', 't_progres_proyek.project_member_mahasiswa_id', '=', 't_project_member_mahasiswa.project_member_mahasiswa_id')
            ->leftJoin('d_mahasiswa as member_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'member_mahasiswa.mahasiswa_id')
            ->select(
                't_progres_proyek.*',
                'leader_profesional.nama_profesional as leader_profesional_nama',
                'member_dosen.nama_dosen as member_dosen_nama',
                'member_profesional.nama_profesional as member_profesional_nama',
                'member_mahasiswa.nama_mahasiswa as member_mahasiswa_nama'
            );
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t_progres_proyek.nama_progres', 'like', "%{$search}%")
                ->orWhere('t_progres_proyek.deskripsi_progres', 'like', "%{$search}%")
                ->orWhere('leader_profesional.nama_profesional', 'like', "%{$search}%")
                ->orWhere('member_dosen.nama_dosen', 'like', "%{$search}%")
                ->orWhere('member_profesional.nama_profesional', 'like', "%{$search}%")
                ->orWhere('member_mahasiswa.nama_mahasiswa', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $progres = $query->orderBy('t_progres_proyek.created_at', 'asc')
            ->paginate($perPageProgresProyek, ['*'], 'page', $page);
        
        // Get current dosen member ID for permission checking
        $currentProfesionalMemberId = null;
        if (!$roleCheck['isLeader']) {
            $currentProfesionalMemberId = DB::table('t_project_member_profesional')
                ->where('proyek_id', $id)
                ->where('profesional_id', $profesionalId)
                ->whereNull('deleted_at')
                ->value('project_member_profesional_id');
        }
        
        // PERBAIKAN: Process each item dengan optimized assignee name processing
        $progres->getCollection()->transform(function ($item) use ($roleCheck, $currentProfesionalMemberId, $profesionalId) {
            $assignedName = 'Not Assigned';
            
            if (!empty($item->project_leader_id)) {
                if (!empty($item->leader_profesional_nama)) {
                    $assignedName = $item->leader_profesional_nama;
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
            $item->can_edit = $roleCheck['isLeader'] || 
                            ($item->project_member_profesional_id === $currentProfesionalMemberId);
            
            // Logic delete permission
            if ($roleCheck['isLeader']) {
                $item->can_delete = true;
            } else {
                $item->can_delete = ($item->project_member_profesional_id === $currentProfesionalMemberId) && 
                                ($item->created_by == $profesionalId);
            }
            
            unset($item->leader_dosen_nama);
            unset($item->leader_profesional_nama);
            unset($item->member_dosen_nama);
            unset($item->member_profesional_nama);
            unset($item->member_mahasiswa_nama);
            
            // Check if task is overdue
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
                'isLeader' => $roleCheck['isLeader'],
                'isMember' => $roleCheck['isMember'],
                'currentProfesionalMemberId' => $currentProfesionalMemberId,
                'data' => $progres->items(),
                'proyek' => $proyek,
                'pagination' => [
                    'current_page' => $progres->currentPage(),
                    'per_page_progres_proyek' => $perPageProgresProyek,
                    'total' => $progres->total(),
                    'last_page' => $progres->lastPage(),
                    'html' => $paginationHtml
                ]
            ]);
        }
        
        return view('pages.Dosen.DataProyek.data_progres_proyek', [
            'proyek' => $proyek,
            'progres' => $progres,
            'search' => $search,
            'profesionalInfo' => $profesionalInfo,
            'isLeader' => $roleCheck['isLeader'],
            'isMember' => $roleCheck['isMember'],
            'currentProfesionalMemberId' => $currentProfesionalMemberId
        ]);
    }

    private function validateProgressDates(Request $request, $proyekId)
    {
        // Get project dates
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $proyekId)
            ->first();
            
        if (!$proyek) {
            return ['success' => false, 'message' => 'Proyek tidak ditemukan'];
        }
        
        $errors = [];
        
        // Valdisasi tanggal berdasarkan status progres
        if ($request->status_progres === 'In Progress') {
            // tanggal mulai dan selesai jika in progres harus diisi
            if (empty($request->tanggal_mulai_progres)) {
                $errors['tanggal_mulai_progres'] = ['Actual start date is required for In Progress status'];
            }
            if (empty($request->tanggal_selesai_progres)) {
                $errors['tanggal_selesai_progres'] = ['Expected end date is required for In Progress status'];
            }
        }
        
        // Validasi dengan range tanggal proyek
        if ($request->tanggal_mulai_progres || $request->tanggal_selesai_progres) {
            $proyekMulai = Carbon::parse($proyek->tanggal_mulai);
            $proyekSelesai = Carbon::parse($proyek->tanggal_selesai);
            
            // Validasi tanggal mulai jika diisi
            if ($request->tanggal_mulai_progres) {
                $tanggalMulai = Carbon::parse($request->tanggal_mulai_progres);
                
                if ($tanggalMulai->lt($proyekMulai)) {
                    $errors['tanggal_mulai_progres'] = ['Start date cannot be before project start date (' . $proyekMulai->format('d-m-Y') . ')'];
                }
                
                if ($tanggalMulai->gt($proyekSelesai)) {
                    $errors['tanggal_mulai_progres'] = ['Start date cannot be after project end date (' . $proyekSelesai->format('d-m-Y') . ')'];
                }
            }
            
            // Validasi tanggal selesai jika diisi
            if ($request->tanggal_selesai_progres) {
                $tanggalSelesai = Carbon::parse($request->tanggal_selesai_progres);
                
                if ($tanggalSelesai->lt($proyekMulai)) {
                    $errors['tanggal_selesai_progres'] = ['End date cannot be before project start date (' . $proyekMulai->format('d-m-Y') . ')'];
                }
                
                if ($tanggalSelesai->gt($proyekSelesai)) {
                    $errors['tanggal_selesai_progres'] = ['End date cannot be after project end date (' . $proyekSelesai->format('d-m-Y') . ')'];
                }
            }
            
            // Validasi start vs end date jika keduanya diisi
            if ($request->tanggal_mulai_progres && $request->tanggal_selesai_progres) {
                $tanggalMulai = Carbon::parse($request->tanggal_mulai_progres);
                $tanggalSelesai = Carbon::parse($request->tanggal_selesai_progres);
                
                if ($tanggalMulai->gt($tanggalSelesai)) {
                    $errors['tanggal_selesai_progres'] = ['End date cannot be before start date'];
                }
            }
        }
        
        return ['success' => empty($errors), 'errors' => $errors];
    }

    public function getMyProgresByProyek($id, Request $request)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profesional tidak ditemukan'
                ], 401);
            }

            $profesionalInfo = DB::table('d_profesional')
                ->where('profesional_id', $profesionalId)
                ->select('profesional_id', 'nama_profesional')
                ->first();

            // Check role profesional dalam proyek
            $roleCheck = $this->checkProfesionalRole($id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
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

            // Get current profesional member ID
            $currentProfesionalMemberId = null;
            $currentLeaderId = null;
            
            if ($roleCheck['isLeader']) {
                $currentLeaderId = DB::table('t_project_leader')
                    ->where('proyek_id', $id)
                    ->where('leader_type', 'Profesional')
                    ->where('leader_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_leader_id');
            }
            
            if ($roleCheck['isMember']) {
                $currentProfesionalMemberId = DB::table('t_project_member_profesional')
                    ->where('proyek_id', $id)
                    ->where('profesional_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_member_profesional_id');
            }

            // Build query untuk My Progres - progres yang dibuat atau ditugaskan ke dosen ini
            $query = DB::table('t_progres_proyek')
                ->where('t_progres_proyek.proyek_id', $id)
                ->whereNull('t_progres_proyek.deleted_at')
                ->where(function($q) use ($profesionalId, $currentProfesionalMemberId, $currentLeaderId) {
                    // Progres yang dibuat oleh profesional ini
                    $q->where('t_progres_proyek.created_by', $profesionalId);
                    
                    // ATAU progres yang ditugaskan ke profesional ini sebagai member
                    if ($currentProfesionalMemberId) {
                        $q->orWhere('t_progres_proyek.project_member_profesional_id', $currentProfesionalMemberId);
                    }
                    
                    // ATAU progres yang ditugaskan ke profesional ini sebagai leader
                    if ($currentLeaderId) {
                        $q->orWhere('t_progres_proyek.project_leader_id', $currentLeaderId);
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
                    ->orWhere('member_mahasiswa.nama_mahasiswa', 'like', "%{$search}%")
                    ->orWhere('t_progres_proyek.status_progres', 'like', "%{$search}%")
                    ->orWhere('t_progres_proyek.status_progres', 'like', "%{$search}%");
                });
            }

            // Pagination
            $myProgres = $query->orderBy('t_progres_proyek.created_at', 'desc')
                ->paginate($perPageMyProgres, ['*'], 'page', $page);

            // Process each item
            $myProgres->getCollection()->transform(function ($item) use ($profesionalId, $currentProfesionalMemberId, $currentLeaderId, $roleCheck) {
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

                // Determine progress type dan creation status
                $isCreatedByCurrentUser = ($item->created_by == $profesionalId);
                $isAssignedAsLeader = ($item->project_leader_id === $currentLeaderId);
                $isAssignedAsMember = ($item->project_member_profesional_id === $currentProfesionalMemberId);
                $isAssigned = $isAssignedAsLeader || $isAssignedAsMember;

                if ($isCreatedByCurrentUser && $isAssigned) {
                    $item->progress_type = 'created';
                } elseif (!$isCreatedByCurrentUser && $isAssigned) {
                    $item->progress_type = 'assigned';
                } else {
                    $item->progress_type = 'unknown';
                }

                // SPECIAL LOGIC untuk My Progres: Permission flags
                $item->can_edit = $roleCheck['isLeader'] || 
                                ($item->project_member_profesional_id === $currentProfesionalMemberId) ||
                                ($item->project_leader_id === $currentLeaderId);
                
                // MODIFIKASI LOGIC DELETE untuk My Progres
                if ($roleCheck['isLeader']) {
                    // CASE KHUSUS: Project Leader di My Progres
                    if ($isCreatedByCurrentUser) {
                        // Jika dia yang buat progres → BISA DELETE
                        $item->can_delete = true;
                        $item->delete_reason = 'created_by_leader';
                    } else {
                        // Jika progres di-assign kepada dia dari koordinator → TIDAK BISA DELETE
                        $item->can_delete = false;
                        $item->delete_reason = 'assigned_from_coordinator';
                    }
                } else {
                    // LOGIC EXISTING untuk Member
                    $item->can_delete = (($item->project_member_profesional_id === $currentProfesionalMemberId) || 
                                    ($item->project_leader_id === $currentLeaderId)) && 
                                    ($item->created_by == $profesionalId);
                    $item->delete_reason = $item->can_delete ? 'created_by_member' : 'not_creator';
                }

                // TAMBAHAN: Flag untuk frontend 
                $item->is_created_by_current_user = $isCreatedByCurrentUser;
                $item->is_assigned_from_coordinator = !$isCreatedByCurrentUser && $isAssigned;

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
                'isLeader' => $roleCheck['isLeader'],
                'isMember' => $roleCheck['isMember'],
                'data' => $myProgres->items(),
                'profesionalInfo' => $profesionalInfo,
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

    public function updateProgresProyek($id, Request $request)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
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

            // Check role dosen dalam proyek
            $roleCheck = $this->checkProfesionalRole($progres->proyek_id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Get current dosen member ID and leader ID
            $currentProfesionalId = null;
            $currentLeaderId = null;
            
            if (!$roleCheck['isLeader']) {
                $currentProfesionalId = DB::table('t_project_member_profesional')
                    ->where('proyek_id', $progres->proyek_id)
                    ->where('profesional_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_member_profesional_id');
            }
            
            if ($roleCheck['isLeader']) {
                $currentLeaderId = DB::table('t_project_leader')
                    ->where('proyek_id', $progres->proyek_id)
                    ->where('leader_type', 'Profesional')
                    ->where('leader_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_leader_id');
            }

            // Validasi hak edit berdasarkan role
            $canEditThisProgress = $roleCheck['isLeader'] || 
                                ($progres->project_member_profesional_id === $currentProfesionalId) ||
                                ($progres->project_leader_id === $currentLeaderId);
                                
            if (!$canEditThisProgress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak untuk mengedit progres ini'
                ], 403);
            }

            // TAMBAHAN: Check if user created this progress (untuk nama progres)
            $isCreatedByCurrentUser = ($progres->created_by == $profesionalId);

            $convertEmptyToNull = function($value) {
                return empty($value) ? null : $value;
            };

            $tanggalMulaiToSave = null;
            $tanggalSelesaiToSave = null;

            $dateValidation = $this->validateProgressDates($request, $progres->proyek_id);
            if (!$dateValidation['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi tanggal gagal',
                    'errors' => $dateValidation['errors']
                ], 422);
            }

            if ($request->status_progres === 'In Progress') {
                // get data tanggal dari request jika ada, atau convert ke null jika kosong
                $tanggalMulaiToSave = $convertEmptyToNull($request->tanggal_mulai_progres);
                $tanggalSelesaiToSave = $convertEmptyToNull($request->tanggal_selesai_progres);
                
            } else {
                // untuk to do, get tanggal dari request jika ada, atau preserve tanggal lama
                if (!empty($request->tanggal_mulai_progres)) {
                    $tanggalMulaiToSave = $request->tanggal_mulai_progres;
                } else {
                    // tanggal yang sudah ada atau bisa null
                    $tanggalMulaiToSave = $progres->tanggal_mulai_progres;
                }
                
                if (!empty($request->tanggal_selesai_progres)) {
                    $tanggalSelesaiToSave = $request->tanggal_selesai_progres;
                } else {
                    // tanggal yang sudah ada atau bisa null
                    $tanggalSelesaiToSave = $progres->tanggal_selesai_progres;
                }
            }

            // UPDATED: Different validation rules based on role and creation status
            if ($roleCheck['isLeader']) {
                // Leader bisa update semua field
                $validator = Validator::make($request->all(), [
                    'nama_progres' => 'required|string|max:255',
                    'status_progres' => 'required|in:To Do,In Progress,Done',
                    'persentase_progres' => 'required|integer|min:0|max:100',
                    'deskripsi_progres' => 'nullable|string',
                    'tanggal_mulai_progres' => 'nullable|date',
                    'tanggal_selesai_progres' => 'nullable|date|after_or_equal:tanggal_mulai_progres',
                    'assigned_to' => 'nullable|string',
                    'assigned_type' => 'nullable|in:leader,dosen,profesional,mahasiswa',
                ]);
            } else {
                // Member validation rules tergantung apakah dia yang buat progres
                $rules = [
                    'status_progres' => 'required|in:To Do,In Progress,Done',
                    'persentase_progres' => 'required|integer|min:0|max:100',
                    'deskripsi_progres' => 'nullable|string',
                    'tanggal_mulai_progres' => 'nullable|date',
                    'tanggal_selesai_progres' => 'nullable|date|after_or_equal:tanggal_mulai_progres',
                ];
                
                // TAMBAHAN: Hanya bisa edit nama jika dia yang buat progres
                if ($isCreatedByCurrentUser) {
                    $rules['nama_progres'] = 'nullable|string|max:255';
                }
                
                $validator = Validator::make($request->all(), $rules);
            }
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // TAMBAHAN: Validasi tanggal dengan range proyek
            $dateValidation = $this->validateProgressDates($request, $progres->proyek_id);
            if (!$dateValidation['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi tanggal gagal',
                    'errors' => $dateValidation['errors']
                ], 422);
            }

            // Prepare update data based on role and creation status
            $updateData = [
                'updated_at' => Carbon::now(),
                'updated_by' => $profesionalId
            ];

            $convertEmptyToNull = function($value) {
                return empty($value) ? null : $value;
            };
            
            if ($roleCheck['isLeader']) {
                // Leader bisa update semua field
                $updateData['nama_progres'] = $request->nama_progres;
                $updateData['status_progres'] = $request->status_progres;
                $updateData['persentase_progres'] = $request->persentase_progres;
                $updateData['deskripsi_progres'] = $request->deskripsi_progres;
                $updateData['tanggal_mulai_progres'] = $tanggalMulaiToSave;
                $updateData['tanggal_selesai_progres'] = $tanggalSelesaiToSave;
                
                // Handle assignment
                $assignedTo = $request->input('assigned_to');
                $updateData['project_leader_id'] = null;
                $updateData['project_member_dosen_id'] = null;
                $updateData['project_member_profesional_id'] = null;
                $updateData['project_member_mahasiswa_id'] = null;
                
                if ($request->has('assigned_type') && !empty($assignedTo)) {
                    switch ($request->assigned_type) {
                        case 'leader':
                            $updateData['project_leader_id'] = $assignedTo;
                            break;
                        case 'dosen':
                            $updateData['project_member_dosen_id'] = $assignedTo;
                            break;
                        case 'profesional':
                            $updateData['project_member_profesional_id'] = $assignedTo;
                            break;
                        case 'mahasiswa':
                            $updateData['project_member_mahasiswa_id'] = $assignedTo;
                            break;
                    }
                }
                $updateData['assigned_to'] = $assignedTo;
                
            } else { 
                // MODIFIKASI: Hanya update nama jika dia yang buat progres
                if ($isCreatedByCurrentUser && $request->has('nama_progres')) {
                    $updateData['nama_progres'] = $request->nama_progres;
                }
                
                $updateData['status_progres'] = $request->status_progres;
                $updateData['persentase_progres'] = $request->persentase_progres;
                $updateData['deskripsi_progres'] = $request->deskripsi_progres;
                $updateData['tanggal_mulai_progres'] = $tanggalMulaiToSave;
                $updateData['tanggal_selesai_progres'] = $tanggalSelesaiToSave;
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

    public function storeProgresProyek(Request $request)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profesional tidak ditemukan'
                ], 401);
            }

            // TAMBAHAN: Check role profesional dalam proyek
            $roleCheck = $this->checkProfesionalRole($request->proyek_id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
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
                    'assigned_type' => 'nullable|in:leader,dosen,profesional,mahasiswa',
                    'assigned_to' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // TAMBAHAN: Validasi assignment berdasarkan role
                if (!$roleCheck['isLeader'] && $request->has('assigned_to')) {
                    // Jika bukan leader, hanya bisa assign ke diri sendiri
                    if ($request->assigned_type === 'profesional') {
                        $profesionalMemberId = DB::table('t_project_member_profesional')
                            ->where('proyek_id', $request->proyek_id)
                            ->where('profesional_id', $profesionalId)
                            ->whereNull('deleted_at')
                            ->value('project_member_profesional_id');
                        
                        if ($request->assigned_to !== $profesionalMemberId) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Anda hanya dapat menugaskan progres kepada diri sendiri'
                            ], 403);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda hanya dapat menugaskan progres kepada diri sendiri'
                        ], 403);
                    }
                }
                
                // Generate UUID for progres ID
                $progresId = (string) Str::uuid();
                
                // Prepare assignment data
                $projectLeaderId = null;
                $projectMemberProfesionalInd = null;
                $projectMemberProfesionalId = null;
                $projectMemberMahasiswaId = null;
                
                if ($request->has('assigned_type') && $request->has('assigned_to') && !empty($request->assigned_to)) {
                    switch ($request->assigned_type) {
                        case 'leader':
                            $projectLeaderId = $request->assigned_to;
                            break;
                        case 'dosen':
                            $projectMemberProfesionalInd = $request->assigned_to;
                            break;
                        case 'profesional':
                            $projectMemberProfesionalId = $request->assigned_to;
                            break;
                        case 'mahasiswa':
                            $projectMemberMahasiswaId = $request->assigned_to;
                            break;
                    }
                }
                
                // Insert data into t_progres_proyek table
                DB::table('t_progres_proyek')->insert([
                    'progres_proyek_id' => $progresId,
                    'proyek_id' => $request->proyek_id,
                    'project_leader_id' => $projectLeaderId,
                    'project_member_dosen_id' => $projectMemberProfesionalInd,
                    'project_member_profesional_id' => $projectMemberProfesionalId,
                    'project_member_mahasiswa_id' => $projectMemberMahasiswaId,
                    'assigned_to' => $request->assigned_to,
                    'nama_progres' => $request->nama_progres,
                    'deskripsi_progres' => $request->deskripsi_progres,
                    'status_progres' => $request->status_progres,
                    'persentase_progres' => $request->persentase_progres,
                    'created_at' => Carbon::now(),
                    'created_by' => $profesionalId,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data progres berhasil ditambahkan',
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
                
                foreach ($progresData as $index => $progres) {
                    // Generate UUID for each progres
                    $progresId = (string) Str::uuid();
                    
                    // Prepare assignment data
                    $projectLeaderId = null;
                    $projectMemberProfesionalInd = null;
                    $projectMemberProfesionalId = null;
                    $projectMemberMahasiswaId = null;
                    
                    if (isset($progres['assigned_type']) && isset($progres['assigned_to']) && !empty($progres['assigned_to'])) {
                        switch ($progres['assigned_type']) {
                            case 'leader':
                                $projectLeaderId = $progres['assigned_to'];
                                break;
                            case 'dosen':
                                $projectMemberProfesionalInd = $progres['assigned_to'];
                                break;
                            case 'profesional':
                                $projectMemberProfesionalId = $progres['assigned_to'];
                                break;
                            case 'mahasiswa':
                                $projectMemberMahasiswaId = $progres['assigned_to'];
                                break;
                        }
                    }
                    
                    try {
                        // Insert data into t_progres_proyek table
                        DB::table('t_progres_proyek')->insert([
                            'progres_proyek_id' => $progresId,
                            'proyek_id' => $request->proyek_id,
                            'project_leader_id' => $projectLeaderId,
                            'project_member_dosen_id' => $projectMemberProfesionalInd,
                            'project_member_profesional_id' => $projectMemberProfesionalId,
                            'project_member_mahasiswa_id' => $projectMemberMahasiswaId,
                            'assigned_to' => $progres['assigned_to'] ?? null,
                            'nama_progres' => $progres['nama_progres'],
                            'deskripsi_progres' => $progres['deskripsi_progres'] ?? null,
                            'status_progres' => $progres['status_progres'],
                            'persentase_progres' => $progres['persentase_progres'],
                            'created_at' => Carbon::now(),
                            'created_by' => session('user_id', auth()->id()),
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
                    'message' => 'Semua data progres berhasil ditambahkan',
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

    public function storeMyProgres(Request $request)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profesional tidak ditemukan'
                ], 401);
            }

            // Check role profesional dalam proyek
            $roleCheck = $this->checkProfesionalRole($request->proyek_id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
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
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Generate UUID for progres ID
                $progresId = (string) Str::uuid();
                
                // Auto-assign ke diri sendiri
                $projectLeaderId = null;
                $projectMemberProfesionalInd = null;
                $assignedTo = null;
                
                if ($roleCheck['isLeader']) {
                    // Jika profesional adalah leader, assign sebagai leader
                    $projectLeaderId = DB::table('t_project_leader')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('leader_type', 'Dosen')
                        ->where('leader_id', $profesionalId)
                        ->whereNull('deleted_at')
                        ->value('project_leader_id');
                    $assignedTo = $projectLeaderId;
                } else {
                    // Jika profesional adalah member, assign sebagai member
                    $projectMemberProfesionalInd = DB::table('t_project_member_profesional')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('profesional_id', $profesionalId)
                        ->whereNull('deleted_at')
                        ->value('project_member_profesional_id');
                    $assignedTo = $projectMemberProfesionalInd;
                }
                
                // Insert data into t_progres_proyek table
                DB::table('t_progres_proyek')->insert([
                    'progres_proyek_id' => $progresId,
                    'proyek_id' => $request->proyek_id,
                    'project_leader_id' => $projectLeaderId,
                    'project_member_dosen_id' => $projectMemberProfesionalInd,
                    'project_member_profesional_id' => null,
                    'project_member_mahasiswa_id' => null,
                    'assigned_to' => $assignedTo,
                    'nama_progres' => $request->nama_progres,
                    'deskripsi_progres' => $request->deskripsi_progres,
                    'status_progres' => $request->status_progres,
                    'persentase_progres' => $request->persentase_progres,
                    'created_at' => Carbon::now(),
                    'created_by' => $profesionalId,
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
                
                // Auto-assign setup
                $projectLeaderId = null;
                $projectMemberProfesionalInd = null;
                $assignedTo = null;
                
                if ($roleCheck['isLeader']) {
                    $projectLeaderId = DB::table('t_project_leader')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('leader_type', 'Profesional')
                        ->where('leader_id', $profesionalId)
                        ->whereNull('deleted_at')
                        ->value('project_leader_id');
                    $assignedTo = $projectLeaderId;
                } else {
                    $projectMemberProfesionalInd = DB::table('t_project_member_profesional')
                        ->where('proyek_id', $request->proyek_id)
                        ->where('profesional_id', $profesionalId)
                        ->whereNull('deleted_at')
                        ->value('project_member_profesional_id');
                    $assignedTo = $projectMemberProfesionalInd;
                }
                
                foreach ($progresData as $index => $progres) {
                    // Generate UUID for each progres
                    $progresId = (string) Str::uuid();
                    
                    try {
                        // Insert data into t_progres_proyek table (auto-assign ke diri sendiri)
                        DB::table('t_progres_proyek')->insert([
                            'progres_proyek_id' => $progresId,
                            'proyek_id' => $request->proyek_id,
                            'project_leader_id' => $projectLeaderId,
                            'project_member_profesional_id' => $projectMemberProfesionalInd,
                            'project_member_dosen_id' => null,
                            'project_member_mahasiswa_id' => null,
                            'assigned_to' => $assignedTo,
                            'nama_progres' => $progres['nama_progres'],
                            'deskripsi_progres' => $progres['deskripsi_progres'] ?? null,
                            'status_progres' => $progres['status_progres'],
                            'persentase_progres' => $progres['persentase_progres'],
                            'created_at' => Carbon::now(),
                            'created_by' => $profesionalId,
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

    public function deleteProgresProyek($id)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
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

            // Check role dosen dalam proyek
            $roleCheck = $this->checkProfesionalRole($progres->proyek_id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // PERBAIKAN: Validasi hak delete dengan logic baru
            if (!$roleCheck['isLeader']) {
                // Jika bukan leader, cek dua kondisi:
                // 1. Progres harus di-assign ke profesional ini
                // 2. Progres harus dibuat oleh profesional ini sendiri (bukan oleh leader/koordinator)
                
                $profesionalMemberId = DB::table('t_project_member_profesional')
                    ->where('proyek_id', $progres->proyek_id)
                    ->where('profesional_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_member_profesional_id');
                
                // Cek apakah progres di-assign ke profesional ini
                if ($progres->project_member_profesional_id !== $profesionalMemberId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda hanya dapat menghapus progres yang ditugaskan kepada Anda'
                    ], 403);
                }
                
                // TAMBAHAN: Cek apakah progres dibuat oleh profesional ini sendiri
                if ($progres->created_by != $profesionalId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak dapat menghapus progres yang dibuat oleh project leader atau koordinator'
                    ], 403);
                }
            }
            
            // Soft delete the progres
            DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => $profesionalId
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

    public function getProgresDetail($id, Request $request = null)
    {
        try {
            $profesionalId = session('profesional_id');
            
            if (!$profesionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data profesional tidak ditemukan'
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

            // Check role profesional dalam proyek
            $roleCheck = $this->checkProfesionalRole($progres->proyek_id, $profesionalId);
            
            if (!$roleCheck['isLeader'] && !$roleCheck['isMember']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke proyek ini'
                ], 403);
            }

            // Get current profesional member ID and leader ID for permission checking
            $currentProfesionalMemberId = null;
            $currentLeaderId = null;
            
            if (!$roleCheck['isLeader']) {
                $currentProfesionalMemberId = DB::table('t_project_member_profesional')
                    ->where('proyek_id', $progres->proyek_id)
                    ->where('profesional_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_member_profesional_id');
            }
            
            if ($roleCheck['isLeader']) {
                $currentLeaderId = DB::table('t_project_leader')
                    ->where('proyek_id', $progres->proyek_id)
                    ->where('leader_type', 'Profesional')
                    ->where('leader_id', $profesionalId)
                    ->whereNull('deleted_at')
                    ->value('project_leader_id');
            }

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
            
            // Determine progress creation status
            $isCreatedByCurrentUser = ($progres->created_by == $profesionalId);
            $isAssignedAsLeader = (!empty($progres->project_leader_id) && $progres->project_leader_id === $currentLeaderId);
            $isAssignedAsMember = (!empty($progres->project_member_profesional_id) && $progres->project_member_profesional_id === $currentProfesionalMemberId);
            $isAssigned = $isAssignedAsLeader || $isAssignedAsMember;
            
            // DETEKSI MY PROGRES - Multiple criteria untuk menentukan apakah ini My Progres
            $isMyProgres = false;
            
            // Cara 1: Deteksi berdasarkan parameter request (dari frontend)
            if ($request && $request->has('is_my_progres')) {
                $isMyProgres = (bool)$request->input('is_my_progres');
            }
            
            // Cara 2: Deteksi berdasarkan logika My Progres (created BY user OR assigned TO user)
            if (!$isMyProgres) {
                $isMyProgres = $isCreatedByCurrentUser || $isAssigned;
            }
            
            // Cara 3: Deteksi berdasarkan URL atau referer (backup detection)
            if (!$isMyProgres && $request) {
                $referer = $request->header('referer', '');
                $isMyProgres = (strpos($referer, 'my-progres') !== false) || 
                            (strpos($referer, '#my-progres-proyek-section') !== false);
            }
            
            // Cara 4: Deteksi berdasarkan context khusus My Progres
            if (!$isMyProgres) {
                // Jika progres dibuat oleh user DAN ditugaskan kepada user yang sama
                if ($isCreatedByCurrentUser && $isAssigned) {
                    $isMyProgres = true;
                }
                
                // Atau jika progres hanya ditugaskan kepada user (assigned progress)
                if (!$isCreatedByCurrentUser && $isAssigned) {
                    $isMyProgres = true;
                }
            }
            
            // Determine progress type (untuk logging dan debugging)
            $progressType = 'unknown';
            if ($isCreatedByCurrentUser && $isAssigned) {
                $progressType = 'created_and_assigned';
            } elseif ($isCreatedByCurrentUser && !$isAssigned) {
                $progressType = 'created';
            } elseif (!$isCreatedByCurrentUser && $isAssigned) {
                $progressType = 'assigned';
            }
            
            // 🔧 FIX: Tentukan permission dengan logic yang benar untuk PROFESIONAL
            $canEditThisProgress = false;
            
            if ($roleCheck['isLeader']) {
                // Leader bisa edit semua progres
                $canEditThisProgress = true;
            } else {
                // Member hanya bisa edit progres yang BENAR-BENAR di-assign ke dia
                if (!empty($progres->project_member_profesional_id) && $progres->project_member_profesional_id === $currentProfesionalMemberId) {
                    $canEditThisProgress = true;
                } elseif (!empty($progres->project_leader_id) && !empty($currentLeaderId) && $progres->project_leader_id === $currentLeaderId) {
                    $canEditThisProgress = true;
                }
                // Progres yang di-assign ke dosen/mahasiswa TIDAK bisa diedit oleh profesional member
            }
            
            // MODIFIKASI DELETE PERMISSION - Konsisten dengan My Progres logic
            $canDeleteThisProgress = false;
            $deleteReason = '';
            
            if ($roleCheck['isLeader']) {
                if ($isMyProgres) {
                    // SPECIAL CASE: My Progres untuk Leader
                    if ($isCreatedByCurrentUser) {
                        $canDeleteThisProgress = true;
                        $deleteReason = 'created_by_leader_in_my_progres';
                    } else {
                        $canDeleteThisProgress = false;
                        $deleteReason = 'assigned_from_coordinator_in_my_progres';
                    }
                } else {
                    // Regular Progres untuk Leader - bisa delete semua
                    $canDeleteThisProgress = true;
                    $deleteReason = 'leader_full_access';
                }
            } else {
                // Member logic - hanya bisa delete yang dia buat sendiri DAN di-assign ke dia
                if ($canEditThisProgress && ($progres->created_by == $profesionalId)) {
                    $canDeleteThisProgress = true;
                    $deleteReason = 'created_by_member';
                } else {
                    $canDeleteThisProgress = false;
                    $deleteReason = 'not_creator_member_or_not_assigned';
                }
            }
            
            $editableFields = [];
            if ($roleCheck['isLeader']) {
                // ✅ SPECIAL CASE: Jika ini My Progres dan user adalah Leader
                if ($isMyProgres) {
                    // Leader di My Progres TIDAK bisa edit assignment fields
                    $editableFields = ['nama_progres', 'status_progres', 'persentase_progres', 'deskripsi_progres', 'tanggal_mulai_progres', 'tanggal_selesai_progres'];
                    // TIDAK termasuk: 'assigned_type', 'assigned_to'
                } else {
                    // Leader di regular progres bisa edit semua field termasuk assignment
                    $editableFields = ['nama_progres', 'status_progres', 'persentase_progres', 'deskripsi_progres', 'tanggal_mulai_progres', 'tanggal_selesai_progres', 'assigned_type', 'assigned_to'];
                }
            } else if ($canEditThisProgress) {
                if ($isCreatedByCurrentUser) {
                    // Jika dia yang buat progres, bisa edit nama dan tanggal
                    $editableFields = ['nama_progres', 'status_progres', 'persentase_progres', 'deskripsi_progres', 'tanggal_mulai_progres', 'tanggal_selesai_progres'];
                } else {
                    // Jika hanya ditugaskan (bukan dia yang buat), tidak bisa edit nama
                    $editableFields = ['status_progres', 'persentase_progres', 'deskripsi_progres', 'tanggal_mulai_progres', 'tanggal_selesai_progres'];
                }
                // Member NEVER bisa edit assignment fields (baik regular maupun my progres)
            } else {
                $editableFields = [];
            }
            
            // ✅ TAMBAHKAN project data untuk validasi tanggal di frontend
            $proyekData = DB::table('m_proyek')
                ->where('proyek_id', $progres->proyek_id)
                ->select('proyek_id', 'nama_proyek', 'tanggal_mulai', 'tanggal_selesai')
                ->first();
            
            // ✅ PREPARE response data dengan explicit deskripsi handling
            $responseData = [
                'progres_proyek_id' => $progres->progres_proyek_id,
                'proyek_id' => $progres->proyek_id,
                'nama_progres' => $progres->nama_progres ?? '',
                'deskripsi_progres' => $progres->deskripsi_progres ?? '', // ✅ EXPLICIT null handling
                'status_progres' => $progres->status_progres ?? 'To Do',
                'persentase_progres' => $progres->persentase_progres ?? 0,
                'tanggal_mulai_progres' => $progres->tanggal_mulai_progres,
                'tanggal_selesai_progres' => $progres->tanggal_selesai_progres,
                'assigned_to' => $progres->assigned_to,
                'assigned_name' => $assignedName,
                'assigned_type' => $assignedType,
                'created_by' => $progres->created_by,
                'created_at' => $progres->created_at,
                'updated_at' => $progres->updated_at
            ];
            
            return response()->json([
                'success' => true,
                'isLeader' => $roleCheck['isLeader'],
                'isMember' => $roleCheck['isMember'],
                'canEdit' => $canEditThisProgress,
                'canDelete' => $canDeleteThisProgress,
                'deleteReason' => $deleteReason,
                'editableFields' => $editableFields,
                'isCreatedByCurrentUser' => $isCreatedByCurrentUser, 
                'isMyProgres' => $isMyProgres,
                'progressType' => $progressType,
                'isAssignedFromCoordinator' => !$isCreatedByCurrentUser && $isAssigned,
                'proyek' => $proyekData,
                'data' => $responseData // ✅ EXPLICIT response data object
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data progres: ' . $e->getMessage()
            ], 500);
        }
    }
}