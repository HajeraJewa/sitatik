<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'perangkat_daerah_id',
        'role',
        'latitude',
        'longitude',
    ];

    /**
     * Relasi ke Perangkat Daerah (Sudah Benar)
     */
    public function perangkatDaerah()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'perangkat_daerah_id');
    }

    /**
     * FIX FINAL: Tambahkan Relasi ke Recommendations
     * Menghilangkan error "Call to undefined relationship [recommendations]"
     */
    public function recommendations()
    {
        // Satu User (Operator) bisa memiliki banyak pengajuan rekomendasi
        return $this->hasMany(Recommendation::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}