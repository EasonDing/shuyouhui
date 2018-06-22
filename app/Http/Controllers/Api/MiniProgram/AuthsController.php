<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Entities\User;
use App\Entities\WeiXinUser;
use App\Http\Controllers\Controller;
use App\Http\Utils\Configuring;
use Carbon\Carbon;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class AuthsController extends Controller
{
    /**
     * 微信小程序授权 获取unionid
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function auth(Request $request)
    {
        $this->validate($request, [
            'code' => [
                'required',
            ],
        ], [
            'required' => '微信code 不能为空',
        ]);

        $config = [
            'app_id' => env('WECHAT_MINI_PROGRAM_APPID'),
            'secret' => env('WECHAT_MINI_PROGRAM_SECRET'),
        ];

        $app = Factory::miniProgram($config);
        $miniUser = $app->auth->session($request->code);

        try {
            $attributes = ['unionid' => $miniUser['unionid']];
            $request->offsetSet('openid', $miniUser['openid']);
            $wxUser = WeiXinUser::query()->updateOrCreate($attributes, $request->all());

            if ($wxUser) {
                $token = $wxUser->createToken($miniUser['unionid'])->accessToken;
                return $this->withCode(200)->withData(['token' => $token])->response('用户授权成功');
            }

            return $this->withCode(500)->withData($wxUser)->response('用户授权失败');

        } catch (\Exception $e) {

            $errors = [
                'maybe'   => ['passport:install'],
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            return $this->withCode(500)->withData($errors)->response('服务器错误');
        }
    }

    /**
     * 在数据库中生成一条用户记录
     * @param Request $request
     * @return Response $response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone'   => [
                'required',
                'regex:/^0?(13|14|15|17|18)[0-9]{9}$/'
            ],
            'captcha' => 'required'
        ], [
            'phone.required'   => '手机号不能为空!',
            'captcha.required' => '验证码不能为空！',
            'phone.regex'      => '请输入正确的手机号!'
        ]);

        $captchatKey = Configuring::CAPTCHA_KEY_MINIPROGRAM . $request->phone;
        //判断验证码是否正确
        $code = Redis::get($captchatKey);

        if (!$code) {
            return $this->withCode(500)->response('验证码错误!');
        }

        if ($code != $request->captcha) {
            return $this->withCode(500)->response('验证码输入有误!');
        }


        //判断$request->phone 是否已经注册过 有注册就关联 没注册就创建一个新用户记录
        try {
            $appUser = User::query()->where('loginName', $request->phone)->orWhere('bindPhone', $request->phone)->first();
            DB::beginTransaction();

            if (!$appUser) {
                $appUser = User::query()->create([
                    'loginName'   => $request->phone,
                    'username'    => 'bookfan' . rand(10000, 99999),
                    'UserLogo'    => '/images/DefaultLogo.jpg',
                    'sex'         => 0,
                    'city'        => '',
                    'clientid'    => '',
                    'devicetoken' => '',
                    'Birthday'    => '',
                    'hxid'        => '',
                    'hxpass'      => '',
                    'user_groups' => '',
                    'money'       => '0.00',
                    'bindPhone'   => $request->phone,
                    'updateTime'  => Carbon::now(),
                ]);
            }

            $userId = Auth::user()->id;
            $wxUser = WeiXinUser::query()->where('id', $userId)->update([
                'user_id' => $appUser->userid
            ]);

            if ($appUser && $wxUser) {
                DB::commit();
                //删除redis验证码
                Redis::del($captchatKey);
                return $this->withCode(200)->withData(['user_id' => $appUser->userid])->response('用户创建成功');
            }

            return $this->withCode(500)->response('用户创建失败');

        } catch (\Exception $e) {
            DB::rollback();
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            return $this->withCode(500)->withData($errors)->response('服务器错误！');
        }

    }

    /**
     * 更新用户信息
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->user_id) {
            $appUser = User::query()->where('userid', $user->user_id)->update([
                'username' => $request->nickname,
                'UserLogo' => $request->headimgurl,
                'sex'      => $request->gender,
                'city'     => $request->province . '省' . $request->city . '市',
            ]);
            $userId = $user->user_id;
        } else {
            $appUser = User::query()->create([
                'loginName'=> $user['unionid'],
                'username' => $request->nickname ? $request->nickname : 'bookfan' . rand(10000, 99999),
                'UserLogo' => $request->headimgurl ? $request->headimgurl : '/images/DefaultLogo.jpg',
                'sex'      => $request->gender ? $request->gender : 0,
                'city'     => $request->province ? $request->province . '省' . $request->city . '市' : '未知',
                'updateTime'  => Carbon::now(),
            ]);
            $userId = $appUser->userid;
        }

        $request->offsetSet('user_id', $userId);
        $wxUser = WeiXinUser::query()->where('id', $user->id)->update($request->all());


        if ($wxUser !== false && $appUser !== false) {
            return $this->withCode(200)->withData(['user_id' => $userId])->response('用户信息更新成功');
        }

        return $this->withCode(500)->response('用户信息更新成功');

    }

    /**
     * 检查用户是否已关联数据
     */
    public function checkUserInfo()
    {
        $userId = Auth::user()->user_id;
        #$is_vip = Auth::user();
        #dump($is_vip);exit;
        if ($userId) {
            return $this->withCode(200)->withData(['user_id' => $userId])->response('用户数据已关联');
        }


    }


}
