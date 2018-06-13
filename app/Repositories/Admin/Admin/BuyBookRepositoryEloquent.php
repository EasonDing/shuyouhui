<?php

namespace App\Repositories\Admin\Admin;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Admin\Admin\BuyBookRepository;
use App\Entities\BuyBook;
use App\Validators\Admin\Admin\BuyBookValidator;

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

    protected $fieldSearchable = [
        'title' => 'like',
    ];

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
