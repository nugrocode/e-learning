<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';
    
    // Tetap false jika Abang mau handle input tanggal manual/otomatis dari DB
    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'material_id',
        'file_path',
        'nilai'
    ];

    // PENTING: Agar Laravel tahu kolom ini adalah Tanggal (Objek Carbon)
    // Jadi bisa pakai .format('d M Y') di View
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tanggal_kumpul' => 'datetime' // Jaga-jaga jika ada kolom ini
    ];

    // Relasi ke User (Fix Error RelationNotFound)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Materi
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}