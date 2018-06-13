<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Controller;
use App\Presenters\Api\MiniProgram\BuyBookPresenter;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BuyBookCreateRequest;
use App\Http\Requests\BuyBookUpdateRequest;
use App\Repositories\Api\MiniProgram\BuyBookRepository;
use App\Validators\Api\MiniProgram\BuyBookValidator;


class BuyBooksController extends Controller
{

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
        $this->repository->setPresenter(BuyBookPresenter::class);
        $buyBooks = $this->repository->scopeQuery(function ($query){
            return $query->where('activity_status', 0)->orderBy('created_at', 'desc');
        })->paginate(10);

        if ($buyBooks){
            return $this->withCode(200)->withData($buyBooks)->response('图书列表获取成功！');
        }

        return $this->withCode(500)->withData('图书列表获取失败！')->response();
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

            $buyBook = $this->repository->create($request->all());

            $response = [
                'message' => 'BuyBook created.',
                'data'    => $buyBook->toArray(),
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
        $this->repository->setPresenter(BuyBookPresenter::class);
        $buyBook = $this->repository->find($id);

        return $this->withCode(200)->withData($buyBook)->response();
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

        $buyBook = $this->repository->find($id);

        return view('buyBooks.edit', compact('buyBook'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BuyBookUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(BuyBookUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $buyBook = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'BuyBook updated.',
                'data'    => $buyBook->toArray(),
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

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'BuyBook deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'BuyBook deleted.');
    }
}
