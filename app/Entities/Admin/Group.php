<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Group extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = false;

    protected $primaryKey = 'groupId';

    protected $table = 'T_ClubGroupInfo';

    protected $fillable = [];

    //书吧成员
    public function users()
    {
        return $this->belongsToMany(User::class, 'T_ReGroupUserInfo', 'groupId', 'userId')->withPivot('type');
    }

    //书吧书籍
    public function books(){
        return $this->hasMany(CollectBook::class , 'collectId', 'poster');
    }

}
