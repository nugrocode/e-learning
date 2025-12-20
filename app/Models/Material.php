<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    // Nama tabel di database
    protected $table = 'materials';

    // Matikan timestamps (karena tabelmu tidak punya kolom created_at/updated_at default)
    public $timestamps = false;

    // Relasi: Materi ini milik satu Course (Mata Kuliah)
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relasi: Materi ini memiliki banyak data Progress (dari berbagai user)
    // Relasi ini WAJIB ADA agar fungsi "Kelas Saya" di Controller bisa berjalan
    public function progress()
    {
        return $this->hasMany(Progress::class, 'material_id');
    }
}
