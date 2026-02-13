<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LearningController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| FILE INI SUDAH DIPERBAIKI DAN DIRAPIKAN
|
*/

// ==========================================================
// 1. OTENTIKASI (PUBLIC)
// ==========================================================
Route::get('/', [AuthController::class, 'index']); // Halaman awal ke Login
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);


// ==========================================================
// 2. GROUP ADMIN (Akses: Role Admin)
// ==========================================================
Route::middleware(['cek_role:admin'])->group(function () {
    
    // Redirect /admin ke Dashboard (UX Friendly)
    Route::get('/admin', function() {
        return redirect('/admin/dashboard');
    });

    // Dashboard Admin
    Route::get('/admin/dashboard', [LearningController::class, 'adminDashboard']);

    // --- MANAJEMEN PENGGUNA (USERS) ---
    Route::get('/admin/users', [LearningController::class, 'adminUsersIndex']);
    Route::post('/admin/users', [LearningController::class, 'adminUserStore']);
    Route::put('/admin/users/{id}', [LearningController::class, 'adminUserUpdate']);
    Route::delete('/admin/users/{id}', [LearningController::class, 'adminUserDestroy']);

    // --- MANAJEMEN PENGUMUMAN ---
    Route::get('/admin/pengumuman', [LearningController::class, 'adminPengumumanIndex']);
    Route::post('/admin/pengumuman', [LearningController::class, 'adminPengumumanStore']);
    Route::put('/admin/pengumuman/{id}', [LearningController::class, 'adminPengumumanUpdate']);
    Route::delete('/admin/pengumuman/{id}', [LearningController::class, 'adminPengumumanDestroy']);

    // --- MANAJEMEN KONSENTRASI / PRODI ---
    Route::get('/admin/konsentrasi', [LearningController::class, 'adminKonsentrasiIndex']);
    Route::post('/admin/konsentrasi', [LearningController::class, 'adminKonsentrasiStore']);
    Route::put('/admin/konsentrasi/{id}', [LearningController::class, 'adminKonsentrasiUpdate']);
    Route::delete('/admin/konsentrasi/{id}', [LearningController::class, 'adminKonsentrasiDestroy']);

    // --- BANK MATA KULIAH (MASTER DATA) ---
    Route::get('/admin/bank-mk', [LearningController::class, 'adminBankIndex']);
    Route::post('/admin/bank-mk', [LearningController::class, 'adminBankStore']);
    Route::put('/admin/bank-mk/{id}', [LearningController::class, 'adminBankUpdate']);
    Route::delete('/admin/bank-mk/{id}', [LearningController::class, 'adminBankDestroy']);

    // --- MANAJEMEN KURIKULUM & DISTRIBUSI ---
    Route::get('/admin/kurikulum', [LearningController::class, 'adminKurikulumIndex']);
    
    // Halaman Detail Kurikulum per Prodi
    Route::get('/admin/mata-kuliah/{concentration_id}', [LearningController::class, 'adminKurikulumShow']);
    
    // CRUD Mata Kuliah di dalam Kurikulum
    Route::post('/admin/mata-kuliah', [LearningController::class, 'adminCourseStore']);
    Route::put('/admin/mata-kuliah/{id}', [LearningController::class, 'adminCourseUpdate']);
    Route::delete('/admin/mata-kuliah/{id}', [LearningController::class, 'adminCourseDestroy']);

    // FITUR AI ADMIN (Sorting & Distribute)
    Route::post('/admin/kurikulum/auto-distribute', [LearningController::class, 'adminAutoDistribute']); // Tombol Sakti
    Route::post('/admin/kurikulum/{id}/reset', [LearningController::class, 'adminResetKurikulum']); // Reset Urutan
    Route::post('/admin/kurikulum/{id}/smart-insert', [LearningController::class, 'adminUpdateKurikulum']); // Insert Cerdas
});


// ==========================================================
// 3. GROUP DOSEN (Akses: Role Dosen)
// ==========================================================
Route::middleware(['cek_role:dosen'])->group(function () {

    // Redirect /dosen ke Dashboard
    Route::get('/dosen', function() {
        return redirect('/dosen/dashboard');
    });

    // Dashboard & List Kelas
    Route::get('/dosen/dashboard', [LearningController::class, 'dosenDashboard']);
    Route::get('/dosen/kelas', [LearningController::class, 'dosenKelasIndex']);
    
    // Manajemen Detail Kelas (Materi, Kuis, Mahasiswa)
    Route::get('/dosen/kelas/{id}', [LearningController::class, 'dosenKelasDetail']);

    // CRUD Materi
    Route::post('/dosen/materi', [LearningController::class, 'dosenMateriStore']);
    Route::put('/dosen/materi/{id}', [LearningController::class, 'dosenMateriUpdate']);
    Route::delete('/dosen/materi/{id}', [LearningController::class, 'dosenMateriDestroy']);
    
    // Preview Materi (Agar Dosen bisa melihat tampilan user)
    Route::get('/dosen/preview/{course_id}/{urutan}', [LearningController::class, 'dosenPreviewMateri']);
    
    // CRUD Soal Kuis
    Route::post('/dosen/soal', [LearningController::class, 'dosenSoalStore']);
    Route::delete('/dosen/soal/{id}', [LearningController::class, 'dosenSoalDestroy']);

    // Manajemen Mahasiswa & Nilai
    Route::post('/dosen/kick-student', [LearningController::class, 'dosenKickStudent']);
    Route::put('/dosen/nilai/{id}', [LearningController::class, 'dosenUpdateNilai']);
    
    // Fitur AI Dosen (Reset & Sort Materi)
    Route::post('/dosen/materi/{course_id}/reset', [LearningController::class, 'dosenResetMateri']);
    Route::post('/dosen/materi/{course_id}/smart-insert', [LearningController::class, 'dosenUpdateMateri']);
});


// ==========================================================
// 4. GROUP MAHASISWA (Akses: Role Mahasiswa)
// ==========================================================
Route::middleware(['cek_role:mahasiswa'])->group(function () {
    
    // Dashboard & Akademik
    Route::get('/dashboard', [LearningController::class, 'dashboard']);
    Route::get('/jalur-belajar', [LearningController::class, 'index']);
    Route::get('/mata-kuliah/{id}', [LearningController::class, 'showCourses']);
    Route::get('/kelas-saya', [LearningController::class, 'myClasses']);
    
    // Proses Belajar (Materi, Kuis, Tugas)
    Route::get('/belajar/{course_id}/{urutan?}', [LearningController::class, 'belajar']);
    Route::post('/proses-progress', [LearningController::class, 'storeProgress']);
    Route::post('/proses-kuis', [LearningController::class, 'storeQuiz']);
    Route::post('/proses-tugas', [LearningController::class, 'storeAssignment']);
    
    // Fitur Tambahan (Diskusi AI, Notifikasi, Bantuan)
    Route::get('/bantuan', [LearningController::class, 'bantuan']);
    Route::get('/diskusi', [LearningController::class, 'diskusi']);
    Route::post('/ask-ai', [LearningController::class, 'askAi']); // Endpoint Chat AI
    
    // Diskusi / Komentar Materi
    Route::post('/proses-diskusi', [LearningController::class, 'storeDiscussion']);
    Route::delete('/diskusi/{id}', [LearningController::class, 'destroyDiscussion']);
    Route::get('/notifikasi/{id}', [LearningController::class, 'readNotification']);
    
    // Profil Pengguna (Yang sebelumnya terpotong)
    Route::get('/profil', [LearningController::class, 'editProfile']);
    Route::post('/profil/update', [LearningController::class, 'updateProfile']);
});