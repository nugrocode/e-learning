<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'dosen_id',
        'nama_mk',
        'deskripsi',
        'gambar'
    ];

    /**
     * Relasi: Satu MK dimiliki banyak Konsentrasi/Prodi
     */
    public function concentrations()
    {
        return $this->belongsToMany(Concentration::class, 'concentration_course')
                    ->withPivot('urutan')
                    ->withTimestamps();
    }
    
    /**
     * Relasi: Satu MK diajar oleh satu Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /**
     * Relasi: Satu MK memiliki banyak Materi
     */
    public function materials()
    {
        return $this->hasMany(Material::class, 'course_id');
    }

    /**
     * Relasi untuk menghitung seluruh progres mahasiswa di MK ini.
     * Digunakan untuk fitur withCount('all_progress') di DosenController.
     * Alur: Kursus -> Materi -> Progres
     */
    public function all_progress()
    {
        return $this->hasManyThrough(
            Progress::class, 
            Material::class, 
            'course_id',   // Foreign key di tabel materials
            'material_id', // Foreign key di tabel progress
            'id',          // Local key di tabel courses
            'id'           // Local key di tabel materials
        );
    }
}