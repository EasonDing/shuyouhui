<?php

namespace App\Listeners;

use App\WechatActivies;
use App\Events\UserLogged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendUserLoginNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserLogged  $event
     * @return void
     */
    public function handle(UserLogged $event)
    {
        // 实际上应该是新增记录到系统日志表
        $id = $event->user->id;
        $name = $event->user->name;
        $log = WechatActivies::create($this->userlog($id,$name));
        if($log){
            Log::info('用户 ' . $event->user->name . ' 登录了系统');
        }else{
            Log::info('日志写入失败！');
        }
    }

    public function userlog($id,$name){
        return[
            'subject_id'    =>$id,
            'subject_type'  =>'user',
            'name'          =>'登录系统',
            'description'   =>'用户"'.$name.'"登录了系统',
        ];
    }
}
