<?php

namespace App\Repositories\Api\OfficialAccount;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\OfficialAccount\LoginRepository;
use App\Entities\Login;
use App\Validators\Api\OfficialAccount\LoginValidator;

/**
 * Class LoginRepositoryEloquent.
 *
 * @package namespace App\Repositories\Api\OfficialAccount;
 */
class LoginRepositoryEloquent extends BaseRepository implements LoginRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Login::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return LoginValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
