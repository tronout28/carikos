<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $fillable = [
        'name',
        'owner',
        'user_id',
        'kost_type',
        'price',
        'phone_number',
        'image',
        'description',
        'status',
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
        return $this->belongsToMany(University::class, 'university_kosts', 'kost_id', 'university_id');
    }
}
