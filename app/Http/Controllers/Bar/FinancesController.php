<?php

namespace App\Http\Controllers\Bar;

use App\Calendar;
use App\Criteria\DateBetweenCriteria;
use App\Entities\Bar\Order;
use App\Entities\Bar\User;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Presenters\Bar\FinancePresenter;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\FinanceCreateRequest;
use App\Http\Requests\Bar\FinanceUpdateRequest;
use App\Repositories\Bar\FinanceRepository;
use App\Validators\Bar\FinanceValidator;


class FinancesController extends Controller
{

    /**
     * @var FinanceRepository
     */
    protected $repository;

    /**
     * @var FinanceValidator
     */
    protected $validator;

    public function __construct(FinanceRepository $repository, FinanceValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request, 'updateTime'));
        $this->repository->setPresenter(FinancePresenter::class);
        $books = $this->repository->scopeQuery(function ($query){
            return $query->where('orderStatus', 3)->where('ownId', $this->userId());
        })->paginate(10);

        return $this->sendResponse($books, '账单详情列表获取成功!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FinanceCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(FinanceCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $finance = $this->repository->create($request->all());

            $response = [
                'message' => 'Finance created.',
                'data'    => $finance->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
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
        $finance = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $finance,
            ]);
        }

        return view('finances.show', compact('finance'));
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

        $finance = $this->repository->find($id);

        return view('finances.edit', compact('finance'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  FinanceUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(FinanceUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $finance = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Finance updated.',
                'data'    => $finance->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Finance deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Finance deleted.');
    }


    //获取今日交易、今日充值、账户余额信息
    public function count()
    {
        $date =  date('Y-m-d');
        $startTime = $date . ' 00:00:00';
        $endTime = $date . ' 23:59:59';
        $userId = $this->userId();

        //帐户余额
        $allMoney = User::where('userid', $userId)->sum('money');
        //今日收入
        $toDayIncome = Order::whereBetween('updateTime', [$startTime, $endTime])
            ->where('ownId', $userId)
            ->where('orderStatus', 3)//订单状态要等于3(已完成)
            ->sum('money');
        //总收入
        $totalIncome = Order::where('ownId', $userId)->where('orderStatus', 3)->sum('money');
        //今日支出
        $toDayPay = Order::whereBetween('updateTime', [$startTime, $endTime])->where('userId', $userId)->sum('money');
        //总支出
        $totalSpending = Order::where('userId', $userId)->sum('money');

        $data = ["allMoney"      => $allMoney, "toDayIncome" => $toDayIncome,
                 "totalIncome"   => $totalIncome, "toDayPay" => $toDayPay,
                 "totalSpending" => $totalSpending];

        return $this->sendResponse($data, '财务统计数据获取成功!');
    }


    //统计每日收入
    public function revenue()
    {
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01', $toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar) {
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $calendar->count = Order::whereBetween('updateTime', [$startTime, $endTime])
                ->where('ownId', $this->userId())
                ->where('orderStatus', 3)//订单状态要等于3(已完成)
                ->sum('money');
        }
        return $this->sendResponse($calendars, '获取数据成功');
    }

    //统计每日支出
    public function expenditure()
    {
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01', $toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar) {
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $calendar->count = Order::whereBetween('updateTime', [$startTime, $endTime])->where('userId', $this->userId())->sum('money');
        }
        return $this->sendResponse($calendars, '获取数据成功');
    }
}
