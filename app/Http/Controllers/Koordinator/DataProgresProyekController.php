<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DataProgresProyekController extends Controller
{
    public function getProgresByProyek($id, Request $request){
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
            return redirect()->route('koordinator.dataProyek')->with('error', 'Data proyek tidak ditemukan.');
        }
        
        // Get search parameter
        $search = $request->input('search');
        $perPageProgresProyek = $request->input('per_page_progres_proyek', 3);
        $page = $request->input('page', 1);
        
        // Get progres data
        $query = DB::table('t_progres_proyek')
            ->where('t_progres_proyek.proyek_id', $id)
            ->whereNull('t_progres_proyek.deleted_at')
            ->select('t_progres_proyek.*');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_progres', 'like', "%{$search}%")
                ->orWhere('deskripsi_progres', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $progres = $query->orderBy('created_at', 'asc')
            ->paginate($perPageProgresProyek, ['*'], 'page', $page);
        
        // Process each item to add assignee name
        $progres->getCollection()->transform(function ($item) {
            if (!empty($item->project_leader_id)) {
                $leader = DB::table('t_project_leader')
                    ->where('project_leader_id', $item->project_leader_id)
                    ->whereNull('deleted_at')
                    ->first();
                    
                if ($leader) {
                    if ($leader->leader_type === 'Dosen') {
                        $leaderDetail = DB::table('d_dosen')
                            ->where('dosen_id', $leader->leader_id)
                            ->first();
                        $item->assigned_name = $leaderDetail ? $leaderDetail->nama_dosen : 'Project Leader';
                    } else if ($leader->leader_type === 'Profesional') {
                        $leaderDetail = DB::table('d_profesional')
                            ->where('profesional_id', $leader->leader_id)
                            ->first();
                        $item->assigned_name = $leaderDetail ? $leaderDetail->nama_profesional : 'Project Leader';
                    } else {
                        $item->assigned_name = 'Project Leader';
                    }
                } else {
                    $item->assigned_name = 'Project Leader';
                }
            } 
            else if (!empty($item->project_member_dosen_id)) {
                // Fetch dosen name
                $dosen = DB::table('t_project_member_dosen')
                    ->join('d_dosen', 't_project_member_dosen.dosen_id', '=', 'd_dosen.dosen_id')
                    ->where('t_project_member_dosen.project_member_dosen_id', $item->project_member_dosen_id)
                    ->select('d_dosen.nama_dosen')
                    ->first();
                
                $item->assigned_name = $dosen ? $dosen->nama_dosen : 'Unknown Dosen';
            } 
            else if (!empty($item->project_member_profesional_id)) {
                // Fetch profesional name
                $profesional = DB::table('t_project_member_profesional')
                    ->join('d_profesional', 't_project_member_profesional.profesional_id', '=', 'd_profesional.profesional_id')
                    ->where('t_project_member_profesional.project_member_profesional_id', $item->project_member_profesional_id)
                    ->select('d_profesional.nama_profesional')
                    ->first();
                
                $item->assigned_name = $profesional ? $profesional->nama_profesional : 'Unknown Profesional';
            } 
            else if (!empty($item->project_member_mahasiswa_id)) {
                // Fetch mahasiswa name
                $mahasiswa = DB::table('t_project_member_mahasiswa')
                    ->join('d_mahasiswa', 't_project_member_mahasiswa.mahasiswa_id', '=', 'd_mahasiswa.mahasiswa_id')
                    ->where('t_project_member_mahasiswa.project_member_mahasiswa_id', $item->project_member_mahasiswa_id)
                    ->select('d_mahasiswa.nama_mahasiswa')
                    ->first();
                
                $item->assigned_name = $mahasiswa ? $mahasiswa->nama_mahasiswa : 'Unknown Mahasiswa';
            } 
            else {
                $item->assigned_name = 'Not Assigned';
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
        
        // For non-AJAX requests, return a view
        return view('koordinator.progres-proyek.index', [
            'proyek' => $proyek,
            'progres' => $progres,
            'search' => $search
        ]);
    }
    public function getTeamMembers($proyekId)
    {
        try {
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
                    // Get leader detail if leader is dosen
                    $leaderDetail = DB::table('d_dosen')
                        ->where('dosen_id', $leader->leader_id)
                        ->select(
                            'dosen_id',
                            DB::raw("'{$leader->project_leader_id}' as project_leader_id"),
                            'nama_dosen as nama'
                        )
                        ->first();
                } else if ($leader->leader_type === 'Profesional') {
                    // Get leader detail if leader is profesional
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
            
            // Get dosen members
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
                
            // Get profesional members
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
                
            // Get mahasiswa members
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
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Check if single or multiple progres entries
            $isSingle = $request->input('is_single', 1);
            
            if ($isSingle == 1) {
                // Validate single progres entry
                $validator = Validator::make($request->all(), [
                    'proyek_id' => 'required|string|exists:m_proyek,proyek_id',
                    'nama_progres' => 'required|string|max:255',
                    'deskripsi_progres' => 'nullable|string',
                    'status_progres' => 'required|in:Inisiasi,In Progress,Done',
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
                
                // Generate UUID for progres ID
                $progresId = (string) Str::uuid();
                
                // Prepare assignment data
                $projectLeaderId = null;
                $projectMemberDosenId = null;
                $projectMemberProfesionalId = null;
                $projectMemberMahasiswaId = null;
                
                if ($request->has('assigned_type') && $request->has('assigned_to') && !empty($request->assigned_to)) {
                    switch ($request->assigned_type) {
                        case 'leader':
                            $projectLeaderId = $request->assigned_to;
                            break;
                        case 'dosen':
                            $projectMemberDosenId = $request->assigned_to;
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
                    'project_member_dosen_id' => $projectMemberDosenId,
                    'project_member_profesional_id' => $projectMemberProfesionalId,
                    'project_member_mahasiswa_id' => $projectMemberMahasiswaId,
                    'assigned_to' => $request->assigned_to,
                    'nama_progres' => $request->nama_progres,
                    'deskripsi_progres' => $request->deskripsi_progres,
                    'status_progres' => $request->status_progres,
                    'persentase_progres' => $request->persentase_progres,
                    'created_at' => Carbon::now(),
                    'created_by' => session('user_id', auth()->id()),
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
                    $projectMemberDosenId = null;
                    $projectMemberProfesionalId = null;
                    $projectMemberMahasiswaId = null;
                    
                    if (isset($progres['assigned_type']) && isset($progres['assigned_to']) && !empty($progres['assigned_to'])) {
                        switch ($progres['assigned_type']) {
                            case 'leader':
                                $projectLeaderId = $progres['assigned_to'];
                                break;
                            case 'dosen':
                                $projectMemberDosenId = $progres['assigned_to'];
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
                            'project_member_dosen_id' => $projectMemberDosenId,
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
            // If transaction was started, roll it back
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function deleteDataProgresProyek($id)
    {
        try {
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
            
            // Soft delete the progres
            DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->update([
                    'deleted_at' => Carbon::now(),
                    'deleted_by' => auth()->id()
                ]);
                
            return response()->json([
                'success' => true,
                'message' => 'Data progres berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function getProgresDetail($id)
    {
        try {
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
            
            // Get assigned name based on the assignment
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
            
            return response()->json([
                'success' => true,
                'data' => $progres
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data progres: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'nama_progres' => 'required|string|max:255',
                'status_progres' => 'required|in:Inisiasi,In Progress,Done',
                'persentase_progres' => 'required|integer|min:0|max:100',
                'deskripsi_progres' => 'nullable|string',
                'assigned_to' => 'nullable|string',
                'assigned_type' => 'nullable|in:leader,dosen,profesional,mahasiswa',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
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
            
            // Prepare assignment data
            $projectLeaderId = null;
            $projectMemberDosenId = null;
            $projectMemberProfesionalId = null;
            $projectMemberMahasiswaId = null;
            $assignedTo = $request->input('assigned_to');
            
            if ($request->has('assigned_type') && !empty($assignedTo)) {
                switch ($request->assigned_type) {
                    case 'leader':
                        $projectLeaderId = $assignedTo;
                        break;
                    case 'dosen':
                        $projectMemberDosenId = $assignedTo;
                        break;
                    case 'profesional':
                        $projectMemberProfesionalId = $assignedTo;
                        break;
                    case 'mahasiswa':
                        $projectMemberMahasiswaId = $assignedTo;
                        break;
                }
            }
            
            // Update the record
            DB::table('t_progres_proyek')
                ->where('progres_proyek_id', $id)
                ->update([
                    'nama_progres' => $request->nama_progres,
                    'status_progres' => $request->status_progres,
                    'persentase_progres' => $request->persentase_progres,
                    'deskripsi_progres' => $request->deskripsi_progres,
                    'project_leader_id' => $projectLeaderId,
                    'project_member_dosen_id' => $projectMemberDosenId,
                    'project_member_profesional_id' => $projectMemberProfesionalId,
                    'project_member_mahasiswa_id' => $projectMemberMahasiswaId,
                    'assigned_to' => $assignedTo,
                    'updated_at' => Carbon::now(),
                    'updated_by' => auth()->id()
                ]);
                
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
}