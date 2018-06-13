<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class BookCode extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_Code';

    protected $fillable = [
        'code',
        'isbn',
        'flowPrice',
        'recommend',
    ];

    public function book(){
        return $this->hasOne(Book::class, 'isbn', 'isbn');
    }

}
