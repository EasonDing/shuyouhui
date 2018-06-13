<?php

namespace App\Http\Controllers\Bar;

use App\Calendar;
use App\Criteria\DateBetweenCriteria;
use App\Entities\Bar\Order;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use App\Presenters\Bar\OrderPresenter;
use App\Validators\Bar\OrderValidator;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\OrderCreateRequest;
use App\Http\Requests\Bar\OrderUpdateRequest;
use App\Repositories\Bar\OrderRepository;

class OrdersController extends Controller
{

    /**
     * @var OrderRepository
     */
    protected $repository;

    /**
     * @var OrderValidator
     */
    protected $validator;

    public function __construct(OrderRepository $repository, OrderValidator $validator)
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
        $this->repository->setPresenter(OrderPresenter::class);
        // TODO 加了条件后前端搜索不准确
        $orders = $this->repository->scopeQuery(function ($query){
            return $query->where('ownId', $this->userId())->orWhere('userId', $this->userId());
        })->paginate(10);

        return $this->sendResponse($orders, '订单列表获取成功!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  OrderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OrderCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $order = $this->repository->create($request->all());

            $response = [
                'message' => 'Order created.',
                'data'    => $order->toArray(),
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
        $order = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $order,
            ]);
        }

        return view('orders.show', compact('order'));
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

        $order = $this->repository->find($id);

        return view('orders.edit', compact('order'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  OrderUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(OrderUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $order = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Order updated.',
                'data'    => $order->toArray(),
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

        if ($deleted){
            return $this->sendResponse($deleted, '订单删除成功!');
        }

        return $this->sendError($deleted, '订单删除失败!');
    }


    //统计每日新增订单
    public function newOrder()
    {
        $today = date("Y-m-d", time());
        $calendars = Calendar::whereBetween('updateTime', ['2016-01-01', $today])
            ->orderBy('updateTime', 'desc')
            ->paginate(10);

        foreach ($calendars as $calendar) {
            $startTime = $calendar->updateTime . " 00:00:00";
            $endTime = $calendar->updateTime . " 23:59:59";
            // TODO 此处可能还需加入 图书线下流转的订单
            $calendar->count = Order::whereBetween('updateTime', [$startTime, $endTime])->where('ownId', $this->userId())->count();
        }
        return $this->sendResponse($calendars, '每日新增订单获取成功!');
    }
}
