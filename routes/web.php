<?php

use App\Http\Controllers\PdfUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PdfUploadController::class, 'index'])->name('pdf-uploads.index');
Route::post('/pdf-uploads', [PdfUploadController::class, 'store'])->name('pdf-uploads.store');
