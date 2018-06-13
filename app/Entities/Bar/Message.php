<?php

namespace App\Entities\Bar;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Message extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'T_ChatInfo';

    protected $primaryKey = 'chatId';

    protected $fillable = [
        'title',
        'content',
        'otherId',
        'chatType',
        'updateTime',
        'userId',
        'url',
        'isbn',
        'ChatResult_bk',
        'time',
        'location',
        'recontent',
        'repjson'
    ];

    public $timestamps = false;

}
