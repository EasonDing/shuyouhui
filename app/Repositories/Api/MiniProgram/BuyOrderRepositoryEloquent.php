<?php

namespace App\Repositories\Api\MiniProgram;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Api\MiniProgram\BuyOrderRepository;
use App\Entities\BuyOrder;
use App\Validators\Api\MiniProgram\BuyOrderValidator;

/**
 * Class BuyOrderRepositoryEloquent
 * @package namespace App\Repositories\Api\MiniProgram;
 */
class BuyOrderRepositoryEloquent extends BaseRepository implements BuyOrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BuyOrder::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return BuyOrderValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
