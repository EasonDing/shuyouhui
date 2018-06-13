<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Finance extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_recharge';

    protected $fillable = [];

    public function user(){
        $this->hasOne(User::class, 'userid', 'userId');
    }

}
