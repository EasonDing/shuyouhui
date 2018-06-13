<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class BlogComment extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    protected $primaryKey = 'commentId';

    protected $table = 'T_CommentInfo';

    public function user(){
        return $this->hasOne(User::class, 'userid', 'userId');
    }

}
