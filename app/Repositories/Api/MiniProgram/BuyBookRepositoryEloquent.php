<?php

namespace App\Repositories\Api\MiniProgram;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\MiniProgram\BuyBookRepository;
use App\Entities\BuyBook;
use App\Validators\Api\MiniProgram\BuyBookValidator;

/**
 * Class BuyBookRepositoryEloquent
 * @package namespace App\Repositories\Admin\Admin;
 */
class BuyBookRepositoryEloquent extends BaseRepository implements BuyBookRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BuyBook::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return BuyBookValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
