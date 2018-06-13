<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;

use App\Presenters\Bar\BannerPresenter;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use App\Http\Requests\Bar\BannerCreateRequest;
use App\Http\Requests\Bar\BannerUpdateRequest;
use App\Repositories\Bar\BannerRepository;
use App\Validators\Bar\BannerValidator;


class BannersController extends Controller
{

    // banner 图片存储路径
    const IMAGE_PATH = 'banners/';

    /**
     * @var BannerRepository
     */
    protected $repository;

    /**
     * @var BannerValidator
     */
    protected $validator;

    public function __construct(BannerRepository $repository, BannerValidator $validator)
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
        $this->repository->setPresenter(BannerPresenter::class);
        $banners = $this->repository->scopeQuery(function ($query){
            return $query->where('group_id', $this->groupId())->orderBy('created_at', 'desc');
        })->paginate(10);


        return $this->sendResponse($banners, 'Banner 列表获取成功!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BannerCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BannerCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            //存储图片
            $image = $request->file('file');
            $path = $this->saveImage(self::IMAGE_PATH, $image);
            //往 $request 中追回数据
            $request->offsetSet('image', $path);
            $request->offsetSet('group_id', $this->groupId());

            $banner = $this->repository->create($request->all());

            return $this->sendResponse($banner, 'banner 添加成功!');
        } catch (\Exception $e) {

            return $this->sendResponse(false, 'banner 添加失败!');
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
        $banner = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $banner,
            ]);
        }

        return view('banners.show', compact('banner'));
    }


    /**
     * 编辑 Banner
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner = $this->repository->find($id);

        if ($banner){
            return $this->sendResponse($banner, 'banner 获取成功!');
        }

        return $this->sendError($banner, 'banner 获取失败!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BannerUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(BannerUpdateRequest $request, $id)
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

            $banner = $this->repository->update($request->all(), $id);


            return $this->sendResponse($banner, 'banner 更新成功!');
        } catch (\Exception $e) {

            return $this->sendError(false, 'banner 更新失败!');
        }
    }


    /**
     * 删除 Banner
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if ($deleted) {
            return $this->sendResponse($deleted, 'Banner 删除成功!');
        }

        return $this->sendError($deleted, 'Banner 删除失败!');
    }


    /**
     * 上架 下架
     *
     * @param Request $request
     * @return mixed
     *
     */
    public function putaway(Request $request)
    {
        if ($request->get('status')){

            $banner = $this->repository->update(['status' => 0], $request->get('id'));

            return $this->sendResponse($banner, 'Banner 已下架!');
        }else{
            $banner = $this->repository->update(['status' => 1], $request->get('id'));

            return $this->sendResponse($banner, 'Banner 已上架!');
        }

    }
}
