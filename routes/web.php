<?php

use App\Http\Controllers\Upload\UploadController;
use Illuminate\Support\Facades\Route;

// Upload page route
Route::get('/', [UploadController::class, 'index']);
