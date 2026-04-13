<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Import Controller
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\GoogleController; 

/*
|--------------------------------------------------------------------------
| Web Routes (UPDATED)
|--------------------------------------------------------------------------
|
| 1. AdminController     -> Mengurus Kurikulum, User, & Master Data
| 2. DosenController     -> Mengurus Materi (AI), Kuis, Nilai, & Profil
| 3. MahasiswaController -> Mengurus LMS, Belajar, & Progress
|
*/

// ==========================================================
// 1. OTENTIKASI (PUBLIC)
// ==========================================================
Route::get('/', [AuthController::class, 'index']); // Landing ke Login
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

// ==========================================================
// 1.5. GOOGLE DRIVE INTEGRATION (KHUSUS DOSEN)
// ==========================================================
Route::get('/google/connect', [GoogleController::class, 'redirectToGoogle']);
Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/google/disconnect', [GoogleController::class, 'disconnectGoogle']); // <-- ROUTE BARU DITAMBAHKAN


// ==========================================================
// 2. GROUP ADMIN (Akses: Role Admin)
// ==========================================================
Route::middleware(['cek_role:admin'])->group(function () {
    
    Route::get('/admin', function() { return redirect('/admin/dashboard'); });
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    // MANAJEMEN PENGGUNA (USERS)
    Route::get('/admin/users', [AdminController::class, 'usersIndex']);
    Route::post('/admin/users', [AdminController::class, 'userStore']);
    Route::put('/admin/users/{id}', [AdminController::class, 'userUpdate']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'userDestroy']);

    // MANAJEMEN PENGUMUMAN
    Route::get('/admin/pengumuman', [AdminController::class, 'pengumumanIndex']);
    Route::post('/admin/pengumuman', [AdminController::class, 'pengumumanStore']);
    Route::put('/admin/pengumuman/{id}', [AdminController::class, 'pengumumanUpdate']);
    Route::delete('/admin/pengumuman/{id}', [AdminController::class, 'pengumumanDestroy']);

    // MASTER DATA (KONSENTRASI & BANK MK)
    Route::get('/admin/konsentrasi', [AdminController::class, 'konsentrasiIndex']);
    Route::post('/admin/konsentrasi', [AdminController::class, 'konsentrasiStore']);
    Route::put('/admin/konsentrasi/{id}', [AdminController::class, 'konsentrasiUpdate']);
    Route::delete('/admin/konsentrasi/{id}', [AdminController::class, 'konsentrasiDestroy']);

    Route::get('/admin/bank-mk', [AdminController::class, 'bankIndex']);
    Route::post('/admin/bank-mk', [AdminController::class, 'bankStore']);
    Route::put('/admin/bank-mk/{id}', [AdminController::class, 'bankUpdate']);
    Route::delete('/admin/bank-mk/{id}', [AdminController::class, 'bankDestroy']);

    // KURIKULUM & AI DISTRIBUTION
    Route::get('/admin/kurikulum', [AdminController::class, 'kurikulumIndex']);
    Route::get('/admin/mata-kuliah/{concentration_id}', [AdminController::class, 'kurikulumShow']);
    
    Route::post('/admin/mata-kuliah', [AdminController::class, 'courseStore']);
    Route::put('/admin/mata-kuliah/{id}', [AdminController::class, 'courseUpdate']);
    Route::delete('/admin/mata-kuliah/{id}', [AdminController::class, 'courseDestroy']);

    // FITUR AI ADMIN (FIXED ROUTE PATHS)
    Route::post('/admin/kurikulum/auto-distribute', [AdminController::class, 'autoDistribute']);
    Route::post('/admin/kurikulum/reset/{id}', [AdminController::class, 'resetKurikulum']);     // Diperbaiki
    Route::post('/admin/kurikulum/update/{id}', [AdminController::class, 'updateKurikulum']);   // Diperbaiki
});


