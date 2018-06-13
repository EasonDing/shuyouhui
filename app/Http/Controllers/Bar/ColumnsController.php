<?php

namespace App\Http\Controllers\Bar;

use App\Entities\Bar\Column;
use App\Http\Controllers\Controller;
use App\Presenters\Bar\ColumnPresenter;

use App\Repositories\Bar\ColumnCommentRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\ColumnCreateRequest;
use App\Http\Requests\Bar\ColumnUpdateRequest;
use App\Repositories\Bar\ColumnRepository;
use App\Validators\Bar\ColumnValidator;


class ColumnsController extends Controller
{

    //专栏图片存储路径
    const IMAGE_PATH = 'columns';

    /**
     * @var ColumnRepository
     */
    protected $repository;

    protected $columnCommentRepository;

    /**
     * @var ColumnValidator
     */
    protected $validator;

    public function __construct(ColumnRepository $repository, ColumnCommentRepository $columnCommentRepository, ColumnValidator $validator)
    {
        $this->repository = $repository;
        $this->columnCommentRepository = $columnCommentRepository;
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
        $this->repository->setPresenter(ColumnPresenter::class);
        $columns = $this->repository->scopeQuery(function ($query){
            return $query->where('group_id', $this->groupId())->orderBy('created_at', 'desc');
        })->paginate(10);;

        return $this->sendResponse($columns, '专栏列表获取成功');
    }

    /**
     * 添加专栏
     *
     * @param  ColumnCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ColumnCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            //存储图片
            $image = $request->file('file');
            $path = $this->saveImage(self::IMAGE_PATH, $image);
            //往 $request 中追回数据
            $request->offsetSet('image', $path);
            $request->offsetSet('group_id', $this->groupId());

            $column = $this->repository->create($request->all());

            return $this->sendResponse($column, '专栏添加成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, '专栏添加失败!');
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
        $column = $this->repository->find($id);

        if ($column){
            return $this->sendResponse($column, '专栏详情获取成功!');
        }

        return $this->sendError(false, '专栏详情获取失败!');
    }


    /**
     * 编辑专栏
     *
     * @param  int $columnId
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($columnId)
    {
        $column = $this->repository->find($columnId);

        if ($column){
            return $this->sendResponse($column, '专栏详情获取成功!');
        }

        return $this->sendError($column, '专栏详情获取失败!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  ColumnUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(ColumnUpdateRequest $request, $id)
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            //存储图片
            if (!empty($request->file())){
                $image = $request->file('file');
                $path = $this->saveImage(self::IMAGE_PATH, $image);

                //往 $request 中追回数据
                $request->offsetSet('image', $path);
            } else {
                $request->offsetUnset('image');
            }

            $column = $this->repository->update($request->all(), $id);


            return $this->sendResponse($column, '专栏更新成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, '专栏更新失败!');
        }
    }


    /**
     * 删除专栏
     *
     * @param  int $columnId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($columnId)
    {
        $deleted = $this->repository->delete($columnId);

        if ($deleted) {
            return $this->sendResponse($deleted, '专栏删除成功!');
        }

        return $this->sendError($deleted, '专栏删除失败!');
    }
}
