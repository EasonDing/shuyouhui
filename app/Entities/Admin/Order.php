<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Order extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $table = 'T_inflowOrder';

    public $timestamps = false;

    protected $fillable = [];

    public function user()
    {
        return $this->hasOne(User::class, 'userid', 'userId');
    }

    public function code()
    {
        return $this->hasOne(BookCode::class, 'code', 'qrCode');
    }

}
