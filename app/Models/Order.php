<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'kost_id',
        'name',
        'email',
        'phone_number',
        'total_price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }
}
