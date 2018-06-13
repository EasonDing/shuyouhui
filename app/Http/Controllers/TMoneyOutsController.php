<?php

namespace App\Http\Controllers;

use App\Entities\Bar\User;
use App\Entities\Recharge;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TMoneyOutCreateRequest;
use App\Http\Requests\TMoneyOutUpdateRequest;
use App\Repositories\TMoneyOutRepository;
use App\Validators\TMoneyOutValidator;


class TMoneyOutsController extends Controller
{

    /**
     * @var TMoneyOutRepository
     */
    protected $repository;

    /**
     * @var TMoneyOutValidator
     */
    protected $validator;

//    public function __construct(TMoneyOutRepository $repository, TMoneyOutValidator $validator)
//    {
//        $this->repository = $repository;
//        $this->validator  = $validator;
//    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        //账户余额
        $moneyCount = User::where('userid', $user['poster_id'])->pluck('money');
        //已提现金额
        $out = $this->getOut($user['poster']);

        $data = [
            'moneyCount'    =>$moneyCount[0],
            'out'           =>$out
        ];

        return $this->sendResponse($data, '数据获取成功');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TMoneyOutCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TMoneyOutCreateRequest $request)
    {
        $user = Auth::user();
        try {
            $data = [
                'userId'    =>$user['poster'],
                'money'     =>$request->get('money'),
                'updateTime'=>Carbon::now(),
                'wechatName'=>$request->get('wechatName'),
            ];
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $tMoneyOut = $this->repository->create($data);

            $response = [
                'message' => 'TMoneyOut created.',
                'data'    => $tMoneyOut->toArray(),
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
        $tMoneyOut = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $tMoneyOut,
            ]);
        }

        return view('tMoneyOuts.show', compact('tMoneyOut'));
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

        $tMoneyOut = $this->repository->find($id);

        return view('tMoneyOuts.edit', compact('tMoneyOut'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  TMoneyOutUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(TMoneyOutUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $tMoneyOut = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'TMoneyOut updated.',
                'data'    => $tMoneyOut->toArray(),
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
                'message' => 'TMoneyOut deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'TMoneyOut deleted.');
    }

    //今日已提现
    public function getOut($userId){
        $t = time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
        //whereBetween 比较区间
        $startTime = date("Y-m-d H:i:s", $start);
        $endTime = date("Y-m-d H:i:s", $end);
        return Recharge::whereBetween('updateTime', [$startTime, $endTime])
            ->where('money_out', 1)
            ->where('money_out_status', 2)
            ->where('userId', $userId)
            ->sum('money');
    }
}
