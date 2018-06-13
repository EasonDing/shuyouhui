<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class BuyInvite extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'user_id',
        'order_id',
    ];

}
