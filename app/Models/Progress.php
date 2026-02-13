<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progress';
    public $timestamps = false; // Karena tabelmu tidak ada created_at/updated_at default

    protected $fillable = ['user_id', 'material_id', 'status', 'tanggal_selesai'];

    // --- TAMBAHKAN INI ---
    public function material()
    {
        // Relasi: Progress milik satu Material
        return $this->belongsTo(Material::class, 'material_id');
    }
}
