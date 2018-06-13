<?php

namespace App\Http\Controllers;

use App\Entities\Group;
use App\Entities\News;
use App\Entities\UserInfo;
use App\ReGroupUserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require_once(dirname(__FILE__) . '/' . '../library/getui/IGt.Push.php');
require_once(dirname(__FILE__) . '/' . '../library/getui/igetui/IGt.AppMessage.php');
require_once(dirname(__FILE__) . '/' . '../library/getui/igetui/IGt.APNPayload.php');
require_once(dirname(__FILE__) . '/' . '../library/getui/igetui/template/IGt.BaseTemplate.php');
require_once(dirname(__FILE__) . '/' . '../library/getui/IGt.Batch.php');
require_once(dirname(__FILE__) . '/' . '../library/getui/igetui/utils/AppConditions.php');

class PushController extends Controller
{
    //管理员个推
    public function admin(Request $request){
        //http的域名
//        define('HOST', 'http://sdk.open.api.igexin.com/apiex.htm');

        //https的域名
        define('HOST','https://api.getui.com/apiex.htm');


        define('APPKEY', env('PUSHER_APP_KEY'));
        define('APPID', env('PUSHER_APP_ID'));
        define('MASTERSECRET', env('PUSHER_APP_SECRET'));

        $type = $request->get('type');
        $clientid = $request->get('clientid');
        $pushData = [
            'title'     =>$request->get('title'),
            'content'   =>$request->get('content'),
            'username'  =>$request->get('username'),
        ];
        //个推类型 1、全员， 2、吧主 ，3 个人
        if ($type == 1){
            $push = $this->pushMessageToApp($pushData);
        }else if ($type == 2){
            //获取所有吧主的clientid
            $posterId = Group::query()->pluck('poster');
            $clientid = UserInfo::query()->whereIn('userid', $posterId)->pluck('clientid');
            $data = array();
            for ($i=0; $i<count($clientid); $i++){
                if (!empty($clientid[$i])){
                    $data[] = $clientid[$i];
                }
            }
            $push = $this->pushMessageToSingleBatch($pushData, $data);
        }else if ($type == 3){
            //判断clientid不等于空再发送
            if (empty($clientid)){
                return $this->sendError($clientid, 'clientid为空',501);
            }
            $push =$this->pushMessageToSingle($pushData,$clientid);
        }
        $user = Auth::user();
        //给数据添加其它信息，用于存储到数据库
        $pushData['news_type']  =$type == 3 ? 2 : 1;
        $pushData['user_type']  =$type;
        $pushData['send_id']    =empty($user['poster_id']) ? 1 : $user['poster_id'];
        $pushData['user_id'] =$request->get('userid');//接收者id

        //判断消息是否推送成功 1、成功 2、失败
        if ($push['result'] == "ok"){
            $pushData['status'] = 1;
            $pushData = News::query()->create($pushData);
            return $this->sendResponse($pushData, '消息推送成功');
        }else{
            $pushData['status'] = 2;
            $pushData = News::query()->create($pushData);
            return $this->sendError($pushData, '消息推送失败');
        }

    }

