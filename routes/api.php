<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\NoticeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Documentos
Route::get('/documents', [DocumentController::class, 'index']);
Route::post('/documents/store', [DocumentController::class, 'store']);
Route::get('/documents/show/{document}', [DocumentController::class, 'show']);
Route::post('/documents/update/{document}', [DocumentController::class, 'update']);
Route::post('/documents/delete/{document}', [DocumentController::class, 'destroy']);


// Documentos
Route::get('/notices', [NoticeController::class, 'index']);
Route::post('/notices/store', [NoticeController::class, 'store']);
Route::get('/notices/show/{notice}', [NoticeController::class, 'show']);
Route::post('/notices/update/{notice}', [NoticeController::class, 'update']);
Route::post('/notices/delete/{notice}', [NoticeController::class, 'destroy']);
