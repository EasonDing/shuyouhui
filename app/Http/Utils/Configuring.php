<?php

namespace App\Http\Utils;

class Configuring {

    /**
     * redis缓存 小程序验证码前缀
     */
    const CAPTCHA_KEY_MINIPROGRAM = 'Captcha-Wx-MiniProgram-';
    /**
     * redis缓存 微信公众号验证码前缀
     */
    const CAPTCHA_KEY_OFFICIALACCOUNT = 'Captcha-Wx-OfficialAccount-';
}
