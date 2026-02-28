<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    protected $table = 'upload_sessions';

    protected $fillable = [
        'upload_id',
        'file_name',
        'total_chunks',
        'uploaded_chunks',
        'status',
        's3_path'
    ];
}
