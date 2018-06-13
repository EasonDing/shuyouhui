<?php

namespace App\Http\Controllers\Bar;

use App\Entities\Bar\Book;
use App\Entities\Bar\BookCode;
use App\Entities\Bar\CollectBook;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\BookCreateRequest;
use App\Http\Requests\Bar\BookUpdateRequest;
use App\Repositories\Bar\BookRepository;
use App\Validators\Bar\BookValidator;


class BooksController extends Controller
{

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
     * 书吧书架列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
//        $books = $this->repository->all();

        //TODO 待修改
        $input = $request->get('input');//搜索框值：书名称
        $startTime = $request->get('startTime');//开始日期
        $endTime = $request->get('endTime');//结束日期

        $isbn = CollectBook::query()->orderBy('created_at', 'desc')
            ->where('collectId', $this->userId())
            ->where('inflow', 1)//0流出、1流入
            ->where('Type', 2)//类型(1.APP用户 2.书友群,3.书店)
            ->where('inType', 3)//流入类型 0未知 1自己流入 2他人书架流入3贝壳图书流入
            ->pluck('isbn');

        $query = Book::query();
        //搜索框条件
        if (!empty($input) || $input > 0) {
            $query->where('title', 'like', "%{$input}%");
        }
        //时间区间
        if ($startTime > 0 && $endTime > 0) {
            $startTime = date('Y-m-d H:i:s', $startTime);
            $endTime = date('Y-m-d H:i:s', $endTime);
            $query->whereBetween('created_at', [$startTime, $endTime]);
        }

        $books = $query->whereIn('isbn', $isbn)//0.管理员上传、1、吧主上传/
        ->paginate(5);
        //推荐语从绑定的表中获取
        foreach ($books as $book){
            $summary = CollectBook::where('ISBN', $book->isbn)->pluck('summary');
            $book->summary = $summary[0];

        }
        return $this->sendResponse($books, '入库贝壳图书列表获取成功!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BookCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BookCreateRequest $request)
    {

//        try {
//
//            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
//
//            $book = $this->repository->create($request->all());
//
//        } catch (ValidatorException $e) {
//
//        }

        $isbn = $request->get('isbn');
        $summary = $request->get('summary');
        $type = 2;//类型(1.APP用户 2.书友群,3.书店)
        $collectId = $this->userId();//入库操作者
        $updateTime = Carbon::now();
        $inflow = 1;//流入
        $inType = 3;//从贝壳图书流入
        $address = '';
        $name   = '';
        $tel    = '';
        $code   ="http://m.bookfan.cn/book/" . $this->random2(28);

        //判断推荐语是否为空！如果为空就使用默认的
        if (empty($summary)) {
            $book = Book::query()->where('isbn', $isbn)->first();
            $summary = $book['summary'];
        }
        //查询该用户和此书是否以绑定 等于空再添加
        $query = CollectBook::query()->where('collectId', $collectId)->where('ISBN', $isbn)->where('inflow', 1)->first();
        //为空
        try {
            if (empty($query)) {
                DB::beginTransaction();

                $code = BookCode::create([
                    'code'      =>$code,
                    'isbn'      =>$isbn,
                    'flowPrice' =>0,
                    'recommend' =>'',
                ]);
                $collectBook = CollectBook::create([
                    'codeid'        =>$code['id'],
                    'ISBN'          =>$isbn,
                    'summary'       =>$summary,
                    'type'          =>$type,
                    'collectId'     =>$collectId,
                    'updateTime'    =>$updateTime,
                    'inflow'        =>$inflow,
                    'inType'        =>$inType,
                    'address'       =>$address,
                    'name'          =>$name,
                    'tel'           =>$tel,
                ]);
                if ($collectBook && $code){
                    DB::commit();
                    return $this->sendResponse($collectBook, '添加入库成功');
                }else{
                    return $this->sendError($collectBook, '添加入库失败');
                }
            } else {
                return $this->sendError($query, '图书已存在', 501);
            }
        } catch (\Exception $e) {

            DB::rollback();
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
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

//        $book = $this->repository->find($id);
//
//        return view('books.edit', compact('book'));

        $book = Book::query()->find($id);
        //获取关联表的用户自定义推荐语
        $summary = CollectBook::where('ISBN', $book->isbn)->pluck('summary');
        $book->summary = $summary[0];

        return $this->sendResponse($book,'图书详情获取成功');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BookUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(BookUpdateRequest $request, $isbn)
    {

//        try {
//
//            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
//
//            $book = $this->repository->update($request->all(), $id);
//
//        } catch (ValidatorException $e) {
//
//
//        }


        $data = [
            'summary'       =>$request->get('summary')
        ];

        $query = CollectBook::query()
            ->where('collectId', $this->userId())
            ->where('ISBN', $isbn)
            ->first();
        if ($query instanceof CollectBook){
            $query->update($data);
            return $this->sendResponse($query, '编辑成功');
        }else{
            return $this->sendError($query, '该书与吧主未绑定');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
//        $deleted = $this->repository->delete($id);

        $query = CollectBook::query()->where('collectId', $this->userId())->where('ISBN', $request->get('isbn'))->first();
        if ($query instanceof CollectBook) {
            $query->delete();
            return $this->sendResponse($query, '图书移除成功');
        } else {
            return $this->sendError($query, '没有该数据');
        }
    }


    /**
     * 获取书吧未入库的贝壳图书
     *
     * @param Request $request
     * @return mixed
     */
    public function barBooks(Request $request){
        $input = $request->get('input');//搜索框值：书名称
        $startTime = $request->get('startTime');//开始日期
        $endTime = $request->get('endTime');//结束日期

        $isbn = CollectBook::query()
            ->where('collectId', $this->userId())
            ->where('inType',3)//流入类型 0未知 1自己流入 2他人书架流入3贝壳图书流入
            ->pluck('isbn');

        $query = Book::query();
        //搜索框条件
        if (!empty($input) || $input > 0) {
            $query->where('title', 'like', "%{$input}%");
        }
        //时间区间
        if ($startTime > 0 && $endTime > 0) {
            $startTime = date('Y-m-d H:i:s', $startTime);
            $endTime = date('Y-m-d H:i:s', $endTime);
            $query->whereBetween('created_at', [$startTime, $endTime]);
        }
        $books = $query->where('type', 0)//0.管理员上传、1、吧主上传
        ->whereNotIn('isbn', $isbn)
            ->paginate(5);

        return $this->sendResponse($books, '未入库贝壳图书列表');
    }


    //code图书信息
    function random2($length){
        $hash = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        mt_srand((double)microtime() * 1000000);
        for($i = 0; $i < $length; $i++){
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}
