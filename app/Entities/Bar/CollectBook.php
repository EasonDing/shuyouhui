<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class CollectBook extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_CollectBookInfo';

    protected $fillable = [
        'codeid',
        'ISBN',
        'summary',
        'type',
        'collectId',
        'updateTime',
        'inflow',
        'inType',
        'address',
        'name',
        'tel',
    ];

    public function book(){
        return $this->hasOne(Book::class, 'isbn', 'ISBN');
    }


}
