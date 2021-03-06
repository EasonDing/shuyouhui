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
            $book_num1 = DB::table('vip_book')->where(['userid'=>$userId])->get();
            $book_num2 = DB::table('T_CollectBookInfo')->where(['collectId'=>$userId])->get();
            $book_num1 = $book_num1->toArray();
            $book_num2 = $book_num2->toArray();
            $all_book = count($book_num1) + count($book_num2);
            $wx_account = User::where(['userid'=>$userId])->select('weixin_account')->first();

            $data = [
                'u_info'=>$u_info,
                'number'=>$all_number,
                'info'=>$all_info,
                'all_book'=>$all_book,
                'wx_account'=>$wx_account
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

            //$user = Auth::user();
            //var_dump($user);exit;
            //$userId = $user->user_id;
            if(empty(trim($userId))){
                $data = [

                ];
                return $this->withCode(500)->withData($data)->response('用户信息已过期！');
            }
            $number = 'BK' . uniqid();//订单号
            //判断两个人是否已经创建过订单
            if(!empty($invite_id)){
                $is_exist = DB::table('invite_vip_order')->where(['userid'=> $userId,'invited_id'=> $invite_id])->first();
                if($is_exist){
                    $data = get_object_vars($is_exist);
                    return $this->withCode(200)->withData($data)->response('订单已存在！');
                }
            }

            $data = [
                'order_number'=>$number ,
                'userid'=> $userId,
                'invited_id'=> $invite_id ? $invite_id : 0,
                'order_type'=> '会员开通',
                'price'=> 9.90,//单位分
                'create_time'=> date('Y-m-d H-i-s',time())
            ];

            $result = DB::table('invite_vip_order')->insert($data);


            if ($result) {

                return $this->withCode(200)->withData($data)->response('订单创建成功！');
            }

            return $this->withCode(500)->withData($data)->response('订单创建失败！');



        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];

            return $this->withCode(500)->withData($errors)->response('服务器错误');
        }
    }

    /**
     * 支付成功修改状态
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update_order(Request $request)
    {
        try{
            $order_number = $request->post('order');
            $invite_id = $request->post('invited_id');
            $userId = $request->post('userId');
            DB::beginTransaction();
            if($invite_id ){
                $invite_data = [
                    'invite_id'=>$invite_id,
                    'invited_id'=>$userId,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'reward'=>rand(0,200),
                ];

                $invite_insert = DB::table('invite_vip')->insert($invite_data);
            }


            $update_data = [
                'status'=>1,
                'pay_time'=>date('Y-m-d H:i:s',time())
            ];
            $up_order = DB::table('invite_vip_order')->where(['order_number'=>$order_number])->update($update_data);

            if( $up_order ){
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
     * 绑定微信号
     */
    public function bindWechat(Request $request)
    {

//        $regex = '/^[ a-z0-9]$/i';
//        if(!preg_match($regex, $request->get('wechat'))){
//            return $this->withCode(500)->response('微信号错误!');
//        }
        $user = Auth::user();
        //var_dump($user->user_id);exit;
        if(trim(empty($request->get('wechat')))){
            return $this->withCode(500)->response('微信号错误!');
        }
        $wechat = $request->get('wechat');
        //检测是否曾经注册过账号
        $appUser = User::Where(['weixin_account'=>$wechat])->first();
        #var_dump($appUser);exit;
        if ($appUser) {
            return $this->withCode(500)->response('该微信号已被绑定!');
        }
        $userId = $user->user_id;
       #var_dump($userId);
        $appUser = User::where('userid', $userId)->update(['weixin_account'=> $wechat]);
        if ($appUser) {
            return $this->withCode(200)->response('微信号绑定成功');
        }
        return $this->withCode(500)->response('微信号绑定失败');

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
        #dd($updateMoney);

        return $this->withCode(200)->withData($recharge)->response('订单支付状态更新成功！');
    }

    /**
     * 激活图书封面上传
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     */
    public function upload_pic(Request $request)
    {

        #var_dump($request->file('book_pic'));exit;
        $file = $request->file('book_pic');//获取图片
        $url_path = 'uploads/cover/'.date('Y-m-d');
        $rule = ['jpg', 'png', 'gif','jpeg'];
        if ($file->isValid()) {
            $clientName = $file->getClientOriginalName();
            #var_dump($clientName);exit;
            $tmpName = $file->getFileName();
            $realPath = $file->getRealPath();
            $entension = $file->getClientOriginalExtension();
            if (!in_array($entension, $rule)) {
                return '图片格式为jpg,png,gif';
            }
            $newName = uniqid() . "." . $entension;
            $path = $file->move($url_path, $newName);
            $namePath = "/".$url_path . '/' . $newName;
            if($path){
                $data = [
                    'path'=>$namePath
                ];
                return $this->withCode(200)->withData($data)->response('上传成功！');
            }else{
                return $this->withCode(500)->response('上传失败！');
            }
        }else{
            return $this->withCode(500)->response('上传失败！');
        }
    }

    public function is_activation(Request $request)
    {
        $qrcode = $request->post('qrcode');
        $is_exist = DB::table('vip_book')->where(['qrcode'=>$qrcode])->first();
        if($is_exist){
           # dump($is_exist->id);exit;
            return $this->withCode(500)->withData(['id'=>$is_exist->id])->response('该二维码已绑定书籍！');
        }else{
            return $this->withCode(200)->response('该二维码未绑定书籍！');
        }
    }

    public function upload_book(Request $request)
    {
        $book_name = $request->post('book_name');
        $bookcbs = $request->post('bookcbs');
        $bookauther = $request->post('bookauther');
        $book_liuyan = $request->post('book_liuyan');
        $pic_url = $request->post('pic_url');//
        $userId = $request->post('userId');
        $qrcode = $request->post('qrcode');
        if(empty(trim($book_name))){
            return $this->withCode(500)->response('书名不能为空！');
        }
        if(empty(trim($bookcbs))){
            return $this->withCode(500)->response('出版社不能为空！');
        }
        if(empty(trim($bookauther))){
            return $this->withCode(500)->response('作者不能为空！');
        }
        if(empty(trim($book_liuyan))){
            return $this->withCode(500)->response('留言不能为空！');
        }
        if(empty(trim($pic_url))){
            return $this->withCode(500)->response('请先上传封面！');
        }
        if(empty(trim($userId))){
            return $this->withCode(500)->response('登录信息已过期！');
        }
        if(empty(trim($qrcode))){
            return $this->withCode(500)->response('请扫描二维码进去！');
        }
        $data = [
            'userid'=>$userId,
            'title'=>$book_name,
            'author'=>$bookauther,
            'image'=>"http://".$_SERVER['SERVER_NAME'].$pic_url,
            'qrcode'=>$qrcode,
            'publisher'=>$bookcbs,
            'summary'=>$book_liuyan,
            'created_at'=>date('Y-m-d H:i:s',time())
        ];

        $is_exist = DB::table('vip_book')->where(['qrcode'=>$qrcode])->first();
        if($is_exist){
            return $this->withCode(500)->response('该二维码已绑定书籍！');
        }

        $result = DB::table('vip_book')->insert($data);
        if($result){
            return $this->withCode(200)->response('激活成功！');
        }else{
            return $this->withCode(500)->response('激活失败！');
        }

    }

    public function vipBookDetail(Request $request)
    {
        $book_id = $request->post('book_id');
        $userId = $request->post('userId');
        $is_look = DB::table('vip_book_look')->where(['book_id'=>$book_id,'userid'=>$userId])->first();

        if(empty($is_look)){

            DB::table('vip_book_look')->insert(['userid'=>$userId,'book_id'=>$book_id,'time'=>date('Y-m-d H:i:s')]);
        }
        $book_info = DB::table('vip_book')->where(['id'=>$book_id])->first();
        $u_info = DB::table('weixin_users')->where(['user_id'=>$book_info->userid])->first();
        if($book_info){
            $look_info = DB::table('vip_book_look')
                ->join('T_USERINFO','vip_book_look.userid','=','T_USERINFO.userid')
                ->where(['book_id'=>$book_id])
                ->select('T_USERINFO.userid', 'T_USERINFO.UserLogo')
                ->orderByRaw('vip_book_look.time desc')
                ->get();
            $liuyan_info = DB::table('vip_book_message')
                ->join('T_USERINFO','vip_book_message.userid','=','T_USERINFO.userid')
                ->where(['book_id'=>$book_id])
                ->select('vip_book_message.message','vip_book_message.time','T_USERINFO.userid', 'T_USERINFO.UserLogo','T_USERINFO.username')
                ->orderByRaw('vip_book_message.time desc')
                ->get();
            $look_info = $look_info->toArray();
            $liuyan_info = $liuyan_info->toArray();
            $data = [
                'book_info'=>$book_info,
                'look_info'=>$look_info,
                'liuyan_info'=>$liuyan_info,
                'u_info'=>$u_info
            ];
            return $this->withCode(200)->withData($data)->response('详情返回成功！');
        }else{
            return $this->withCode(500)->response('该图书不存在！');
        }
    }

    public function add_message(Request $request)
    {
        $book_id = $request->post('book_id');
        $user_id = $request->post('userId');
        $message = $request->post('message');
        if(empty(trim($message))){
            return $this->withCode(500)->response('请输入留言内容！');
        }
        $data = [
            'userid'=>$user_id,
            'message'=>$message,
            'book_id'=>$book_id,
            'time'=>date('Y-m-d H:i:s',time())
        ];
        $result = DB::table('vip_book_message')->insert($data);
        if($result){
            $liuyan_info = DB::table('vip_book_message')
                ->join('T_USERINFO','vip_book_message.userid','=','T_USERINFO.userid')
                ->where(['book_id'=>$book_id])
                ->select('vip_book_message.message','vip_book_message.time','T_USERINFO.userid', 'T_USERINFO.UserLogo','T_USERINFO.username')
                ->orderByRaw('vip_book_message.time desc')
                ->get();
            $liuyan_info = $liuyan_info->toArray();
            return $this->withCode(200)->withData($liuyan_info)->response('留言成功！');
        }else{
            return $this->withCode(500)->response('留言失败！');
        }
    }

    public function weixin_account(Request $request)
    {
        $userId = $request->post('UserId');
        $info = User::where(['userid'=>$userId])->first();
        $info = $info->toArray();
        if($info['weixin_account']){
            return $this->withCode(200)->withData(['wx_account'=>$info['weixin_account']])->response('获取成功！');
        }else{
            return $this->withCode(500)->response('获取绑定微信失败！');
        }
    }

    public function phone_account(Request $request)
    {
        $userId = $request->post('UserId');
        $info = User::where(['userid'=>$userId])->first();
        $info = $info->toArray();
        if($info['bindPhone']){
            return $this->withCode(200)->withData(['wx_account'=>$info['weixin_account']])->response('获取成功！');
        }else{
            return $this->withCode(500)->response('获取绑定微信失败！');
        }
    }

}
