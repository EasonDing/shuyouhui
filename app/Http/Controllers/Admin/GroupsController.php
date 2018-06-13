<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateBetweenCriteria;
use App\Entities\Admin\AdminUser;
use App\Entities\Admin\GroupUserRelation;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Presenters\Admin\GroupPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Admin\GroupCreateRequest;
use App\Http\Requests\Admin\GroupUpdateRequest;
use App\Repositories\Admin\GroupRepository;
use App\Validators\Admin\GroupValidator;


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
     * auditStatus 3 通过审核的书吧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request, 'updateTime'));
        $this->repository->setPresenter(GroupPresenter::class);
        $books = $this->repository->scopeQuery(function ($query){
            return $query->where('auditStatus', 3);
        })->paginate(10);

        return $this->sendResponse($books, '书吧列表获取成功!');
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
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $group,
            ]);
        }

        return view('groups.show', compact('group'));
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
     * @param  string            $id
     *
     * @return Response
     */
    public function update(GroupUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $group = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Group updated.',
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $deleted = $this->repository->delete($id);
            GroupUserRelation::where('groupId', $id)->delete();
            AdminUser::where('group_id', $id)->delete();

            DB::commit();
            return $this->sendResponse($deleted, '书吧删除成功');
        } catch (\Exception $e) {

            DB::rollback();
            return $this->sendError(false, '书吧删除失败');
        }
    }


    /**
     * 无管理员账号的书吧列表
     *
     * @param auditStatus 3 通过审核的书吧
     *
     * @return \Illuminate\Http\Response
     */
    public function groups()
    {
        $groupId = $this->repository->findWhere(['auditStatus' => 3])->pluck('groupId');

        $userGroupId = AdminUser::pluck('group_id');

        $oldGroupId = array_diff(json_decode($groupId), json_decode($userGroupId));

        $groups = $this->repository->orderBy('updateTime', 'desc')->findWhereIn('groupId', $oldGroupId, ['groupId', 'groupName']);

        if ($groups){
            return $this->sendResponse($groups, '无管理员书吧列表获取成功!');
        }

        return $this->sendError($groups, '无管理员书吧列表获取失败!');
    }
}
