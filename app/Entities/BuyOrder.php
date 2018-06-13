<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class BuyOrder extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'book_id',
        'user_id',
        'area',
        'address',
        'phone',
        'price',
        'real_price',
        'activity_status',
        'pay_time',
        'pay_status',
        'number'
    ];

    public function buyBook()
    {
        return $this->hasOne(BuyBook::class, 'id', 'book_id');
    }

    public function buyInvite()
    {
        return $this->hasMany(BuyInvite::class, 'order_id', 'id');
    }

    public function buyUser()
    {
        return $this->hasOne(User::class, 'userid', 'user_id');
    }

}
