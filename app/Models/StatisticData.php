<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatisticData extends Model
{
    use HasFactory;

    // Menentukan nama tabel (opsional jika nama file sudah jamak)
    protected $table = 'statistic_data';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'recommendation_id',
        'user_id',
        'tahun',
        'isi_data'
    ];

    /**
     * Cast kolom isi_data menjadi array secara otomatis.
     * Ini SANGAT PENTING agar Laravel bisa membaca data JSON 
     * sebagai array PHP biasa.
     */
    protected $casts = [
        'isi_data' => 'array',
    ];

    /**
     * Relasi ke Model Recommendation (Struktur Tabel)
     */
    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_id');
    }

    /**
     * Relasi ke Model User (OPD Pemilik Data)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
