<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\DateBetweenCriteria;
use App\Http\Controllers\Controller;
use App\Presenters\Admin\BookPresenter;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use App\Http\Requests\Admin\BookCreateRequest;
use App\Http\Requests\Admin\BookUpdateRequest;
use App\Repositories\Admin\BookRepository;
use App\Validators\Admin\BookValidator;


class BooksController extends Controller
{
    //贝壳图书图片存储目录
    const IMAGE_PATH = 'adminBook/';

    /**
     * @var BookRepository
     */
    protected $repository;

    /**
     * @var BookValidator
     */
    protected $validator;

    public function __construct(BookRepository $repository, BookValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * 查询贝壳图书列表
     *
     * @param Request $request
     * @param type  0 贝壳图书
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request));
        $this->repository->setPresenter(BookPresenter::class);
        $books = $this->repository->scopeQuery(function ($query){
            return $query->where('type', 0);
        })->paginate(10);

        return $this->sendResponse($books, '贝壳图书获取成功!');
    }

    /**
     * 添加贝壳图书
     *
     * @param  BookCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BookCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $book = $this->repository->findWhere(['isbn' => $request->get('isbn')])->first();

            if (!empty($book)){
                return $this->sendError($book, '该图书已存在,现阶段暂时无法添加!');
            }

            //存储图片
            $image = $request->file('file');
            $path = $this->saveImage(self::IMAGE_PATH, $image);
            //往 $request 中追加数据
            $request->offsetSet('image', $path);
            $request->offsetSet('cate', 0);//暂时无用
            $request->offsetSet('type', 0);//0.管理员上传、1、吧主上传
            $request->offsetSet('updateTime', Carbon::now());//无用、为了查询方便添加

            $book = $this->repository->create($request->all());

            return $this->sendResponse($book, '贝壳图书添加成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, '贝壳图书添加失败!');
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
        $book = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $book,
            ]);
        }

        return view('books.show', compact('book'));
    }


    /**
     * 编辑贝壳图书
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $book = $this->repository->find($id);

        if ($book){
            return $this->sendResponse($book, '贝壳图书获取成功！');
        }

        return $this->sendError($book, '贝壳图书获取失败!');
    }


    /**
     * 更新贝壳图书
     *
     * @param  BookUpdateRequest $request
     * @param  string            $bookId
     *
     * @return Response
     */
    public function update(BookUpdateRequest $request, $bookId)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            //存储图片
            if (!empty($request->file())){
                $image = $request->file('file');
                $path = $this->saveImage(self::IMAGE_PATH, $image);
                //往 $request 中追加数据
                $request->offsetSet('image', $path);
            } else {
                $request->offsetUnset('image');
            }

            $book = $this->repository->update($request->all(), $bookId);


            return $this->sendResponse($book, '贝壳图书更新成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, '贝壳图书更新失败!');
        }
    }


    /**
     * 删除贝壳图书
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if ($deleted){
            return $this->sendResponse($deleted, '贝壳图书删除成功!');
        }

        return $this->sendError($deleted, '贝壳图书删除失败!');
    }
}
