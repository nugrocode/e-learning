<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'judul',
        'isi',
        'tipe',      // info, penting, libur
        'is_active', // 1 atau 0
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
}
