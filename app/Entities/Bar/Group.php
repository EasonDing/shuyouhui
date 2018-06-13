<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Group extends Model implements Transformable
{
    use TransformableTrait;

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'groupId';

    protected $table = 'T_ClubGroupInfo';

    public function users(){
        return $this->belongsToMany(User::class, 'T_ReGroupUserInfo', 'groupId', 'userId')->withPivot(['updateTime','type']);
    }

}
