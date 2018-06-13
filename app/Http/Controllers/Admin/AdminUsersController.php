<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateBetweenCriteria;
use App\Entities\Admin\Group;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use App\Presenters\Admin\AdminUserPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Admin\AdminUserCreateRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Repositories\Admin\AdminUserRepository;
use App\Validators\Admin\AdminUserValidator;


class AdminUsersController extends Controller
{

    /**
     * @var AdminUserRepository
     */
    protected $repository;

    /**
     * @var AdminUserValidator
     */
    protected $validator;

    public function __construct(AdminUserRepository $repository, AdminUserValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @param type 1 管理员 2 书吧管理员
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request));
        $this->repository->setPresenter(AdminUserPresenter::class);
        $books = $this->repository->scopeQuery(function ($query){
            return $query->where('type', 2);
        })->paginate(10);

        return $this->sendResponse($books, '书吧管理员列表获取成功!');
    }

    /**
     * 添加管理员账号
     *
     * @param  AdminUserCreateRequest $request
     * @param  type 0成员 1吧主
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            //查询书吧及吧主相关信息
            $group = Group::query()->with(['users' => function ($query) {
                return $query->where('type', 1);
            }])->find($request->get('group_id'));

            if ($group->users->isEmpty()) {
                return $this->sendError($group, $group->groupName . ' 吧主跑路啦!');
            }

            //将数组转成对象
            $user = collect($group->users)->first();
            //追加数据
            $request->offsetSet('password', bcrypt($request->get('password')));
            $request->offsetSet('poster_id', $group->poster);
            $request->offsetSet('group_name', $group->groupName);
            $request->offsetSet('mobile', $user->bindPhone);
            $request->offsetSet('name', $user->username);
            $request->offsetSet('type', 2);
            $request->offsetSet('face', $user->UserLogo);

            DB::beginTransaction();
            $user = $this->repository->create($request->all());

            //TODO 改成模型方式
            DB::table('role_user')->insert([
                'role_id' => 2,
                'user_id' => $user['id']
            ]);
            DB::commit();

            
            return $this->sendResponse($user, '吧主账号创建成功!');
        } catch (\Exception $e) {

            DB::rollback();
            return $this->sendError(false, '服务器错误啦!');
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
        $adminUser = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $adminUser,
            ]);
        }

        return view('adminUsers.show', compact('adminUser'));
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

        $adminUser = $this->repository->find($id);

        return view('adminUsers.edit', compact('adminUser'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  AdminUserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(AdminUserUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $adminUser = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'AdminUser updated.',
                'data'    => $adminUser->toArray(),
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

        if ($deleted) {
            return $this->sendResponse($deleted, '吧主账号删除成功!');
        } else {
            return $this->sendError($deleted, '吧主账号删除失败!');
        }
    }


    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        if (password_verify($request->get('oldPassword'), $request->user()->password)){
            $this->repository->update([
                'password'  => bcrypt($request->get('checkPassword'))
            ],$request->user()->id);

            return $this->sendResponse(true, '密码修改成功!');
        }else{
            return $this->sendError(false,'原密码不正确');
        }
    }

    /**
     * 获取当前登录的用户信息
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        return $this->sendResponse($request->user(), '用户信息获取成功!');
    }
}
