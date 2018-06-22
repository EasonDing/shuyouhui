<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Entities\Recharge;
use App\Entities\User;
use App\Entities\WeiXinUser;
use App\Http\Controllers\Controller;
use App\Http\Utils\Configuring;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\Api\MiniProgram\UserRepository;
use App\Validators\Api\MiniProgram\UserValidator;


class UsersController extends Controller
{

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserValidator
     */
    protected $validator;

    public function __construct(UserRepository $repository, UserValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }


    /**
     * 获取用户信息
     */
    public function show(Request $request)
    {
        if ($request->userId) {
            $user = WeiXinUser::query()->where('user_id', $request->userId)->first();
        } else {
            $user = Auth::user();
            $user = WeiXinUser::query()->find($user->id);
        }

        if ($user) {
            return $this->withCode(200)->withData($user)->response('用户信息获取成功');
        }

        return $this->withCode(500)->withData($user)->response('用户信息获取失败');

    }

    public function check_vip(Request $request)
    {
        $userId = $request->post('UserId');
        $is_vip = User::where(['userid'=>$userId])->first();
        if(empty($is_vip) ){

           return $this->withCode(500)->response('用户信息不存在');

        }else{
            if($is_vip->is_vip == 1){
                return $this->withCode(200)->withData($is_vip->is_vip)->response('success');
            }else{
                return $this->withCode(200)->response('success');
            }


        }



    }

    /**
     * 绑定手机号
     */
    public function bindPhone(Request $request)
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
        
        $phone = $request->get('phone');

        //检测是否曾经注册过账号
        $appUser = User::query()->where('loginName', $phone)->orWhere('bindPhone', $phone)->first();
        if ($appUser) {

            $appUserId = $appUser->userid;
            return $this->bindUser($request, $appUserId);
        }

        $userId = Auth::user()->user_id;
        $appUser = User::query()->where('userid', $userId)->update([
            'loginName'     => $phone,
            'bindPhone'     => $phone,
        ]);

        if ($appUser) {
            Redis::del($captchatKey);
            return $this->withCode(200)->response('手机号绑定成功');
        }

        return $this->withCode(500)->response('手机号绑定失败');

    }

    /**
     * 更新绑定手机号
     */
    public function updateBindPhone(Request $request) {
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

        $phone = $request->get('phone');

        $wxUser = Auth::user();
        $appUser = User::query()->find($wxUser->user_id);
        if ($appUser->bindPhone == $phone) {
            return $this->withCode(500)->response('与原手机号一致');
        }

        $appUser = User::query()->where('bindPhone', $phone)->whereKeyNot($wxUser->user_id)->first();
        if ($appUser) {
            return $this->withCode(500)->response('该手机号以被绑定');
        }

        $appUser = User::query()->where('userid', $wxUser->user_id)->update([
            'bindPhone'     => $phone
        ]);

        if ($appUser) {
            Redis::del($captchatKey);
            return $this->withCode(200)->response('手机号绑定更新成功');
        }

        return $this->withCode(500)->response('手机号绑定更新失败');

    }

    /**
     * 检测是否已绑定手机号
     */
    public function checkBindPhone(Request $request)
    {
       $user = Auth::user();

       $appUser = User::query()->where('userid', $user->user_id)->first();

       if ($appUser->bindPhone) {
           return $this->withCode(200)->response('已绑定手机号');
       }

       return $this->withCode(500)->response('未绑定手机号');
    }

    /**
     * 如果用户曾经注册过，需要进行账号关联
     */
    public function bindUser($request, $appUserId) {
        $wxUser = Auth::user();
        try {
            DB::beginTransaction();

            $appUser = User::query()->where('userid', $appUserId)->update([
                'username' => $request->nickname ? $request->nickname : 'bookfan' . rand(1000,9999),
                'UserLogo' => $request->headimgurl ? $request->headimgurl : '/images/userLogo.png',
                'sex'      => $request->gender ? $request->gender : 0,
                'city'     => $request->province . '省' . $request->city . '市',
                'bindPhone'=> $request->phone,
                'loginName'=> $request->phone,
            ]);

            //绑定旧账号后删除 新的账号
            $delAppUser = User::query()->find($wxUser->user_id)->delete();

            $wxUser = WeiXinUser::query()->where('id', $wxUser->id)->update([
                'user_id'   => $appUserId
            ]);

            $captchatKey = Configuring::CAPTCHA_KEY_MINIPROGRAM . $request->phone;
            if ($wxUser !== false && $appUser !== false && $delAppUser) {
                DB::commit();
                Redis::del($captchatKey);
                //将新的userId 返回 保存
                return $this->withCode(200)->withData(['userId' => $appUserId])->response('账号绑定成功！');
            }

            return $this->withCode(500)->response('账号绑定失败！');

        } catch (\Exception $e) {
            DB::rollback();
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            return $this->withCode(500)->withData($errors)->response('服务器错误！');
        }
    }

    //创建充值订单
    public function createOrder(Request $request) {
        $user = Auth::user();
        $appUser = User::find($user->user_id);
        $recharge = Recharge::query()->create([
            'userId'    => $user->user_id,
            'money'     => $request->get('money'),
            'updateTime'     => Carbon::now(),
            'type'     => 2,
            'orderNo'     => 'RE' . random_int(1, 9999999999999999),//订单号
            'status'     => 0,
            'phone'     =>  $appUser->bindPhone ? $appUser->bindPhone: '',
        ]);

        if ($recharge) {
            return $this->withCode(200)->withData($recharge)->response('预充值订单完成！');
        }

        return $this->withCode(500)->withData($recharge)->response('预充值订单失败！');
    }

    //更新订单状态
    public function updatePayStatus(Request $request) {
        $user = Auth::user();
        $recharge = Recharge::query()->find($request->get('orderId'))->update([
            'status'    => 1
        ]);
        $appUser = User::find($user->user_id);
        $updateMoney = User::query()->find($user->user_id)->update([
            'money' => $appUser->money + $request->get('money'),
        ]);
        dd($updateMoney);

        return $this->withCode(200)->withData($recharge)->response('订单支付状态更新成功！');
    }
}
