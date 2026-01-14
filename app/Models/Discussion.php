<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $table = 'discussions';
    public $timestamps = true;

    protected $fillable = [
        'parent_id', // <--- TAMBAHAN BARU
        'course_id',
        'material_id',
        'user_id',
        'message'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Komentar ini punya banyak balasan (Anak)
    public function replies()
    {
        return $this->hasMany(Discussion::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
