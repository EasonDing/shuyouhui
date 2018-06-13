<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\WeiXinUserCreateRequest;
use App\Http\Requests\WeiXinUserUpdateRequest;
use App\Repositories\Api\MiniProgram\WeiXinUserRepository;
use App\Validators\Api\MiniProgram\WeiXinUserValidator;


class WeiXinUsersController extends Controller
{

    /**
     * @var WeiXinUserRepository
     */
    protected $repository;

    /**
     * @var WeiXinUserValidator
     */
    protected $validator;

    public function __construct(WeiXinUserRepository $repository, WeiXinUserValidator $validator)
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
        $weiXinUsers = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $weiXinUsers,
            ]);
        }

        return view('weiXinUsers.index', compact('weiXinUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  WeiXinUserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WeiXinUserCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $weiXinUser = $this->repository->create($request->all());

            $response = [
                'message' => 'WeiXinUser created.',
                'data'    => $weiXinUser->toArray(),
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
        $weiXinUser = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $weiXinUser,
            ]);
        }

        return view('weiXinUsers.show', compact('weiXinUser'));
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

        $weiXinUser = $this->repository->find($id);

        return view('weiXinUsers.edit', compact('weiXinUser'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  WeiXinUserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(WeiXinUserUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $weiXinUser = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'WeiXinUser updated.',
                'data'    => $weiXinUser->toArray(),
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
                'message' => 'WeiXinUser deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'WeiXinUser deleted.');
    }
}
