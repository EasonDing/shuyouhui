<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Entities\BuyInvite;
use App\Entities\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\BuyInviteCreateRequest;
use App\Http\Requests\BuyInviteUpdateRequest;
use App\Repositories\Api\MiniProgram\BuyInviteRepository;
use App\Validators\Api\MiniProgram\BuyInviteValidator;


class BuyInvitesController extends Controller
{

    /**
     * @var BuyInviteRepository
     */
    protected $repository;

    /**
     * @var BuyInviteValidator
     */
    protected $validator;

    public function __construct(BuyInviteRepository $repository, BuyInviteValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BuyInviteCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BuyInviteCreateRequest $request)
    {

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $user = Auth::user();
            $request->offsetSet('user_id', $user->user_id);
            $buyInvite = $this->repository->create($request->all());

            if ($buyInvite) {
                return $this->withCode(200)->withData($buyInvite)->response('受邀请人关联成功');
            }

            return $this->withCode(500)->withData($buyInvite)->response('受邀请人关联失败');


        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ];
            return $this->withCode(500)->withData($errors)->response('受邀请人关联失败');
        }
    }

    //检测用户是否为新用户且帮助过好友
    public function checkUserInvite(Request $request)
    {
        $dateTime = strtotime('2018-04-01 00:00:00');
        $appUser = User::query()->find($request->get('userId'));
        $updateTime = strtotime($appUser->updateTime);
        if ($updateTime > $dateTime) {
            //用户符合要求，检测是否帮助过好友

            $buyInvite = BuyInvite::query()->where('user_id', $request->get('userId'))->first();
            if ($buyInvite) {
                return $this->withCode(500)->response('您已帮助过好友');
            }
            return $this->withCode(200)->response('用户可点击帮助好友');
        }

        return $this->withCode(500)->response('您不是新用户，无法用户好友');

    }
}
