<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Finance extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_inflowOrder';

    protected $fillable = [];

    public function user(){
        return $this->hasOne(User::class, 'userid', 'userId');
    }

}
