<?php

namespace App\Jobs;

use App\Models\UploadSession;
use App\Mail\UploadCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 1200; // 20 minutes

    protected $uploadId;

    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function handle()
    {
        $session = UploadSession::where('upload_id', $this->uploadId)->firstOrFail();

        try {

            $session->update(['status' => 'processing']);

            $chunksPath = "chunks/{$this->uploadId}";
            $finalDirectory = storage_path('app/final');

            if (!file_exists($finalDirectory)) {
                mkdir($finalDirectory, 0777, true);
            }

            $uniqueFileName = Str::uuid() . '_' . $session->file_name;
            $finalPath = $finalDirectory . '/' . $uniqueFileName;

            $output = fopen($finalPath, 'w');

            // Merge chunks using Storage disk (IMPORTANT FIX)
            for ($i = 0; $i < $session->total_chunks; $i++) {

                $chunkFile = "{$chunksPath}/chunk_{$i}";

                if (!Storage::disk('local')->exists($chunkFile)) {
                    throw new \Exception("Missing chunk: {$i}");
                }

                $chunkStream = Storage::disk('local')->readStream($chunkFile);

                stream_copy_to_stream($chunkStream, $output);

                fclose($chunkStream);
            }

            fclose($output);

            // Upload to S3
            $s3Path = "videos/" . date('Y/m/') . $uniqueFileName;
            Log::info('Saving S3 Path: ' . $s3Path);

            Storage::disk('s3')->put(
                $s3Path,
                fopen($finalPath, 'r')
            );

            // Cleanup local files
            Storage::disk('local')->deleteDirectory($chunksPath);
            unlink($finalPath);

            $session->update([
                'status' => 'completed',
                's3_path' => $s3Path
            ]);

            // Send Email Notification
            Mail::to(config('mail.upload_notification_email'))
                ->send(new UploadCompleted($session, $s3Path));

        } catch (\Exception $e) {

            $session->update([
                'status' => 'failed'
            ]);

            throw $e;
        }
    }
}