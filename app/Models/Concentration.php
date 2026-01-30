<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concentration extends Model
{
    use HasFactory;

    protected $table = 'concentrations';

    protected $fillable = [
        'nama_konsentrasi',
        'deskripsi',
        'gambar'
    ];

    // Relasi Many-to-Many ke Course
    // Artinya: Satu Prodi punya banyak MK, lewat tabel perantara 'concentration_course'
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'concentration_course')
                    ->withPivot('urutan') // Penting! Agar kita bisa akses kolom 'urutan' di tabel pivot
                    ->withTimestamps();
    }
}