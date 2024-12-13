<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class university extends Model
{
    protected $fillable = [
        'university',
        'latitude',
        'longitude',
    ];

    public function kosts()
    {
        return $this->belongsToMany(Kost::class, 'university_kost', 'university_id', 'kost_id');
    }

}
