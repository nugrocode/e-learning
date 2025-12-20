<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LearningController;



// --- 1. OTENTIKASI (Login/Logout) ---
Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);


// --- 2. DASHBOARD ---
Route::get('/dashboard', function () {
    // Cek Session Manual
    if (!session()->has('status') || session('status') != 'login') {
        return redirect('/login')->with('error', 'Silakan login dulu!');
    }
    return view('user.dashboard');
});


// --- 3. FITUR PEMBELAJARAN ---

// Halaman Daftar Jalur Belajar (Pilih Konsentrasi)
Route::get('/jalur-belajar', [LearningController::class, 'index']);

// Halaman Daftar Mata Kuliah (berdasarkan ID Konsentrasi)
Route::get('/mata-kuliah/{id}', [LearningController::class, 'showCourses']);

// Halaman Materi Belajar (Video/Kuis)
// Parameter {urutan?} bersifat opsional (default urutan 1)
Route::get('/belajar/{course_id}/{urutan?}', [LearningController::class, 'belajar']);


// --- 4. PROSES LOGIKA (POST) ---

// Simpan Progress (Saat tombol "Lanjut" diklik)
Route::post('/proses-progress', [LearningController::class, 'storeProgress']);

// Simpan Nilai Kuis
Route::post('/proses-kuis', [LearningController::class, 'storeQuiz']);

// Simpan Tugas GitHub
Route::post('/proses-tugas', [LearningController::class, 'storeAssignment']);

// Route Kelas Saya
Route::get('/kelas-saya', [LearningController::class, 'myClasses']);

// Route Bantuan
Route::get('/bantuan', [LearningController::class, 'bantuan']);

// --- FITUR AI ASSISTANT (CHATBOT) ---

// 1. Route untuk membuka halaman chatting
Route::get('/diskusi', [LearningController::class, 'diskusi']);

// 2. Route API untuk memproses pertanyaan ke Gemini (AJAX)
Route::post('/ask-ai', [LearningController::class, 'askAi']);


// Route Auto-Sort Kurikulum by AI
Route::post('/auto-sort-kurikulum/{id}', [LearningController::class, 'autoSortKurikulum']);
