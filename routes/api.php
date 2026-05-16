<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/pdf/upload', [PdfController::class, 'upload']);

Route::get('/pdf/status/{task}', [PdfController::class, 'status']);

Route::get('/pdf/download/{task}', [PdfController::class, 'download']);
