<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Book extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

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
