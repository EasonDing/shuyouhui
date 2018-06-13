<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class GroupUserRelation extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = false;

    protected $table = 'T_ReGroupUserInfo';

    protected $fillable = [];

}
