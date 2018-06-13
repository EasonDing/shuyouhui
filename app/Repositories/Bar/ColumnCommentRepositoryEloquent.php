<?php

namespace App\Repositories\Bar;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\ColumnCommentRepository;
use App\Entities\Bar\ColumnComment;
use App\Validators\Bar\ColumnCommentValidator;

/**
 * Class ColumnCommentRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class ColumnCommentRepositoryEloquent extends BaseRepository implements ColumnCommentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ColumnComment::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
