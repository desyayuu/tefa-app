<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class DataTimelineController extends Controller
{
    /**
     * Display timeline data for a specific project
     */
    public function index($id, Request $request)
    {
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
        
        // Get timeline data
        $query = DB::table('t_timeline_proyek')
            ->where('proyek_id', $id)
            ->whereNull('deleted_at');
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_timeline_proyek', 'like', "%{$search}%")
                  ->orWhere('deskripsi_timeline', 'like', "%{$search}%");
            });
        }
        
        $timelines = $query->orderBy('tanggal_mulai_timeline', 'desc')
            ->get();
        
        return view('pages.Koordinator.DataProyek.data_timeline_proyek', compact('proyek', 'timelines', 'search'));
    }
    
    /**
     * Store new timeline data
     */
    public function addDataTimeline(Request $request)
    {
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
        
        if ($isSingle) {
            // Validate single timeline
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
            
            // Insert single timeline
            $timelineId = DB::table('t_timeline_proyek')->insertGetId([
                'timeline_proyek_id' => Str::uuid(),
                'proyek_id' => $request->input('proyek_id'),
                'nama_timeline_proyek' => $request->input('nama_timeline'),
                'tanggal_mulai_timeline' => $request->input('tanggal_mulai_timeline'),
                'tanggal_selesai_timeline' => $request->input('tanggal_selesai_timeline'),
                'deskripsi_timeline' => $request->input('deskripsi_timeline'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
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
            
            // Insert multiple timeline
            $insertedIds = [];
            foreach ($timelineData as $timeline) {
                $timelineId = DB::table('t_timeline_proyek')->insertGetId([
                    'timeline_proyek_id' => Str::uuid(),
                    'proyek_id' => $request->input('proyek_id'),
                    'nama_timeline_proyek' => $timeline['nama_timeline'],
                    'tanggal_mulai_timeline' => $timeline['tanggal_mulai_timeline'],
                    'tanggal_selesai_timeline' => $timeline['tanggal_selesai_timeline'],
                    'deskripsi_timeline' => $timeline['deskripsi_timeline'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $insertedIds[] = $timelineId;
            }
            
            return response()->json([
                'success' => true,
                'message' => count($insertedIds) . ' data timeline berhasil ditambahkan',
                'data' => ['ids' => $insertedIds]
            ]);
        }
    }
    
    /**
     * Get timeline data for editing
     */
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
        
        return response()->json([
            'success' => true,
            'data' => $timeline
        ]);
    }
    
    /**
     * Update timeline data
     */
    public function update(Request $request, $id)
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
        
        // Update timeline
        DB::table('t_timeline_proyek')
            ->where('timeline_proyek_id', $id)
            ->update([
                'nama_timeline_proyek' => $request->input('nama_timeline'),
                'tanggal_mulai_timeline' => $request->input('tanggal_mulai_timeline'),
                'tanggal_selesai_timeline' => $request->input('tanggal_selesai_timeline'),
                'deskripsi_timeline' => $request->input('deskripsi_timeline'),
                'updated_at' => Carbon::now(),
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data timeline berhasil diperbarui'
        ]);
    }
    
    /**
     * Delete timeline data
     */
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
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data timeline berhasil dihapus'
        ]);
    }
}