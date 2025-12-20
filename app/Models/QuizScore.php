<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizScore extends Model
{
    protected $table = 'quiz_scores';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'material_id',
        'skor',
        'tanggal_kerja'
    ];
}
