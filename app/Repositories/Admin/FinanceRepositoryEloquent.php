<?php

namespace App\Repositories\Admin;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Admin\FinanceRepository;
use App\Entities\Admin\Finance;
use App\Validators\Admin\FinanceValidator;

/**
 * Class FinanceRepositoryEloquent
 * @package namespace App\Repositories\Admin;
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

    protected $fieldSearchable = [
        'phone' => 'like',
    ];

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
