<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    // Izinkan semua kolom diisi (mass assignment)
    protected $guarded = ['id'];

    // 1. Relasi ke User (Siapa yang berkomentar?)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 2. Relasi ke Material (Komentar ini ada di materi mana?)
    // INI YANG TADI MENYEBABKAN ERROR
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    // 3. Relasi Balasan (Opsional: Jika nanti mau fitur Reply bertingkat)
    public function replies()
    {
        return $this->hasMany(Discussion::class, 'parent_id');
    }
}