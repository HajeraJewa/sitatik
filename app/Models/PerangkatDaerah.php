<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerangkatDaerah extends Model
{
    // FIX: Laravel secara default menambah 's' di akhir nama tabel. 
    // Baris ini memaksa Laravel menggunakan nama yang benar.
    protected $table = 'perangkat_daerah';

    protected $fillable = [
        'kode_opd',
        'nama_opd',
        'alias_opd'
    ];
}