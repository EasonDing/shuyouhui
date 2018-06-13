<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Blog extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_BlogInfo';

    protected $primaryKey = 'blogId';

    protected $fillable = [];

    public function user(){
        return $this->hasOne(User::class, 'userid', 'userId');
    }

}