    //吧主个推
    public function bar(Request $request){
        //http的域名
//        define('HOST', 'http://sdk.open.api.igexin.com/apiex.htm');

        //https的域名
        define('HOST','https://api.getui.com/apiex.htm');


        define('APPKEY', env('PUSHER_APP_KEY'));
        define('APPID', env('PUSHER_APP_ID'));
        define('MASTERSECRET', env('PUSHER_APP_SECRET'));

        $type = $request->get('type');
        $clientid = $request->get('clientid');
        $pushData = [
            'title'     =>$request->get('title'),
            'content'   =>$request->get('content'),
            'username'  =>$request->get('username'),
        ];
        //个推类型 1、书吧全员 ，3 个人
        if ($type == 1){
            $user = Auth::user();
            //根据书吧ID查找书吧查找到所有用户ID
            $groupUserId = ReGroupUserInfo::where('groupId', $user['group_id'])->pluck('userId');
            $clientid = UserInfo::query()->whereIn('userid', $groupUserId)->pluck('clientid');
            $data = array();
            for ($i=0; $i<count($clientid); $i++){
                if (!$clientid[$i] == ""){
                    $data[] = $clientid[$i];
                }
            }
            $push = $this->pushMessageToSingleBatch($pushData, $data);
        }else if ($type == 3){
            //判断clientid不等于空再发送
            if (empty($clientid)){
                return $this->sendError($clientid, 'clientid为空',501);
            }
            $push =$this->pushMessageToSingle($pushData,$clientid);
        }
        $user = Auth::user();
        //给数据添加其它信息，用于存储到数据库
        $pushData['news_type']  =$type == 3 ? 2 : 1;//消息类型1、公共、2私人
        $pushData['user_type']  =$type;//接收者类型:1、全员消息 2、吧主消息 3、私人
        $pushData['send_id']    =empty($user['poster_id']) ? 1 : $user['poster_id'];
        $pushData['user_id'] =$request->get('userid');//接收者id

        //判断消息是否推送成功 1、成功 2、失败
        if ($push['result'] == "ok"){
            $pushData['status'] = 1;
            $pushData = News::query()->create($pushData);
            return $this->sendResponse($pushData, '消息推送成功');
        }else{
            $pushData['status'] = 2;
            $pushData = News::query()->create($pushData);
            return $this->sendError($pushData, '消息推送失败');
        }
    }

    //单推接口案例
    function pushMessageToSingle($pushData,$clientid)
    {
        $igt = new \IGeTui(HOST,APPKEY,MASTERSECRET);

        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $template = $this->IGtNotificationTemplateDemo($pushData);


        //定义"SingleMessage"
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($clientid);
        //$target->set_alias(Alias);

        try {
            $rep = $igt->pushMessageToSingle($message, $target);
            return $rep;

        }catch(RequestException $e){
            $requstId =e.getRequestId();
            //失败时重发
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
            return $rep;
        }
    }

    //多推推送
    function pushMessageToSingleBatch($pushData,$clientid)
    {
        $igt = new \IGeTui(HOST, APPKEY, MASTERSECRET);
        $batch = new \IGtBatch(APPKEY, $igt);
        $batch->setApiUrl(HOST);

        $templateNoti = $this->IGtNotificationTemplateDemo($pushData);


        for ($i = 0; $i < count($clientid); $i++) {
            //个推信息体
            $messageNoti = new \IGtSingleMessage();
            $messageNoti->set_isOffline(true);//是否离线
            $messageNoti->set_offlineExpireTime(12 * 1000 * 3600);//离线时间
            $messageNoti->set_data($templateNoti);//设置推送消息类型
            //$messageNoti->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送

            $targetNoti = new \IGtTarget();
            $targetNoti->set_appId(APPID);
            $targetNoti->set_clientId($clientid[$i]);
            $batch->add($messageNoti, $targetNoti);
        }

        try {
            $rep = $batch->submit();
            return $rep;
        }catch(Exception $e){
            $rep=$batch->retry();
            return $rep;
        }
    }

    //群推接口案例
    function pushMessageToApp($pushData){
        $igt = new \IGeTui(HOST,APPKEY,MASTERSECRET);
        $template = $this->IGtNotificationTemplateDemo($pushData);
        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $appIdList=array(APPID);
        $message->set_appIdList($appIdList);
        $rep = $igt->pushMessageToApp($message,"任务组名");

        return $rep;
    }


    function IGtNotificationTemplateDemo($pushData)
    {
        $template = new \IGtNotificationTemplate();
        $template->set_appId(APPID);//应用appid
        $template->set_appkey(APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent("测试离线");//透传内容
        $template->set_title($pushData['title']);//通知栏标题
        $template->set_text($pushData['content']);//通知栏内容
        $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        return $template;
    }
}