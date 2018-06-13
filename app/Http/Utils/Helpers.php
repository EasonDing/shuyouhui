<?php

namespace App\Http\Utils;

use Illuminate\Support\Facades\Auth;

trait Helpers
{

    use ApiResponse;

    /**
     * 短信接口
     * @param $mobile 手机号
     * @param $content 短信内容
     * @param $sign
     * @return bool|void
     */
    public function send_sms($mobile, $content, $sign)
    {
        $head = 'zjxzh_bookfun';
        $server_ip = '122.224.186.100';
        $port = 9926;
        if (!$mobile || !$content) return;
        $buf = str_pad($head, 30, "\0") . str_pad($mobile, 20, "\0") . str_pad($content, 500, "\0") . str_pad($sign, 30, "\0");
        $sockfd = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("创建失败");
        socket_connect($sockfd, $server_ip, $port) or die("连接错误");
        socket_write($sockfd, $buf, strlen($buf)) or die("写入错误");
        $a = socket_read($sockfd, 30);
        $result = $a == str_pad("received", 30, "\0") ? true : false;
        $buf = "";
        socket_close($sockfd);
        return $result;
    }

    /**
     * @param $path 图片路径
     * @param $file 图片资源
     */
    public function saveImage($path, $file)
    {
        $date = date('Y/m', time());
        $imagePath = $file->store($path . $date);

        return $imagePath;
    }
}