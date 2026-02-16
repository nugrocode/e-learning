<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';
    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}