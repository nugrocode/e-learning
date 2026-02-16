<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'judul_materi',
        'deskripsi_materi',
        'video_url',
        'file_lampiran',
        'kategori',
        'tipe_submission',
        'link_drive',
        'urutan'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'material_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'material_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'material_id');
    }
}