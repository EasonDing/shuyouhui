<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Admin\Group;
use App\Entities\Admin\Message;
use App\Entities\Admin\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Admin\MessageCreateRequest;
use App\Http\Requests\Admin\MessageUpdateRequest;
use App\Repositories\Admin\MessageRepository;
use App\Validators\Admin\MessageValidator;


class MessagesController extends Controller
{

    /**
     * @var MessageRepository
     */
    protected $repository;

    /**
     * @var MessageValidator
     */
    protected $validator;

    public function __construct(MessageRepository $repository, MessageValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $messages = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $messages,
            ]);
        }

        return view('messages.index', compact('messages'));
    }

    /**
     * 发送个人消息
     *
     * @param  MessageCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(MessageCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            //追加数据
            $request->offsetSet('updateTime', Carbon::now());
            $request->offsetSet('chatType', 1);
            $request->offsetSet('ChatResult', 0);
            $request->offsetSet('userId', 'admin');
            $request->offsetSet('otherId', $request->get('id'));
            $request->offsetSet('url', '');
            $request->offsetSet('isbn', '');
            $request->offsetSet('ChatResult_bk', 0);
            $request->offsetSet('time', '');
            $request->offsetSet('location', '');
            $request->offsetSet('recontent', '');
            $request->offsetSet('repjson', '');

            $message = $this->repository->create($request->all());

            if ($message){
                return $this->sendResponse($message, '消息发送成功!');
            }

            return $this->sendError($message, '消息发送失败!');

        } catch (\Exception $e) {

            return $this->sendError(false, '服务器错误！');
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
        $message = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $message,
            ]);
        }

        return view('messages.show', compact('message'));
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

        $message = $this->repository->find($id);

        return view('messages.edit', compact('message'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  MessageUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(MessageUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $message = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Message updated.',
                'data'    => $message->toArray(),
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
                'message' => 'Message deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Message deleted.');
    }


    /*
     * 给所有用户发送消息
     *
     * @param title 消息标题
     * @param content 消息内容
     *
     */
    public function allUser(Request $request)
    {
        //验证逻辑
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ],[
            'required'  => ':attribute 不能为空',
        ],[
            'title'     => '消息标题',
            'content'     => '消息内容',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return $this->sendError(false, $message,422);
        }

        //获取所有成员 ID
        $userIds = User::query()->pluck('userid');

        $data = [];
        foreach ($userIds as $userId) {
            //追加数据
            $data[] = [
                'title' => $request->get('title'),
                'content' => $request->get('content'),
                'updateTime' => Carbon::now(),
                'chatType' => 1,
                'ChatResult' => 0,
                'userId' => 'admin',
                'otherId' => $userId,
                'url' => '',
                'isbn' => '',
                'ChatResult_bk' => 0,
                'time' => '',
                'location' => '',
                'recontent' => '',
                'repjson' => '',
            ];
        }

        try {
            //将数据插分200条一批插入
            $messages = collect($data)->chunk(200)->toArray();
            DB::beginTransaction();
            foreach ($messages as $message) {

                $created = Message::query()->insert($message);
            }
            DB::commit();
            return $this->sendResponse($created, '全员消息发送成功!');

        } catch (\Exception $e) {

            DB::rollback();
            return $this->sendError($created, '全员消息发送失败!');
        }

    }


    /*
     * 给所有吧主发送消息
     *
     * @param title 消息标题
     * @param content 消息内容
     * @param type 是否管理员 1管理员 0成员
     * @param auditStatus 3通过验证的书吧
     *
     */
    public function allBar(Request $request)
    {
        //验证逻辑
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ],[
            'required'  => ':attribute 不能为空',
        ],[
            'title'     => '消息标题',
            'content'     => '消息内容',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return $this->sendError(false, $message,422);
        }

        //获取所有成员 ID
        $groups = Group::query()->with(['users' => function($query){
            return $query->where('type', 1);
        }])->where('auditStatus', 3)->get();

        $data = [];
        foreach ($groups as $group){
            //判断书吧是否存在吧主
            if (!$group->users->isEmpty()){
                $user = $group->users->first();
                //追加数据
                $data[] = [
                    'title' => $request->get('title'),
                    'content' => $request->get('content'),
                    'updateTime' => Carbon::now(),
                    'chatType' => 1,
                    'ChatResult' => 0,
                    'userId' => 'admin',
                    'otherId' => $user->userid,
                    'url' => '',
                    'isbn' => '',
                    'ChatResult_bk' => 0,
                    'time' => '',
                    'location' => '',
                    'recontent' => '',
                    'repjson' => '',
                ];
            }
        }

        try {
            //将数据插分200条一批插入
            $messages = collect($data)->chunk(200)->toArray();
            DB::beginTransaction();
            foreach ($messages as $message) {

                $created = Message::query()->insert($message);
            }
            DB::commit();
            return $this->sendResponse($created, '吧主消息发送成功!');

        } catch (\Exception $e) {

            DB::rollback();
            return $this->sendError($created, '吧主消息发送失败!');
        }
    }
}
