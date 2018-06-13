<?php
namespace App\Http\Controllers;

use App\Entities\Bar\CollectBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TBookCreateRequest;
use App\Http\Requests\TBookUpdateRequest;
use App\Repositories\TBookRepository;
use App\Validators\TBookValidator;
class TBooksController extends Controller
{
    /**
     * @var TBookRepository
     */
    protected $repository;
    /**
     * @var TBookValidator
     */
    protected $validator;

//    public function __construct(TBookRepository $repository, TBookValidator $validator)
//    {
//        $this->repository = $repository;
//        $this->validator  = $validator;
//    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $input = $request->get('input');//搜索框值：书名称
        $startTime = $request->get('startTime');//开始日期
        $endTime = $request->get('endTime');//结束日期
        $CollectBook =CollectBook::query()->where('collectId', $user['poster_id'])->where('inflow', 1)
            ->where('Type', 1)->with('book')->paginate(5);
//        $query = TBook::query();
//        $query->whereIn('isbn', $CollectBook);
//        //搜索框条件
//        if (!empty($input) || $input > 0) {
//            $query->where('title', 'like', "%{$input}%");
//        }
//        //时间区间
//        if ($startTime > 0 && $endTime > 0) {
//            $startTime = date('Y-m-d H:i:s', $startTime);
//            $endTime = date('Y-m-d H:i:s', $endTime);
//            $query->whereBetween('created_at', [$startTime, $endTime]);
//        }
//
//        $books = $query->orderBy('created_at', 'desc')->paginate(5);
        return $this->sendResponse($CollectBook, '上传图书列表获取成功');
    }
    //获取吧主 入库的贝壳图书 //TODO 应该是收藏不是入库
    public function bookShell(Request $request){
        $user = Auth::user();
        $posterId = $user['poster_id'];
        $input = $request->get('input');//搜索框值：书名称
        $startTime = $request->get('startTime');//开始日期
        $endTime = $request->get('endTime');//结束日期
        $isbn = CollectBook::query()->orderBy('created_at', 'desc')
            ->where('collectId', $posterId)
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
        return $this->sendResponse($books, '入库贝壳图书列表');
    }
    //获取吧主 未入库的贝壳图书
    public function bookBar(Request $request){
        $user = Auth::user();
        $input = $request->get('input');//搜索框值：书名称
        $startTime = $request->get('startTime');//开始日期
        $endTime = $request->get('endTime');//结束日期
        $isbn = CollectBook::query()
            ->where('collectId', $user['poster_id'])
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
    //添加图书到书架
    public function addStorage(Request $request)
    {
        $user = Auth::user();
        $isbn = $request->get('isbn');
        $summary = $request->get('summary');
        $type = 2;//类型(1.APP用户 2.书友群,3.书店)
        $collectId = $user['poster_id'];//入库操作者
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
                $code = Code::create([
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
        } catch (Exception $exception) {
            DB::rollback();
        }
    }
    //编辑贝壳图书推荐语
    public function summary(Request $request, $isbn){
        $user = Auth::user();
        $data = [
            'summary'       =>$request->get('summary')
        ];
        $query = CollectBook::query()
            ->where('collectId', $user['poster_id'])
            ->where('ISBN', $isbn)
            ->first();
        if ($query instanceof CollectBook){
            $query->update($data);
            return $this->sendResponse($query, '编辑成功');
        }else{
            return $this->sendError($query, '该书与吧主未绑定');
        }
    }
    //从书吧书架移除
    public function removeBook(Request $request)
    {
        $user = Auth::user();
        try {
            $query = CollectBook::query()->where('collectId', $user['poster_id'])->where('ISBN', $request->get('isbn'))->first();
            if ($query instanceof CollectBook) {
//                DB::beginTransaction();
                $query->delete();
//                Code::where('id', $request->get('codeid'))->delete();
//                DB::commit();
                return $this->sendResponse($query, '图书移除成功');
            } else {
                return $this->sendError($query, '没有该数据');
            }
        } catch (Exception $exception) {
//            DB::rollback();
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  TBookCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TBookCreateRequest $request)
    {
        $user = Auth::user();
        $data = [
            'cate'      => 0,//图书类型，暂无用
            'title' => $request->get('title'),
            'image' => $request->get('image'),
            'author' => $request->get('author'),
            'isbn' => $request->get('isbn'),
            'publisher' => $request->get('publisher'),
            'price' => $request->get('price'),
            'summary' => $request->get('summary'),
            'type' => 1,//0.管理员上传、1、吧主上传
            'updateTime'    =>Carbon::now(),//其时不需要，但为了查询方便添加
        ];
        $code   ="http://m.bookfan.cn/book/" . $this->random2(28);
        $book = TBook::where('isbn', $data['isbn'])->first();
        //书库中已有该isbn的书
        if (!empty($book)) {
            //判断此书是管理员上传还是吧主上传，如果是管理员上传提示去书库中添加，如果是其它吧主上传就直接与此书绑定
            //0、管理员 1、吧主
            if ($book->type === 0) {
                return $this->sendError($book, '贝壳书库中已有此书', 501);
            }
            try {
                DB::beginTransaction();
                $code = Code::create([
                    'code' => $code,
                    'isbn' => $data['isbn'],
                    'flowPrice' => 0,
                    'recommend' => '',
                ]);
                $CollectBook = CollectBook::query()->create([
                    'ISBN' => $book->isbn,
                    'codeid' => $code['id'],
                    'Type' => 1,//类型(1.APP用户 2.书友群,3.书店)
                    'collectId' => $user['poster_id'],//吧主id
                    'updateTime' => Carbon::now(),
                    'inflow' => 1,//0.流出 1.流入,
                    'inType' => 1,//流入类型 0未知 1自己流入 2他人书架流入3贝壳图书流入
                    'summary' => $data['summary'],
                    'address' => '',
                    'name' => '',
                    'tel' => '',
                ]);
                if ($code && $CollectBook){
                    DB::commit();
                    return $this->sendResponse($CollectBook, '图书上传成功');
                }
            } catch (Exception $exception) {
                DB::rollback();
            }
        }else{
            //书库中没有该isbn的书，正常上传
            //开启事务
            DB::beginTransaction();
            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);
            $book = $this->repository->create($data);
            //create创建返回的是一个model 这个model就是TBook 从而进行判断
            if ($book instanceof TBook) {
                $code = Code::create([
                    'code' => $code,
                    'isbn' => $data['isbn'],
                    'flowPrice' => 0,
                    'recommend' => '',
                ]);
                $CollectBook = CollectBook::query()->create([
                    'ISBN'      =>$book->isbn,
                    'codeid'    =>$code['id'],
                    'Type'      =>1,//类型(1.APP用户 2.书友群,3.书店)
                    'collectId' =>$user['poster_id'],//吧主id
                    'updateTime'=>Carbon::now(),
                    'inflow'    =>1,//0.流出 1.流入,
                    'inType'    =>1,//流入类型 0未知 1自己流入 2他人书架流入3贝壳图书流入
                    'summary'   =>$data['summary'],
                    'address'   =>'',
                    'name'      =>'',
                    'tel'       =>'',
                ]);
                if ($code && $CollectBook){
                    DB::commit();
                    return $this->sendResponse($book, '图书上传成功');
                }
            } else {
                DB::rollback();
                return $this->sendError($book, '图书上传失败', 500);
            }
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
        $tBook = $this->repository->find($id);
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $tBook,
            ]);
        }
        return view('tBooks.show', compact('tBook'));
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
        $book = Book::query()->find($id);
        //获取关联表的用户自定义推荐语
        $summary = CollectBook::where('ISBN', $book->isbn)->pluck('summary');
        $book->summary = $summary[0];
        return $this->sendResponse($book,'图书详情获取成功');
    }
    //上传图书 编辑页面 图书信息
    public function bookInfo($codeId){
        $CollectBook = CollectBook::where('codeid', $codeId)->where('inflow', 1)->with('book')->first();
        return $this->sendResponse($CollectBook, '图书信息获取成功');
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  TBookUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(TBookUpdateRequest $request, $id)
    {
        $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
        $book = $this->repository->update($request->all(), $id);
        return $this->sendResponse($book, '图书更新成功');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($codeId)
    {
        $count = CollectBook::where('codeid', $codeId)->count();
        if ($count > 0){
            return $this->sendError($count, '该书已产生流转行为', 501);
        }else{
            $CollectBook = CollectBook::where('codeid', $codeId)->delete();
            return $this->sendResponse($CollectBook, '图书删除成功');
        }
    }
    //图片上传
    public function uploadImage(Request $request){
        $file = $request->file('file');
        $allowed_extensions = ["png", "jpg"];//限制的数据类型
        //判断图片是否符合格式要求
        if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
            return ['error' => '只能上传 png, jpg 格式'];
        }
        $destinationPath = 'storage/uploads/' . date('Y/m/d', time()) . "/"; //public 文件夹下面建 storage/uploads 文件夹
        $extension = $file->getClientOriginalExtension();//获取图片类型
        $fileName = 'book_' . str_random(10) . '.' . $extension;
        $file->move($destinationPath, $fileName);
        $filePath = asset($destinationPath . $fileName);//完整的图片路径
        return $filePath;
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