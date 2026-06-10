<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PdfUploadController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return auth()->check()
		? redirect()->route('dashboard')
		: redirect()->route('login');
});

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'create'])->name('login');
	Route::post('/login', [AuthController::class, 'store'])->name('login.store');

	Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
	Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

	Route::get('/dashboard', [QuizController::class, 'index'])->name('dashboard');
	Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
	Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

	Route::post('/pdf-uploads', [PdfUploadController::class, 'store'])->name('pdf-uploads.store');
});
