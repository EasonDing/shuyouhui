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
use App\Repositories\Api\MiniProgram\BuyOrderRepository;
use App\Validators\Api\MiniProgram\BuyOrderValidator;

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

    /**
     * 检查是否为书友会会员
     * @param Request $request
     * @return mixed
     */
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


    /*
     * 我邀请的好友
     */
    public function invite_user(Request $request)
    {
        $userId = $request->post('UserId');
        $type = $request->post('t_type');
        $all_info = DB::table('invite_vip')
            ->join('weixin_users', 'invite_vip.invited_id', '=', 'weixin_users.user_id')
            ->where(['invite_vip.invite_id'=>$userId])
            ->select('weixin_users.id','weixin_users.nickname', 'weixin_users.user_id','weixin_users.headimgurl','weixin_users.province','weixin_users.city')
            ->get();
        $info_array = $all_info->toArray();
        $all_number = count($info_array) > 0 ? count($info_array):0;
        $u_info = DB::table('weixin_users')->where(['user_id'=>$userId])->first();
        if($type == 'my'){
            $all_income = DB::table('invite_vip')->where(['invite_id'=>$userId])->sum('reward');

            $all_income = $all_income/100;
            $data = [
                'income'=>$all_income,
                'number'=>$all_number,
                'info'=>$all_info,
                'u_info'=>$u_info
            ];
        }else{

            $data = [
                'u_info'=>$u_info,
                'number'=>$all_number,
                'info'=>$all_info
            ];
        }
        return $this->withCode(200)->withData($data)->response('success');
    }

    /**
     * 订单创建
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function order_vip(Request $request)
    {
        try {
            $invite_id = $request->post('invited_id');
            $userId = $request->post('userId');
            $user = Auth::user();
            $number = 'BK' . uniqid();//订单号
            //判断两个人是否已经创建过订单
            $is_exist = DB::table('invite_vip_order')->where(['userid'=> $userId,'invited_id'=> $invite_id])->first();
            if($is_exist){
                #dump($is_exist);exit;
                $data = get_object_vars($is_exist);
                return $this->withCode(200)->withData($data)->response('订单已存在！');
            }else{
                $data = [
                    'order_number'=>$number ,
                    'userid'=> $userId,
                    'invited_id'=> $invite_id,
                    'order_type'=> '会员开通',
                    'price'=> 9.9,//单位分
                    'create_time'=> date('Y-m-d H-i-s',time())
                ];

                $result = DB::table('invite_vip_order')->insert($data);


                if ($result) {

                    return $this->withCode(200)->withData($data)->response('订单创建成功！');
                }

                return $this->withCode(500)->withData($data)->response('订单创建失败！');
            }


        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];

            return $this->withCode(500)->withData($errors)->response('服务器错误');
        }
    }

    public function update_order(Request $request)
    {
        try{
            $order_number = $request->post('order');
            $invite_id = $request->post('invited_id');
            $userId = $request->post('userId');
            DB::beginTransaction();
            $invite_data = [
                'invite_id'=>$invite_id,
                'invited_id'=>$userId,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'reward'=>rand(0,200),
            ];

            $invite_insert = DB::table('invite_vip')->insert($invite_data);

            $update_data = [
                'status'=>1,
                'pay_time'=>date('Y-m-d H:i:s',time())
            ];
            $up_order = DB::table('invite_vip_order')->where(['order_number'=>$order_number])->update($update_data);

            if( $invite_insert && $up_order){
                $is_vip = User::where(['userid'=>$userId])->first();
                if($is_vip->is_vip == 0){
                    $vip_update = User::where(['userid'=>$userId])->update(['is_vip'=>1]);
                    if($vip_update){
                        DB::commit();
                        return $this->withCode(200)->response('信息修改成功');
                    }else{
                        DB::rollback();
                        return $this->withCode(200)->response('信息修改失败');
                    }
                }
                DB::commit();
                return $this->withCode(200)->response('信息修改成功');
            }else{
                DB::rollback();
                return $this->withCode(200)->response('信息修改失败');
            }
        }catch (\Exception $e){

            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];

            return $this->withCode(500)->withData($errors)->response('服务器错误');
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
