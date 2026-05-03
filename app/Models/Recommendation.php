<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'table_name',
        'table_code',
        'table_structure',
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
        return $this->hasMany(StatisticData::class, 'recommendation_id');
    }
    public function category()
    {
        // Ini adalah 'jembatan' yang menghubungkan angka 11 ke nama "Kesehatan"
        return $this->belongsTo(Category::class, 'category_id');
    }
}
