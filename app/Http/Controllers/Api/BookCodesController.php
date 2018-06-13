<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BookCodeCreateRequest;
use App\Http\Requests\BookCodeUpdateRequest;
use App\Repositories\Api\BookCodeRepository;
use App\Validators\Api\BookCodeValidator;

/**
 * Class BookCodesController.
 *
 * @package namespace App\Http\Controllers\Api;
 */
class BookCodesController extends Controller
{
    /**
     * @var BookCodeRepository
     */
    protected $repository;

    /**
     * @var BookCodeValidator
     */
    protected $validator;

    /**
     * BookCodesController constructor.
     *
     * @param BookCodeRepository $repository
     * @param BookCodeValidator $validator
     */
    public function __construct(BookCodeRepository $repository, BookCodeValidator $validator)
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
        $bookCodes = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $bookCodes,
            ]);
        }

        return view('bookCodes.index', compact('bookCodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BookCodeCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(BookCodeCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $bookCode = $this->repository->create($request->all());

            $response = [
                'message' => 'BookCode created.',
                'data'    => $bookCode->toArray(),
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
    public function show($code)
    {
        $bookCode = $this->repository->scopeQuery(function ($query) use($code) {
            return $query->where('code', $code);
        })->first();

        if ($bookCode->activity_status) {
            return $this->withCode(200)->withData($bookCode)->response('二维码已激活');
        }

        return $this->withCode(500)->withData($bookCode)->response('二维码未激活');
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
        $bookCode = $this->repository->find($id);

        return view('bookCodes.edit', compact('bookCode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BookCodeUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(BookCodeUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $bookCode = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'BookCode updated.',
                'data'    => $bookCode->toArray(),
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
                'message' => 'BookCode deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'BookCode deleted.');
    }
}
