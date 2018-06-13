<?php

namespace App\Repositories\Bar;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\ColumnRepository;
use App\Entities\Bar\Column;
use App\Validators\Bar\ColumnValidator;

/**
 * Class ColumnRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class ColumnRepositoryEloquent extends BaseRepository implements ColumnRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Column::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ColumnValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
