<?php

namespace App\Repositories\Admin;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Admin\AdminUserRepository;
use App\Entities\Admin\AdminUser;
use App\Validators\Admin\AdminUserValidator;

/**
 * Class AdminUserRepositoryEloquent
 * @package namespace App\Repositories\Admin;
 */
class AdminUserRepositoryEloquent extends BaseRepository implements AdminUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AdminUser::class;
    }

    protected $fieldSearchable = [
        'group_name' => 'like',
        'mobile' => 'like',
    ];

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AdminUserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
