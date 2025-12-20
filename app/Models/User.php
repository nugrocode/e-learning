<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'nim_nidn',
        'nama_lengkap',
        'password',
        'role',
        'foto_profil'
    ];

    protected $hidden = [
        'password',
    ];
}
