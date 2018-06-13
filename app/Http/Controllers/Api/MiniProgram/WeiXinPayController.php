<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Controller;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;


class WeiXinPayController extends Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = app('wechat.payment');
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'title' => 'required',
            'number' => 'required',
            'total_fee' => 'required',
        ]);

        $config = [
            'app_id' => env('WECHAT_MINI_PROGRAM_APPID'),
            'secret' => env('WECHAT_MINI_PROGRAM_SECRET'),
        ];

        $app = Factory::miniProgram($config);
        $miniUser = $app->auth->session($request->code);

        $order = [
            'out_trade_no' => time(),
            'total_fee' => $request->get('total_fee') * 100, // **单位：分**
            'body' => $request->get('title') ? $request->get('title') : '购买书籍',
            'openid' => $miniUser['openid'],
            'trade_type' => 'JSAPI',
        ];

        $pay = $this->app->order->unify($order);
        $json = $this->app->jssdk->bridgeConfig($pay['prepay_id'], false);

        return $json;
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success();// laravel 框架中请直接 `return $pay->success()`
    }
}
