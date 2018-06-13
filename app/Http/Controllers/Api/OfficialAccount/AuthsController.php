<?php

namespace App\Http\Controllers\Api\OfficialAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\Api\OfficialAccount\AuthCreateRequest;
use App\Http\Requests\Api\OfficialAccount\AuthUpdateRequest;
use App\Repositories\Api\OfficialAccount\AuthRepository;
use App\Validators\Api\OfficialAccount\AuthValidator;


class AuthsController extends Controller
{

    /**
     * @var AuthRepository
     */
    protected $repository;

    /**
     * @var AuthValidator
     */
    protected $validator;

    public function __construct(AuthRepository $repository, AuthValidator $validator)
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
        session('wechat.oauth_user'); // 拿到授权用户资料

        return view('weixin.invite.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AuthCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AuthCreateRequest $request)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $auth = $this->repository->create($request->all());

            $response = [
                'message' => 'Auth created.',
                'data'    => $auth->toArray(),
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
        $auth = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $auth,
            ]);
        }

        return view('auths.show', compact('auth'));
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

        $auth = $this->repository->find($id);

        return view('auths.edit', compact('auth'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  AuthUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(AuthUpdateRequest $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $auth = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Auth updated.',
                'data'    => $auth->toArray(),
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
                'message' => 'Auth deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Auth deleted.');
    }
}
