<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{


    protected $table = 'roles';

    /**
     * 用户角色
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
