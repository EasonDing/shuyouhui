<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Presenters\Bar\BlogCommentPresenter;
use App\Presenters\Bar\BlogPresenter;

use App\Repositories\Bar\BlogCommentRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Bar\BlogCreateRequest;
use App\Http\Requests\Bar\BlogUpdateRequest;
use App\Repositories\Bar\BlogRepository;
use App\Validators\Bar\BlogValidator;


class BlogsController extends Controller
{

    /**
     * @var BlogRepository
     */
    protected $repository;

    protected $blogCommentRepository;

    /**
     * @var BlogValidator
     */
    protected $validator;

    public function __construct(BlogRepository $repository, BlogCommentRepository $blogCommentRepository, BlogValidator $validator)
    {
        $this->repository = $repository;
        $this->blogCommentRepository = $blogCommentRepository;
        $this->validator  = $validator;
    }


    /**
     * 获取书吧帖子列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->setPresenter(BlogPresenter::class);
        $blogs = $this->repository->scopeQuery(function ($query){
            return $query->where('groupId', $this->groupId());
        })->paginate(10);

        return $this->sendResponse($blogs, '书吧帖子获取成功!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BlogCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BlogCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $blog = $this->repository->create($request->all());

            $response = [
                'message' => 'Blog created.',
                'data'    => $blog->toArray(),
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
        $blog = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $blog,
            ]);
        }

        return view('blogs.show', compact('blog'));
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

        $blog = $this->repository->find($id);

        return view('blogs.edit', compact('blog'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BlogUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(BlogUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $blog = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Blog updated.',
                'data'    => $blog->toArray(),
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
     * 删除帖子
     *
     * @param  int $blogId 帖子 ID
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($blogId)
    {

        try {
            DB::beginTransaction();
            $deleted = $this->repository->delete($blogId);
            $this->blogCommentRepository->deleteWhere(['AltId' => $blogId, 'type' => 2]);
            DB::commit();

            return $this->sendResponse($deleted, '帖子删除成功!');
        } catch (\Exception $e) {

            DB::rollback();
            return $this->sendError(false, '帖子删除失败!');
        }
    }


    /**
     *
     * 获取帖子评论列表
     * @param int $blogId 帖子 ID
     */
    public function comment($blogId)
    {

        $this->blogCommentRepository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->blogCommentRepository->setPresenter(BlogCommentPresenter::class);
        $blogComments = $this->blogCommentRepository->scopeQuery(function ($query) use ($blogId){
            return $query->where(['AltId' => $blogId, 'type' => 2]);
        })->paginate(10);

        return $this->sendResponse($blogComments, '评论列表获取成功!');
    }


    /**
     *
     * 删除帖子评论
     * @param int $commentId 评论 ID
     */
    public function destroyComment($commentId)
    {

        $deleted = $this->blogCommentRepository->delete($commentId);

        if ($deleted){
            return $this->sendResponse($deleted, '评论删除成功!');
        }

        return $this->sendError(false, '评论删除失败!');
    }
}
