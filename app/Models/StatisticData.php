<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatisticData extends Model
{
    use HasFactory;

    protected $table = 'statistic_data';

    protected $fillable = [
        'recommendation_id',
        'user_id',
        'tahun',
        'isi_data',
        'is_final',
    ];
    protected $casts = [
        'isi_data' => 'array',
    ];
    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
