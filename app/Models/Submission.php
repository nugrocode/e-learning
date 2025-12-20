<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions'; // Nama tabel di database
    public $timestamps = false; // Matikan timestamps jika tidak ada created_at/updated_at default

    // Kolom yang boleh diisi
    protected $fillable = [
        'user_id',
        'material_id',
        'file_path',
        'nilai'
    ];
}
