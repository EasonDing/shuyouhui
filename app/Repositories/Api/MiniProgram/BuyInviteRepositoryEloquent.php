<?php

namespace App\Repositories\Api\MiniProgram;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\MiniProgram\BuyInviteRepository;
use App\Entities\BuyInvite;
use App\Validators\Api\MiniProgram\BuyInviteValidator;

/**
 * Class BuyInviteRepositoryEloquent
 * @package namespace App\Repositories\Api\MiniProgram;
 */
class BuyInviteRepositoryEloquent extends BaseRepository implements BuyInviteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BuyInvite::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return BuyInviteValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
