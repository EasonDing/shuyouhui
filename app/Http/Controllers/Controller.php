<?php

namespace App\Http\Controllers;

use App\Http\Utils\ResponseUtil;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Http\Utils\Helpers;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }


    public function sendError($result, $message, $code = 500)
    {
        return response(
            ResponseUtil::makeError($message, $result, $code),
            $code);
    }

    protected function getCurrentUser()
    {
        return Auth::user();
    }

    protected function groupId()
    {
        return collect(Auth::user())->get('group_id');
    }

    //书吧吧主 ID
    protected function userId()
    {
        return collect(Auth::user())->get('poster_id');
    }

    //短信接口
    function send_mms($mobile,$text,$qianming){
        $head      = 'zjxzh_bookfun';
        $server_ip ='122.224.186.100';
        $port	   = 9926;
        if(!$mobile || !$text) return;
        $buf = str_pad($head, 30, "\0").str_pad($mobile, 20, "\0").str_pad($text, 500, "\0").str_pad($qianming, 30, "\0");//有字?拗?
        $sockfd=socket_create(AF_INET,SOCK_STREAM,SOL_TCP)or die("创建失败");
        socket_connect($sockfd,$server_ip,$port) or die("连接错误");
        socket_write($sockfd,$buf,strlen($buf)) or die("写入错误");
        $a=socket_read($sockfd,30);
        $result = $a == str_pad("received", 30, "\0") ? true : false;
        $buf="";
        socket_close($sockfd);
        return $result;
    }
}
