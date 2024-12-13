<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'price',
        'phone_number',
        'image',
        'description',
        'address',
        'city',
        'regency',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function universities()
    {
        return $this->belongsToMany(University::class, 'university_kost', 'kost_id', 'university_id');
    }
}
