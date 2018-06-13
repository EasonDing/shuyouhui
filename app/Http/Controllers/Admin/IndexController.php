<?php

namespace App\Http\Controllers\Admin;

use App\Calendar;
use App\Entities\Admin\Finance;
use App\Entities\Admin\Group;
use App\Entities\Admin\Order;
use App\Entities\Admin\User;
use App\Http\Controllers\Controller;
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
        $date =  date('Y-m-d');
        $startTime = $date . ' 00:00:00';
        $endTime = $date . ' 23:59:59';

        $userCount = User::count();//所有用户
        $groupCount = Group::where('auditStatus', 3)->count();//所有书吧
        $accountAmount = User::all()->sum('money');

        //今日注册书吧
        $newGroupCount = Group::whereBetween('updateTime', [$startTime, $endTime])->count();
        //今日新增用户总数
        $newUserCount = User::whereBetween('updateTime', [$startTime, $endTime])->count();
        //今日交易金额
        $toDayIncome = Order::whereBetween('updateTime', [$startTime, $endTime])->sum('money');
        //今日充值金额
        $toDayRecharge = Finance::whereBetween('updateTime', [$startTime, $endTime])->where('status', 1)->sum('money');

        $data = ['newUserCount'  => $newUserCount, 'userCount' => $userCount,
                 'newGroupCount' => $newGroupCount, 'groupCount' => $groupCount,
                 'toDayIncome'   => $toDayIncome, 'toDayRecharge' => $toDayRecharge,
                 'accountAmount' => $accountAmount];

        return $this->sendResponse($data, '首页统计数据获取成功');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    //统计今日新增书吧
    public function newGroup(){
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01',$toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar){
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $calendar->count = Group::whereBetween('updateTime', [$startTime, $endTime])->count();
        }

        return $this->sendResponse($calendars, '获取数据成功');
    }

    //统计今日新增用户
    public function newUser(){
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01',$toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar){
          $startTime = $calendar->updateTime . " 00:00:00";
          $endTime = $calendar->updateTime . " 23:59:59";
          $calendar->count = User::whereBetween('updateTime', [$startTime, $endTime])->count();
        }

        return $this->sendResponse($calendars, '获取数据成功');
    }
}
