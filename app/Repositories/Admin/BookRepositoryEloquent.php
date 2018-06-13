<?php

namespace App\Repositories\Admin;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Admin\BookRepository;
use App\Entities\Admin\Book;
use App\Validators\Admin\BookValidator;

/**
 * Class BookRepositoryEloquent
 * @package namespace App\Repositories\Admin;
 */
class BookRepositoryEloquent extends BaseRepository implements BookRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Book::class;
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

        return BookValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
