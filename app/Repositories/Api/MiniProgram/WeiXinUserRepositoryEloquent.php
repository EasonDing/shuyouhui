<?php

namespace App\Repositories\Api\MiniProgram;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\MiniProgram\WeiXinUserRepository;
use App\Entities\WeiXinUser;
use App\Validators\Api\MiniProgram\WeiXinUserValidator;

/**
 * Class WeiXinUserRepositoryEloquent
 * @package namespace App\Repositories\Api\MiniProgram;
 */
class WeiXinUserRepositoryEloquent extends BaseRepository implements WeiXinUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WeiXinUser::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return WeiXinUserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
