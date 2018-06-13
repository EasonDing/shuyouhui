<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends Model implements Transformable
{
    use TransformableTrait;

    protected $primaryKey = 'userid';

    protected $table = 'T_USERINFO';

    protected $guarded= [];

    public $timestamps = false;

}
