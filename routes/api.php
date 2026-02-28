<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Upload\UploadController;


Route::prefix('uploads')->group(function () {
    Route::post('/start', [UploadController::class, 'start']);
    Route::post('/chunk', [UploadController::class, 'uploadChunk']);
    Route::post('/finish', [UploadController::class, 'finish']);
});
