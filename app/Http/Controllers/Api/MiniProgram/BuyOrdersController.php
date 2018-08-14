<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Entities\BuyBook;
use App\Entities\BuyInvite;
use App\Entities\BuyOrder;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Presenters\Api\MiniProgram\BuyOrderPresenter;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BuyOrderCreateRequest;
use App\Http\Requests\BuyOrderUpdateRequest;
use App\Repositories\Api\MiniProgram\BuyOrderRepository;
use App\Validators\Api\MiniProgram\BuyOrderValidator;


class BuyOrdersController extends Controller
{

    /**
     * @var BuyOrderRepository
     */
    protected $repository;

    /**
     * @var BuyOrderValidator
     */
    protected $validator;

    public function __construct(BuyOrderRepository $repository, BuyOrderValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = Auth::user();
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->setPresenter(BuyOrderPresenter::class);
        $buyOrders = $this->repository->scopeQuery(function ($query) use ($user) {
            return $query->where('user_id', $user->user_id)->orderBy('created_at', 'desc');
        })->paginate(10);

        if ($buyOrders) {
            return $this->withCode(200)->withData($buyOrders)->response();
        }

        return $this->withCode(500)->withData('服务器错误！')->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BuyOrderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BuyOrderCreateRequest $request)
    {

        try {
            $user = Auth::user();
            $number = 'BK' . random_int(1, 9999999999999999);//订单号
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            DB::beginTransaction();

            $request->offsetSet('user_id', $user->user_id);
            $request->offsetSet('number', $number);
            $request->offsetSet('pay_status', 1);
            $request->offsetSet('pay_time', Carbon::now());
            $buyOrder = $this->repository->create($request->all());

            //有用户参与活动就将图书锁定，其它用户在24小时内不可参与
            $buyBook = BuyBook::query()->where('id', $request->book_id)->update([
                'activity_status' => 1
            ]);

            if ($buyOrder && $buyBook) {
                DB::commit();
                return $this->withCode(200)->withData($buyOrder)->response('订单创建成功！');
            }

            return $this->withCode(500)->withData($buyOrder)->response('订单创建失败！');

        } catch (ValidatorException $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            DB::rollback();
            return $this->withCode(500)->withData($errors)->response('服务器错误');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $buyOrder = $this->repository->with(['buyBook', 'buyInvite'])->find($id);

        $data = $buyOrder->toArray();
        //结束时间戳
        $endTime = strtotime($data['created_at']) + 24 * 60 * 60;
        //当前时间戳
        $currentTime = time();
        //如果时间戳小于结束时间戳
        if ($endTime > $currentTime) {
            $remainingTime = $endTime - $currentTime;
            $remainingTime = $this->time2string($remainingTime);
            $data['buy_book']['remaining_time'] = $remainingTime;
        } else {
            //默认为00:00:00
            $data['buy_book']['remaining_time'] = '00:00:01';
        }
        $data['buy_book']['created_at'] = substr($data['buy_book']['created_at'], 10, 10);
        $data['buy_book']['activity_price'] = substr($data['buy_book']['activity_price'], 0, strpos($data['buy_book']['activity_price'], '.'));
        $data['buy_book']['price'] = substr($data['buy_book']['price'], 0, strpos($data['buy_book']['price'], '.'));
        $data['invite_count'] = count($data['buy_invite']);
        $data['surplus_invite'] = $data['buy_book']['invite_total'] - count($data['buy_invite']);

        $data['buy_book']['image'] = asset('storage/' . $data['buy_book']['image']);
        $data['buy_book']['image_text'] = asset('storage/' . $data['buy_book']['image_text']);

        $userId = [];
        foreach ($data['buy_invite'] as $datum) {
            $userId[] = $datum['user_id'];
        }

        $appUser = User::query()->whereIn('userid', $userId)->get();
        $userLogos = [];
        foreach ($appUser as $item) {
            $userLogos[] = $item->UserLogo;
        }
        $data['buy_invite'] = $userLogos;

        return $this->withCode(200)->withData($data)->response();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $buyOrder = $this->repository->find($id);

        return view('buyOrders.edit', compact('buyOrder'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BuyOrderUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(BuyOrderUpdateRequest $request, $id)
    {

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $buyOrder = $this->repository->find($id);
            $endTime = $buyOrder->created_at->addDay()->toDateTimeString();
            $buyInviteCount = BuyInvite::query()->where('order_id', $id)->where('created_at', '<', $endTime)->count();

            //如果邀请总数 < 36 则活动任务失败
            DB::beginTransaction();
            if ($buyInviteCount < 36) {
                //将订单活动状态改为 1 未完成
                $buyOrderUpdate = $this->repository->update([
                    'activity_status' => 1,//活动状态：0.活动进行中|1.参与活动失败|2.参与活动成功
                ], $id);
                $buyBook = BuyBook::query()->where('id', $buyOrder->book_id)->update([
                    'activity_status' => 0,//活动状态：0.未锁定|1.锁定|2.该书已被用户领取
                ]);
            } else {
                //如果邀请总数 > 36 参与活动成功
                $buyOrderUpdate = $this->repository->update([
                    'activity_status' => 2,//活动状态：0.活动进行中|1.参与活动失败|2.参与活动成功
                ], $id);
                $buyBook = BuyBook::query()->where('id', $buyOrder->book_id)->update([
                    'activity_status' => 2,//活动状态：0.未锁定|1.锁定|2.该书已被用户领取
                ]);

            }

            if ($buyOrderUpdate && $buyBook) {
                DB::commit();
                return $this->withCode(200)->response('活动状态更新成功');
            }

            return $this->withCode(500)->withData([$buyOrderUpdate, $buyBook])->response('活动状态更新失败');


        } catch (ValidatorException $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            DB::rollback();
            return $this->withCode(500)->withData($errors)->response('服务器错误');
        }
    }


    /*
     * 删除订单
     * 解除0元购书图书状态
     */
    public function destroy(Request $request, $id)
    {

        $deleted = BuyOrder::query()->where('id', $id)->forceDelete();

        BuyBook::query()->where('id', $request->get('bookId'))->update([
            'activity_status'   => 0
        ]);

        return $this->withCode(200)->withData($deleted)->response('订单删除成功！');
    }

    //更新订单支付状态
    public function updatePayStatus($id)
    {
        $buyOrder = BuyOrder::query()->find($id)->update([
            'pay_time'  => Carbon::now(),
            'pay_status'  => 1
        ]);

        if ($buyOrder) {
            return $this->withCode(200)->withData($buyOrder)->response('支付状态更新成功！');
        } else {
            return $this->withCode(500)->withData($buyOrder)->response('支付状态更新失败！');
        }
    }

    /**
     * 计算倒计时
     * @param $second 时间戳相减 剩余的时间戳
     * @return string
     */
    function time2string($second)
    {
        $day = floor($second / (3600 * 24));
        $second = $second % (3600 * 24);//除去整天之后剩余的时间
        $hour = floor($second / 3600);
        $second = $second % 3600;//除去整小时之后剩余的时间
        $minute = floor($second / 60);
        $second = $second % 60;//除去整分钟之后剩余的时间

        $hour = $hour < 10 ? '0' . $hour : $hour;
        $minute = $minute < 10 ? '0' . $minute : $minute;
        $second = $second < 10 ? '0' . $second : $second;
        //返回字符串
//        return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
        return $hour . ":" . $minute . ":" . $second;
    }
}
