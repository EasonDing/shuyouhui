<?php

namespace App\Http\Controllers\Admin;

use App\Calendar;
use App\Criteria\DateBetweenCriteria;
use App\Entities\Admin\Finance;
use App\Entities\Admin\Order;
use App\Entities\Admin\User;
use App\Entities\Recharge;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use App\Presenters\Admin\FinancePresenter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Admin\FinanceCreateRequest;
use App\Http\Requests\Admin\FinanceUpdateRequest;
use App\Repositories\Admin\FinanceRepository;
use App\Validators\Admin\FinanceValidator;


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
     * @param status 0 未支付 1支付成功 2支付失败
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request, 'updateTime'));
        $this->repository->setPresenter(FinancePresenter::class);
        $books = $this->repository->scopeQuery(function ($query){
            return $query->where('status', 1);
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


    /**
     * 财务统计数据
     * 今日交易、今日充值、账户余额信息
     *
     * @param money_out ==null 是充值 1为提现
     */
    public function statistics()
    {
        $date =  date('Y-m-d');
        $startTime = $date . ' 00:00:00';
        $endTime = $date . ' 23:59:59';

        //今日交易金额
        $toDayIncome = Order::whereBetween('updateTime', [$startTime, $endTime])->sum('money');
        //今日充值金额
        $toDayRecharge = Finance::whereBetween('updateTime', [$startTime, $endTime])
            ->where('status', 1)
            ->where('money_out', null)
            ->sum('money');
        $accountAmount = User::all()->sum('money');//账户余额

        $data = array('toDayIncome'=>$toDayIncome, 'toDayRecharge'=>$toDayRecharge, 'accountAmount'=>$accountAmount);

        return $this->sendResponse($data, '财务统计数据获取成功');
    }


    /**
     * 统计每日交易金额
     */
    public function rechargeCount()
    {
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01',$toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar){
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $calendar->count = Finance::whereBetween('updateTime', [$startTime, $endTime])->where('status', 1)->sum('money');
        }

        return $this->sendResponse($calendars, '每日交易金额统计');
    }


    /*
     * 统计今日订单交易金额
     *
     */
    public function tradingCount(){
        $toDay = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01',$toDay])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar){
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            $calendar->count = Order::whereBetween('updateTime', [$startTime, $endTime])->sum('money');
        }

        return $this->sendResponse($calendars, '获取数据成功');
    }
}
