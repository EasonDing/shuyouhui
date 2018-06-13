<?php

namespace App\Repositories\Bar;

use App\Presenters\Bar\GroupUserPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\GroupRepository;
use App\Entities\Bar\Group;
use App\Validators\Bar\GroupValidator;

/**
 * Class GroupRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class GroupRepositoryEloquent extends BaseRepository implements GroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Group::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return GroupValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getPresenter()
    {
        return new GroupUserPresenter();
    }
}
