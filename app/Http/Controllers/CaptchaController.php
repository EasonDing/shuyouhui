<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class CaptchaController extends Controller
{

    /**
     * 验证码过期时间(分钟)
     *
     * @var integer
     */
    const CAPTCHA_EXPIRE = 10;


    /**
     * 前缀识别码，与前缀一一对应
     */
    const CAPTCHA_KEY_PREFIX_CODE = [
        0,//微信小程序
        1,//微信公众号
    ];

    /**
     * 验证码存储 key 前缀
     *
     * @var string
     */
    const CAPTCHA_KEY_PREFIX = [
        'Captcha-Wx-MiniProgram-',//微信小程序
        'Captcha-Wx-OfficialAccount-',//微信公众号
    ];

    /**
     * 获取短信验证码
     * @param Request $request
     * @return int|mixed
     */
    public function captcha(Request $request)
    {
        $this->validate($request, [
            'phone' => [
                'required',
                'regex:/^0?(13|14|15|17|18)[0-9]{9}$/'
            ],
            'prefix'    => [
                'required',
                Rule::in(self::CAPTCHA_KEY_PREFIX_CODE)
            ]
        ], [
            'phone.required'     => '手机号不能为空!',
            'phone.regex' => '请输入正确的手机号!',
            'prefix.required'   => '前缀不能为空'
        ]);

        $phone = $request->get('phone');
        $prefix = $request->get('prefix');
        //拼接键名前缀，为了区分不同功能使用的验证码
        $captchaKey = self::CAPTCHA_KEY_PREFIX[$prefix] . $phone;

        if (Redis::get($captchaKey)) {
            return $this->withCode(500)->response('操作频繁，请稍后再试');
        }

        try {
            $sign = '';
            $content = iconv('utf-8', 'gbk', '您的验证码为:');
            $captcha = rand(1000, 9999);
            $content = $content . $captcha;

            $sendSms = $this->send_sms($phone, $content, $sign);
            Redis::setex($captchaKey, self::CAPTCHA_EXPIRE * 60, $captcha);
            if ($sendSms && Redis::get($captchaKey)) {
                return $this->withCode(200)->response('短信发送成功!');
            }

            return $this->withCode(500)->withData($sendSms)->response('短信发送失败!');

        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            return $this->withCode(500)->withData($errors)->response('短信提供商服务器错误！');
        }
    }
}
