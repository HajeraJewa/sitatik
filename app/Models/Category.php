<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['kode_kategori', 'nama_kategori'];

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'category_id');
    }
}