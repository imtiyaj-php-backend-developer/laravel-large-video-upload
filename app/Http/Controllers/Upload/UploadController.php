<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoUpload;
use App\Models\UploadSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    // Show Upload Video Design
    public function index()
    {
        return view('uploads.upload');
    }

    
    // Start Upload Session
    public function start(Request $request)
    {
        $request->validate([
            'upload_id'    => 'required|string',
            'file_name'    => 'required|string',
            'total_chunks' => 'required|integer|min:1',
        ]);

        // Prevent duplicate sessions
        $session = UploadSession::firstOrCreate(
            ['upload_id' => $request->upload_id],
            [
                'file_name'       => $request->file_name,
                'total_chunks'    => $request->total_chunks,
                'uploaded_chunks' => 0,
                'status'          => 'pending'
            ]
        );

        return response()->json([
            'status' => 'started',
            'data'   => $session
        ]);
    }

    // Upload Chunk
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'upload_id'   => 'required|string|exists:upload_sessions,upload_id',
            'chunk_index' => 'required|integer|min:0',
            'chunk'       => 'required|file|max:5120' // 5MB
        ]);

        $uploadId   = $request->upload_id;
        $chunkIndex = $request->chunk_index;
        $chunk      = $request->file('chunk');

        $path = "chunks/{$uploadId}/chunk_{$chunkIndex}";

        // Prevent duplicate chunk uploads (resumable safe)
        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->put($path, fopen($chunk->getRealPath(), 'r'));

            UploadSession::where('upload_id', $uploadId)
                ->increment('uploaded_chunks');
        }

        return response()->json([
            'status' => 'chunk_uploaded',
            'chunk_index' => $chunkIndex
        ]);
    }

    
    // Finish Upload
    public function finish(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string|exists:upload_sessions,upload_id'
        ]);

        $session = UploadSession::where('upload_id', $request->upload_id)->firstOrFail();

        if ($session->uploaded_chunks != $session->total_chunks) {
            return response()->json([
                'error' => 'Upload incomplete'
            ], 400);
        }

        ProcessVideoUpload::dispatch($session->upload_id);

        return response()->json([
            'status' => 'processing_started'
        ]);
    }
}