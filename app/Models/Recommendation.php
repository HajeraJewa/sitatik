<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = [
        'user_id',
        'table_name',
        'table_code',
        'table_structure',
        'category',
        'description',
        'start_date',
        'end_date',
        'status',
        'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statisticData()
    {
        // Satu rekomendasi (struktur) bisa memiliki banyak isi data (per tahun)
        return $this->hasMany(StatisticData::class, 'recommendation_id');
    }
}