// ==========================================================
// 3. GROUP DOSEN (Akses: Role Dosen)
// ==========================================================
Route::middleware(['cek_role:dosen'])->group(function () {

    Route::get('/dosen', function() { return redirect('/dosen/dashboard'); });

    // 1. DASHBOARD
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard']);

    // 2. DATA MAHASISWA
    Route::get('/dosen/mahasiswa', [DosenController::class, 'mahasiswaIndex']);
    Route::post('/dosen/kick-student', [DosenController::class, 'mahasiswaKick']);

    // 3. MANAJEMEN MATERI & KURIKULUM 
    Route::get('/dosen/materi', [DosenController::class, 'materiIndex']);       
    Route::get('/dosen/materi/{id}', [DosenController::class, 'materiShow']);    
    
    // CRUD Materi
    Route::post('/dosen/materi', [DosenController::class, 'materiStore']);
    Route::put('/dosen/materi/{id}', [DosenController::class, 'materiUpdate']);
    Route::delete('/dosen/materi/{id}', [DosenController::class, 'materiDestroy']);
    
    // Fitur AI Materi
    Route::post('/dosen/materi/ai-insert/{id}', [DosenController::class, 'aiSmartInsert']); 
    Route::post('/dosen/materi/ai-sort/{id}', [DosenController::class, 'aiAutoSort']);      

    // 4. KUIS & BANK SOAL
    Route::get('/dosen/kuis', [DosenController::class, 'kuisIndex']);
    Route::post('/dosen/kuis', [DosenController::class, 'kuisStore']);
    Route::post('/dosen/soal', [DosenController::class, 'soalStore']);
    Route::delete('/dosen/soal/{id}', [DosenController::class, 'soalDestroy']);

    // 5. PENUGASAN & NILAI
    Route::get('/dosen/tugas', [DosenController::class, 'tugasIndex']);
    Route::post('/dosen/tugas/nilai/{id}', [DosenController::class, 'nilaiUpdate']);
    Route::get('/dosen/tugas/download/{id}', [DosenController::class, 'downloadAssignment']); 

    // 6. DISKUSI
    Route::get('/dosen/diskusi', [DosenController::class, 'diskusiIndex']);
    Route::post('/dosen/proses-diskusi', [DosenController::class, 'diskusiStore']);
    Route::delete('/dosen/diskusi/{id}', [DosenController::class, 'diskusiDestroy']);

    // 7. PROFIL DOSEN 
    Route::get('/dosen/profil', [DosenController::class, 'profilIndex']);
    Route::post('/dosen/profil', [DosenController::class, 'profilUpdate']);

    // 8. PREVIEW MATERI 
    Route::get('/dosen/preview/{course_id}/{urutan}', [DosenController::class, 'preview']);
});


// ==========================================================
// 4. GROUP MAHASISWA (Akses: Role Mahasiswa)
// ==========================================================
Route::middleware(['cek_role:mahasiswa'])->group(function () {
    
    // Dashboard & Akademik
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard']);
    Route::get('/jalur-belajar', [MahasiswaController::class, 'index']);
    Route::get('/mata-kuliah/{id}', [MahasiswaController::class, 'showCourses']);
    Route::get('/kelas-saya', [MahasiswaController::class, 'myClasses']);
    
    // Proses Belajar (LMS Core)
    Route::get('/belajar/{course_id}/{urutan?}', [MahasiswaController::class, 'belajar']);
    Route::post('/proses-progress', [MahasiswaController::class, 'storeProgress']);
    Route::post('/proses-kuis', [MahasiswaController::class, 'storeQuiz']);
    Route::post('/proses-tugas', [MahasiswaController::class, 'storeAssignment']);
    
    // Fitur Tambahan
    Route::get('/bantuan', [MahasiswaController::class, 'bantuan']);
    Route::get('/diskusi', [MahasiswaController::class, 'diskusi']); 
    
    // Chatbot AI
    Route::post('/ask-ai', [MahasiswaController::class, 'askAi']); 
    
    // Interaksi & Profil
    Route::post('/proses-diskusi', [MahasiswaController::class, 'storeDiscussion']);
    Route::delete('/diskusi/{id}', [MahasiswaController::class, 'destroyDiscussion']);
    Route::get('/notifikasi/{id}', [MahasiswaController::class, 'readNotification']);
    
    Route::get('/profil', [MahasiswaController::class, 'editProfile']);
    Route::post('/profil/update', [MahasiswaController::class, 'updateProfile']);
});