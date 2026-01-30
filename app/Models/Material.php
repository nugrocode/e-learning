<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    // Nama tabel di database
    protected $table = 'materials';

    // Matikan timestamps (karena tabelmu tidak punya kolom created_at/updated_at default)
    public $timestamps = false;

    // --- BAGIAN PENTING (UPDATE) ---
    // Daftar kolom yang diizinkan untuk diisi/diupdate secara massal
    protected $fillable = [
        'course_id',
        'judul_materi',
        'deskripsi_materi',
        'video_url',
        'file_lampiran',
        'kategori',        // quiz atau video
        'tipe_submission', // github atau file
        'urutan'           // <--- WAJIB ADA: Agar Controller bisa mengubah urutan
    ];

    // Relasi: Materi ini milik satu Course (Mata Kuliah)
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relasi: Materi ini memiliki banyak data Progress (dari berbagai user)
    public function progress()
    {
        return $this->hasMany(Progress::class, 'material_id');
    }
}