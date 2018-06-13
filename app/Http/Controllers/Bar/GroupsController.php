<?php

namespace App\Http\Controllers\Bar;

use App\Entities\Bar\Group;
use App\Http\Controllers\Controller;
use App\Presenters\Bar\GroupUserPresenter;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\GroupCreateRequest;
use App\Http\Requests\Bar\GroupUpdateRequest;
use App\Repositories\Bar\GroupRepository;
use App\Validators\Bar\GroupValidator;


class GroupsController extends Controller
{

    /**
     * @var GroupRepository
     */
    protected $repository;

    /**
     * @var GroupValidator
     */
    protected $validator;

    public function __construct(GroupRepository $repository, GroupValidator $validator)
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
        $groups = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $groups,
            ]);
        }

        return view('groups.index', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  GroupCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(GroupCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $group = $this->repository->create($request->all());

            $response = [
                'message' => 'Group created.',
                'data'    => $group->toArray(),
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
     * 书吧详情
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        $group = $this->repository->find($this->groupId());

        return $this->sendResponse($group, '书吧详情获取成功');
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

        $group = $this->repository->find($id);

        return view('groups.edit', compact('group'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  GroupUpdateRequest $request
     *
     * @return Response
     */
    public function update(GroupUpdateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $request->offsetSet('Latitude', '');
            $request->offsetSet('Longitude', '');
            $group = $this->repository->update($request->all(), $this->groupId());

            return $this->sendResponse($group, '书吧信息更新成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, '书吧信息更新失败!');
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
                'message' => 'Group deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Group deleted.');
    }


    /*
     * 获取书吧用户列表
     */
    public function users()
    {
        $user = Auth::user();
        $this->repository->setPresenter(GroupUserPresenter::class);
        //关联用户表在 Transformers 中调用
        $groupUsers = $this->repository->find($user['group_id']);

        return $this->sendResponse($groupUsers, '书吧成员列表获取成功');
    }


    /*
     * 删除书吧成员
     */
    public function destroyUser($id)
    {
        try {
            $user = Auth::user();
            $group = Group::query()->find($user['group_id']);
            $deleted = $group->users()->detach($id);

            return $this->sendResponse($deleted, '用户删除成功');
        } catch (\Exception $e) {

            return $this->sendError(false, '用户删除失败');
        }
    }
}
