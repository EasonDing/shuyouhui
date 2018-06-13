<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WeiXinUser extends Authenticatable implements Transformable
{
    use HasApiTokens, TransformableTrait;

    protected $table = 'weixin_users';

    protected $fillable = [
        'unionid',
        'openid',
        'user_id',
        'nickname',
        'sex',
        'language',
        'city',
        'province',
        'country',
        'headimgurl',
    ];

}
