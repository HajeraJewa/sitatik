<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'activity', 'description', 'ip_address', 'user_agent'];

    // Relasi ke User untuk mendapatkan nama & OPD
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Fungsi statis agar mudah dipanggil di Controller manapun
    public static function record($activity, $description)
    {
        self::create([
            'user_id' => auth()->id(),
            'activity' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}