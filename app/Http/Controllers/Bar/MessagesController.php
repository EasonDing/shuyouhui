<?php

namespace App\Http\Controllers\Bar;

use App\Entities\Bar\Group;
use App\Entities\Bar\GroupUserRelation;
use App\Entities\Bar\Message;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\MessageCreateRequest;
use App\Http\Requests\Bar\MessageUpdateRequest;
use App\Repositories\Bar\MessageRepository;
use App\Validators\Bar\MessageValidator;


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
     * 发送书吧个人消息
     *
     * @param  MessageCreateRequest $request
     * @param chatType 消息类型1系统消息，2借阅消息
     * @param ChatResult 0未读，1已读，2已删除
     * @param userId 发送者
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
            $request->offsetSet('userId', $this->userId());
            $request->offsetSet('otherId', $request->get('userid'));
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



    public function allUser(Request $request)
    {
        //TODO 未添加验证
        $groupUsers = Group::query()->where('groupId', $this->groupId())->with('users')->first();

        $data = [];
        foreach ($groupUsers->users as $groupUser) {
            //发送全员消息排除自己
            if ($groupUser->pivot->userId != $this->userId()) {
                //追加数据
                $data[] = [
                    'title' => $request->get('title'),
                    'content' => $request->get('content'),
                    'updateTime' => Carbon::now(),
                    'chatType' => 1,
                    'ChatResult' => 0,
                    'userId' => $this->userId(),
                    'otherId' => $groupUser->pivot->userId,
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

        $created = Message::query()->insert($data);

        if ($created){

            return $this->sendResponse($created, '消息发送成功!');
        }

        return $this->sendError($created, '消息发送失败!');
    }
}
