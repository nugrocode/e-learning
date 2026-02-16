<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = true;

    protected $fillable = [
    'nim_nidn',
    'nama_lengkap',
    'password',
    'role',
    'foto_profil',
    'google_token',
    'google_refresh_token'
];

    protected $hidden = [
        'password',
    ];

    // =================================================================
    // DEFINISI RELASI
    // =================================================================

    /**
     * Relasi ke tabel Progress.
     * User (Mahasiswa) memiliki banyak data Progress belajar.
     */
    public function progress()
    {
        return $this->hasMany(Progress::class, 'user_id');
    }

    /**
     * Relasi ke tabel Submissions.
     * User mengirimkan banyak Tugas.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    /**
     * Relasi ke tabel Discussions.
     * User menulis banyak Komentar/Pertanyaan.
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'user_id');
    }

    /**
     * Relasi ke tabel Notifications.
     * User menerima banyak Notifikasi.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}