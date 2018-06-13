<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Book extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_BookInfo';

    protected $fillable = [
        'cate',
        'title',
        'author',
        'publisher',
        'image',
        'summary',
        'isbn',
        'price',
        'type',
        'updateTime'
    ];

}
