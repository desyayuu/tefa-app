<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class DataTimelineProfesionalController extends Controller
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

    public function getDataTimeline($id, Request $request){
        $profesionalId = session('profesional_id');
        
        if (!$profesionalId) {
            return redirect()->route('profesional.dashboard')->with('error', 'Data profesional tidak ditemukan');
        }

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
        $perPageTimeline = $request->input('per_page_timeline', 3);
        $page = $request->input('page', 1);
        
        // Get timeline data
        $query = DB::table('t_timeline_proyek')
            ->where('proyek_id', $id)
            ->whereNull('deleted_at');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_timeline_proyek', 'like', "%{$search}%")
                ->orWhere('deskripsi_timeline', 'like', "%{$search}%");
            });
        }

        //Pagination
        $timelines = $query->orderBy('tanggal_mulai_timeline', 'asc')
            ->paginate($perPageTimeline, ['*'], 'page', $page);
        $paginationHtml = ''; 
        if($timelines->hasPages()) {
            $paginationHtml = view('vendor.pagination.custom', [
                'paginator' => $timelines,
                'elements' => $timelines->links()->elements,
            ])->render();
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'isLeader' => $roleCheck['isLeader'],
                'isMember' => $roleCheck['isMember'],
                'data' => $timelines->items(),
                'proyek_dates' => [
                    'tanggal_mulai' => $proyek->tanggal_mulai,
                    'tanggal_selesai' => $proyek->tanggal_selesai
                ],
                'pagination' => [
                    'current_page' => $timelines->currentPage(),
                    'per_page_timeline' => $timelines->perPage(),
                    'total' => $timelines->total(),
                    'last_page' => $timelines->lastPage(),
                    'html' => $paginationHtml
                ]
            ]);
        }
    }
    
    private function validateTimelineDate($tanggalMulai, $tanggalSelesai, $proyekId)
    {
        // Get project dates
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $proyekId)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->first();
            
        if (!$proyek) {
            return ['valid' => false, 'message' => 'Data proyek tidak ditemukan'];
        }
        
        $proyekMulai = Carbon::parse($proyek->tanggal_mulai);
        $proyekSelesai = Carbon::parse($proyek->tanggal_selesai);
        $timelineMulai = Carbon::parse($tanggalMulai);
        $timelineSelesai = Carbon::parse($tanggalSelesai);
        
        // Check if timeline dates are within project date range
        if ($timelineMulai->lt($proyekMulai)) {
            return [
                'valid' => false, 
                'message' => 'Tanggal mulai timeline tidak boleh sebelum tanggal mulai proyek (' . $proyekMulai->format('d/m/Y') . ')'
            ];
        }
        
        if ($timelineSelesai->gt($proyekSelesai)) {
            return [
                'valid' => false, 
                'message' => 'Tanggal selesai timeline tidak boleh setelah tanggal selesai proyek (' . $proyekSelesai->format('d/m/Y') . ')'
            ];
        }
        
        if ($timelineMulai->gt($proyekSelesai)) {
            return [
                'valid' => false, 
                'message' => 'Tanggal mulai timeline tidak boleh setelah tanggal selesai proyek (' . $proyekSelesai->format('d/m/Y') . ')'
            ];
        }
        
        if ($timelineSelesai->lt($proyekMulai)) {
            return [
                'valid' => false, 
                'message' => 'Tanggal selesai timeline tidak boleh sebelum tanggal mulai proyek (' . $proyekMulai->format('d/m/Y') . ')'
            ];
        }
        
        return ['valid' => true];
    }
    
    public function addDataTimeline(Request $request){
        // Validate the request
        $validator = Validator::make($request->all(), [
            'proyek_id' => 'required|exists:m_proyek,proyek_id',
            'is_single' => 'required|in:0,1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if it's a single or multiple timeline
        $isSingle = $request->input('is_single') == "1";
        try {
            DB::beginTransaction();
            
            if ($isSingle) {
                $validator = Validator::make($request->all(), [
                    'nama_timeline' => 'required|string|max:255',
                    'tanggal_mulai_timeline' => 'required|date',
                    'tanggal_selesai_timeline' => 'required|date|after_or_equal:tanggal_mulai_timeline',
                    'deskripsi_timeline' => 'nullable|string',
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                // Validate against project date range
                $dateValidation = $this->validateTimelineDate(
                    $request->input('tanggal_mulai_timeline'),
                    $request->input('tanggal_selesai_timeline'),
                    $request->input('proyek_id')
                );
                
                if (!$dateValidation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $dateValidation['message']
                    ], 422);
                }

                $timelineId = Str::uuid();
                // Insert single timeline
                DB::table('t_timeline_proyek')->insert([
                    'timeline_proyek_id' => $timelineId,
                    'proyek_id' => $request->input('proyek_id'),
                    'nama_timeline_proyek' => $request->input('nama_timeline'),
                    'tanggal_mulai_timeline' => $request->input('tanggal_mulai_timeline'),
                    'tanggal_selesai_timeline' => $request->input('tanggal_selesai_timeline'),
                    'deskripsi_timeline' => $request->input('deskripsi_timeline'),
                    'created_at' => Carbon::now(),
                    'created_by' => auth()->user()->id ?? session('user_id'),
                    'updated_at' => Carbon::now(),
                    'updated_by' => auth()->user()->id ?? session('user_id'),
                ]);
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data timeline berhasil ditambahkan',
                    'data' => ['id' => $timelineId]
                ]);
            } else {
                // For multiple timeline
                $validator = Validator::make($request->all(), [
                    'timeline_data' => 'required|json',
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                // Parse timeline data
                $timelineData = json_decode($request->input('timeline_data'), true);
                if (empty($timelineData)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data timeline yang ditambahkan'
                    ], 422);
                }
                
                // Validate each timeline against project date range
                foreach ($timelineData as $index => $timeline) {
                    $dateValidation = $this->validateTimelineDate(
                        $timeline['tanggal_mulai_timeline'],
                        $timeline['tanggal_selesai_timeline'],
                        $request->input('proyek_id')
                    );
                    
                    if (!$dateValidation['valid']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Timeline ke-" . ($index + 1) . ": " . $dateValidation['message']
                        ], 422);
                    }
                }
                
                // Insert multiple timeline
                $insertedIds = [];
                foreach ($timelineData as $timeline) {
                    $timelineId = Str::uuid();
                    
                    DB::table('t_timeline_proyek')->insert([
                        'timeline_proyek_id' => $timelineId,
                        'proyek_id' => $request->input('proyek_id'),
                        'nama_timeline_proyek' => $timeline['nama_timeline'],
                        'tanggal_mulai_timeline' => $timeline['tanggal_mulai_timeline'],
                        'tanggal_selesai_timeline' => $timeline['tanggal_selesai_timeline'],
                        'deskripsi_timeline' => $timeline['deskripsi_timeline'],
                        'created_at' => Carbon::now(),
                        'created_by' => auth()->user()->id ?? session('user_id'),
                        'updated_at' => Carbon::now(),
                        'updated_by' => auth()->user()->id ?? session('user_id'),
                    ]);
                    
                    $insertedIds[] = $timelineId;
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => count($insertedIds) . ' data timeline berhasil ditambahkan',
                    'data' => ['ids' => $insertedIds]
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function detailDataTimeline($id)
    {
        $timeline = DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$timeline) {
            return response()->json([
                'success' => false,
                'message' => 'Data timeline tidak ditemukan'
            ], 404);
        }
        
        // Get project dates for validation
        $proyek = DB::table('m_proyek')
            ->where('proyek_id', $timeline->proyek_id)
            ->select('tanggal_mulai', 'tanggal_selesai')
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => $timeline,
            'proyek_dates' => [
                'tanggal_mulai' => $proyek->tanggal_mulai,
                'tanggal_selesai' => $proyek->tanggal_selesai
            ]
        ]);
    }
    
    public function updateDataTimeline(Request $request, $id){
        // Check if timeline exists
        $timeline = DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$timeline) {
            return response()->json([
                'success' => false,
                'message' => 'Data timeline tidak ditemukan'
            ], 404);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'nama_timeline' => 'required|string|max:255',
            'tanggal_mulai_timeline' => 'required|date',
            'tanggal_selesai_timeline' => 'required|date|after_or_equal:tanggal_mulai_timeline',
            'deskripsi_timeline' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Validate against project date range
        $dateValidation = $this->validateTimelineDate(
            $request->input('tanggal_mulai_timeline'),
            $request->input('tanggal_selesai_timeline'),
            $timeline->proyek_id
        );
        
        if (!$dateValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $dateValidation['message']
            ], 422);
        }
        
        // Update timeline
        DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->update([
                'nama_timeline_proyek' => $request->input('nama_timeline'),
                'tanggal_mulai_timeline' => $request->input('tanggal_mulai_timeline'),
                'tanggal_selesai_timeline' => $request->input('tanggal_selesai_timeline'),
                'deskripsi_timeline' => $request->input('deskripsi_timeline'),
                'updated_at' => Carbon::now(),
                'updated_by' => auth()->user()->id ?? session('user_id'),
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data timeline berhasil diperbarui'
        ]);
    }
    
    public function deleteDataTimeline($id)
    {
        // Check if timeline exists
        $timeline = DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->whereNull('deleted_at')
            ->first();
        
        if (!$timeline) {
            return response()->json([
                'success' => false,
                'message' => 'Data timeline tidak ditemukan'
            ], 404);
        }
        
        // Soft delete the timeline
        DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => auth()->user()->id ?? session('user_id'),
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data timeline berhasil dihapus'
        ]);
    }
}