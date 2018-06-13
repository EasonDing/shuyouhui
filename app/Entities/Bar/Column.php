<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Column extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'group_id',
        'title',
        'content',
        'image',
    ];

}
