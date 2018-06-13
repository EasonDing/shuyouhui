<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ColumnComment extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_CommentInfo';

    protected $fillable = [];

}
