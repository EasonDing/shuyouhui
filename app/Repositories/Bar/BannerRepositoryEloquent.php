<?php

namespace App\Repositories\Bar;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\BannerRepository;
use App\Entities\Bar\Banner;
use App\Validators\Bar\BannerValidator;

/**
 * Class BannerRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class BannerRepositoryEloquent extends BaseRepository implements BannerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Banner::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return BannerValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
