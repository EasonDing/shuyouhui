<?php
namespace App\Http\Controllers;
use App\Entities\News;
use App\Entities\Push;
use Illuminate\Http\Request;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\NewsCreateRequest;
use App\Http\Requests\NewsUpdateRequest;
use App\Repositories\NewsRepository;
use App\Validators\NewsValidator;
class NewsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->get('input');//搜索框值：用户名
        $type = $request->get('value');//消息类型1、公共2、私人
        $query = News::query();
        //搜索框条件
        if (!empty($input) || $input > 0){
            $query->where('username','like', "%{$input}%");
        }
        //消息类型 类型:1、公共, 2、私人
        if ($type > 0) {
            $query->where('news_type', $type);
        }
        $news = $query->orderBy('created_at', 'desc')
            ->where('send_id', 1)//1为管理员发送的消息
            ->with('user')->paginate(5);
        return $this->sendResponse($news,'历史消息获取成功');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  NewsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(NewsCreateRequest $request)
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            $news = $this->repository->create($request->all());
            $response = [
                'message' => 'News created.',
                'data'    => $news->toArray(),
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
        $news = $this->repository->find($id);
        if (request()->wantsJson()) {
            return response()->json([
                'data' => $news,
            ]);
        }
        return view('news.show', compact('news'));
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
        $news = $this->repository->find($id);
        return view('news.edit', compact('news'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  NewsUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(NewsUpdateRequest $request, $id)
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $news = $this->repository->update($request->all(), $id);
            $response = [
                'message' => 'News updated.',
                'data'    => $news->toArray(),
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
        $push = News::destroy($id);
        return $this->sendResponse($push, '消息删除成功');
    }
}