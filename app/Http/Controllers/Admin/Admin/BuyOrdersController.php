<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Entities\BuyOrder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class BuyOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = BuyOrder::query();

        $lists = $query->where('activity_status', 2)
            ->with(['buyUser', 'buyBook'])
            ->paginate(10);

        foreach ($lists as $list) {
            $list['address'] = $list->area . $list->address;
        }

        if ($lists) {
            return $this->withCode(200)->withData($lists)->response('获取中奖名单列表成功！');
        }
        return $this->withCode(500)->withData($lists)->response('获取中奖名单列表失败！');

    }
}
