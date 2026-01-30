<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'dosen_id', // <--- KEMBALI KE SINI
        'nama_mk',
        'deskripsi',
        'gambar'
    ];

    // Relasi: Satu MK dimiliki banyak Konsentrasi
    public function concentrations()
    {
        return $this->belongsToMany(Concentration::class, 'concentration_course')
                    ->withPivot('urutan')
                    ->withTimestamps();
    }
    
    // Relasi: Satu MK diajar Satu Dosen
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'course_id');
    }
}