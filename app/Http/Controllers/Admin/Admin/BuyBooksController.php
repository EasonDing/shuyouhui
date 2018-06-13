<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Presenters\Admin\Admin\BuyBookPresenter;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BuyBookCreateRequest;
use App\Http\Requests\BuyBookUpdateRequest;
use App\Repositories\Admin\Admin\BuyBookRepository;
use App\Validators\Admin\Admin\BuyBookValidator;
use App\Criteria\DateBetweenCriteria;


class BuyBooksController extends Controller
{

    //0元购书 图书图片存储目录
    const IMAGE_PATH = 'buyBook/';

    /**
     * @var BuyBookRepository
     */
    protected $repository;

    /**
     * @var BuyBookValidator
     */
    protected $validator;

    public function __construct(BuyBookRepository $repository, BuyBookValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(new DateBetweenCriteria($request));
        $this->repository->setPresenter(BuyBookPresenter::class);
        $buyBooks = $this->repository->scopeQuery(function ($query) {
            //活动状态：0.未锁定|1.锁定|2.该书已被用户领取
            return $query->where('activity_status', '<>', 2);
        })->paginate(10);

        return $this->withCode(200)->withData($buyBooks)->response('0元购书图书列表');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BuyBookCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BuyBookCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            //存储图片
            $image = $request->file('file');
            $image_text = $request->file('file2');

            $imagePath = $this->saveImage(self::IMAGE_PATH, $image);
            $imageTextPath = $this->saveImage(self::IMAGE_PATH, $image_text);
            //往 $request 中追加数据
            $request->offsetSet('image', $imagePath);
            $request->offsetSet('image_text', $imageTextPath);
            $request->offsetSet('invite_total', 38);//需要邀请38个新用户
            $request->offsetSet('activity_price', 0.00);
            $request->offsetSet('real_price', $request->price);

            $buyBook = $this->repository->create($request->all());

            if ($buyBook) {
                return $this->withCode(200)->withData($buyBook)->response('图书添加成功');
            }

            return $this->withCode(500)->withData($buyBook)->response('图书添加失败');

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
        $buyBook = $this->repository->find($id);

        if ($buyBook) {
            return $this->withCode(200)->withData($buyBook)->response('获取图书详情成功！');
        }

        return $this->withCode(500)->withData($buyBook)->response('获取图书详情失败!');
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

        $this->repository->setPresenter(BuyBookPresenter::class);
        $buyBook = $this->repository->find($id);

        if ($buyBook) {
            return $this->withCode(200)->withData($buyBook)->response('获取图书详情成功！');
        }

        return $this->withCode(500)->withData($buyBook)->response('获取图书详情失败!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BuyBookUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(BuyBookUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            //存储图片
            $image = $request->file('file');
            $image_text = $request->file('file2');

            $request->offsetUnset('image');
            if (!empty($image)) {
                $imagePath = $this->saveImage(self::IMAGE_PATH, $image);
                $request->offsetSet('image', $imagePath);
            } else {
                $request->offsetUnset('image');
            }
            if ($image_text) {
                $imageTextPath = $this->saveImage(self::IMAGE_PATH, $image_text);
                $request->offsetSet('image_text', $imageTextPath);
            } else {
                $request->offsetUnset('image_text');
            }

            $buyBook = $this->repository->update($request->all(), $id);

            if ($buyBook) {
                return $this->withCode(200)->withData($buyBook)->response('图书更新成功!');
            }

            return $this->withCode(500)->withData($buyBook)->response('图书更新失败!');
        } catch (ValidatorException $e) {

            return $this->withCode(500)->withData($e->getMessageBag())->response('服务器错误!');
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
            return $this->withCode(200)->withData($deleted)->response('图书删除成功！');
        }

        return $this->withCode(500)->withData($deleted)->response('图书删除失败！');
    }
}
