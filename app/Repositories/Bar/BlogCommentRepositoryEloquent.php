<?php

namespace App\Repositories\Bar;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Bar\BlogCommentRepository;
use App\Entities\Bar\BlogComment;
use App\Validators\Bar\BlogCommentValidator;

/**
 * Class BlogCommentRepositoryEloquent
 * @package namespace App\Repositories\Bar;
 */
class BlogCommentRepositoryEloquent extends BaseRepository implements BlogCommentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BlogComment::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
