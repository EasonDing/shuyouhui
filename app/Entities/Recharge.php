<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Recharge extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_recharge';

    protected $guarded = [];

    public $timestamps = false;

}
