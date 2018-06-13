<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class AdminUser extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'name',
        'face',
        'sex',
        'mobile',
        'status',
        'type',
        'group_id',
        'group_name',
        'poster_id',
    ];

}
