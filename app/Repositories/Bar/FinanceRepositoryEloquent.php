<?php

namespace App\Repositories\Bar;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\FinanceRepository;
use App\Entities\Bar\Finance;
use App\Validators\Bar\FinanceValidator;

/**
 * Class FinanceRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class FinanceRepositoryEloquent extends BaseRepository implements FinanceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Finance::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return FinanceValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
