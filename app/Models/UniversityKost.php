<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversityKost extends Model
{
    protected $fillable = [
        'university_id',
        'kost_id',
    ];

    public function university()
    {
        return $this->belongsTo(university::class);
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }
}
