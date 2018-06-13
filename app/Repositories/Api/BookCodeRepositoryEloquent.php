<?php

namespace App\Repositories\Api;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\BookCodeRepository;
use App\Entities\BookCode;
use App\Validators\Api\BookCodeValidator;

/**
 * Class BookCodeRepositoryEloquent.
 *
 * @package namespace App\Repositories\Api;
 */
class BookCodeRepositoryEloquent extends BaseRepository implements BookCodeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BookCode::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return BookCodeValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
