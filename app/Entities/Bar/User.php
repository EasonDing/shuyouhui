<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    protected $primaryKey = 'userid';

    protected $table = 'T_USERINFO';

    public function group(){
        return $this->belongsToMany(Group::class, 'T_ReGroupUserInfo', 'userId', 'groupId');
    }

}
