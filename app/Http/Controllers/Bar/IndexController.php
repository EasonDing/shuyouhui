<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Calendar;
use App\Entities\Bar\Group;
use App\Entities\Bar\GroupUserRelation;
use App\Entities\Bar\Order;
use App\Entities\Bar\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        $date = date('Y-m-d');
        $startTime = $date . ' 00:00:00';
        $endTime = $date . ' 23:59:59';

        $userId = $this->userId();
        $groupId = $this->groupId();

        $allMoney = User::where('userid', $userId)->sum('money');//帐户余额
        $groupName = Group::where('groupId', $groupId)->pluck('groupName');//书吧名称
        //今日新增用户
        $NewUsers = GroupUserRelation::whereBetween('updateTime', [$startTime, $endTime])->where('groupId', $groupId)->count();
        //今日活跃用户
        $activeUsers = User::whereBetween('LastLoginTime', [$startTime, $endTime])->count();
        //今日收入
        $toDayMoney = Order::whereBetween('updateTime', [$startTime, $endTime])->where('ownId', $userId)->where('orderStatus', 3)->sum('money');
        //今日新增订单
        $newOrders = Order::whereBetween('updateTime', [$startTime, $endTime])->where('ownId', $userId)->count();
        $data = [
            "allMoney"   => $allMoney, "groupName" => $groupName[0],
            "newUsers"   => $NewUsers, 'activeUsers' => $activeUsers,
            "toDayMoney" => $toDayMoney, "newOrders" => $newOrders];

        return $this->sendResponse($data, '首页统计数据获取成功');
    }

    //统计每日新增用户
    public function newUser()
    {
        $groupId = $this->groupId();
        $toDay = date("Y-m-d", time());

        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01', $toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar) {
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $usersId = GroupUserRelation::whereBetween('updateTime', [$startTime, $endTime])->where('groupId', $groupId)->pluck('userId');
            $calendar->count = count($usersId);
            $sexs = User::whereIn('userid', $usersId)->pluck('sex');
            foreach ($sexs as $sex){
                if ($sex == 1){
                    $calendar->man += 1;
                }elseif($sex == 2){
                    $calendar->woman += 1;
                }else{
                    $calendar->man += 1;
                }
            }
        }

        return $this->sendResponse($calendars, '每日新增用户');
    }

    //统计每日新增订单
    public function newOrder(){
        $userId = $this->userId();
        $today = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01', $today])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar) {
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            // TODO 此处可能还需加入 图书线下流转的订单
            $calendar->count = Order::whereBetween('updateTime', [$startTime, $endTime])->where('ownId', $userId)->count();
        }

        return $this->sendResponse($calendars, '每日新增获取数据成功');
    }

    //今日活跃用户列表
    public function activeUser(Request $request){
        $date = date('Y-m-d');
        $startTime = $date . ' 00:00:00';
        $endTime = $date . ' 23:59:59';

        $input = $request->get('input');//搜索框值：手机号或用户名称
        $sex = $request->get('sex');//性别
        //书吧今日活跃用户ID
        $userId = GroupUserRelation::where('groupId', $this->groupId())->pluck('userId');
        $query = User::query();

        if ($sex > 0){
            $query->where('sex', $sex);
        }
        if (!empty($input) || $input > 0){
            $query->where('username', $input)
                ->orWhere('bindPhone',  $input);
        }

        $users = $query->whereBetween('LastLoginTime', [$startTime, $endTime])->whereIn('userid', $userId)->paginate(10);

        foreach ($users as $user) {
            if (!empty($user['Birthday'])){
                $birthday = date('Y', time()) - substr($user['Birthday'], 0, 4);
                $user['Birthday'] = $birthday;
            }
        }

        return $this->sendResponse($users, '今日活跃用户获取成功!');
    }
}
