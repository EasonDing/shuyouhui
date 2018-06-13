<?php

namespace App\Repositories\Api\OfficialAccount;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\OfficialAccount\AuthRepository;
use App\Entities\Auth;
use App\Validators\Api\OfficialAccount\AuthValidator;

/**
 * Class AuthRepositoryEloquent.
 *
 * @package namespace App\Repositories\Api\OfficialAccount;
 */
class AuthRepositoryEloquent extends BaseRepository implements AuthRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Auth::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AuthValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
