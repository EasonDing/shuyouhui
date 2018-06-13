<?php

namespace App\Http\Controllers;

use App\Entities\News;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TNewsCreateRequest;
use App\Http\Requests\TNewsUpdateRequest;
use App\Repositories\TNewsRepository;
use App\Validators\TNewsValidator;


class TNewsController extends Controller
{

    /**
     * @var TNewsRepository
     */
    protected $repository;

    /**
     * @var TNewsValidator
     */
    protected $validator;

//    public function __construct(TNewsRepository $repository, TNewsValidator $validator)
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
        $input = $request->get('input');//搜索框值：手机号或书吧名称
        $type = $request->get('type');//消息类型1、公共2、私人

        $query = News::query();
        //搜索框条件
        if (!empty($input) || $input > 0){
            $query->where('username', 'like', "%{$input}%");
        }
        //书吧类型
        if ($type > 0){
            $query->where('news_type', $type);
        }

        $news = $query->orderBy('created_at', 'desc')->where('send_id', $user['poster_id'])->with('user')->paginate(5);

        return $this->sendResponse($news,'历史消息获取成功');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TNewsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TNewsCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $tNews = $this->repository->create($request->all());

            $response = [
                'message' => 'TNews created.',
                'data'    => $tNews->toArray(),
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
        $tNews = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $tNews,
            ]);
        }

        return view('tNews.show', compact('tNews'));
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

        $tNews = $this->repository->find($id);

        return view('tNews.edit', compact('tNews'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  TNewsUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(TNewsUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $tNews = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'TNews updated.',
                'data'    => $tNews->toArray(),
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
        $news = News::destroy($id);
        return $this->sendResponse($news, '消息删除成功');
    }
}
