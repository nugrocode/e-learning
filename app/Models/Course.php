<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    public $timestamps = false;

    protected $fillable = [
        'concentration_id',
        'nama_mk',
        'deskripsi',
        'icon',
        'urutan' 
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'course_id');
    }
}
