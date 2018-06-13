<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends Model implements Transformable
{
    use TransformableTrait;

    protected $primaryKey = 'userid';

    protected $table = 'T_USERINFO';

    protected $fillable = [];

    public function groups(){
        return $this->belongsToMany(Group::class,'T_ReGroupUserInfo', 'userId', 'groupId')->wherePivot('type', 1);
    }

}
