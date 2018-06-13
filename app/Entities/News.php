<?php
namespace App\Entities;
use App\Entities\Admin\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class News extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    protected $guarded = [];
    public function user(){
        return $this->hasOne(User::class, 'userid', 'user_id');
    }
}