<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class BuyBook extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'image',
        'image_text',
        'invite_total',
        'introduction',
        'isbn',
        'price',
        'activity_price',
        'real_price',
    ];

    public function order()
    {
        return $this->hasMany(BuyOrder::class, 'book_id', 'id');
    }

}
