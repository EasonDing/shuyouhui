<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\Blog;

/**
 * Class BlogTransformer
 * @package namespace App\Transformers\Bar;
 */
class BlogTransformer extends TransformerAbstract
{

    /**
     * Transform the \Blog entity
     * @param \Blog $model
     *
     * @return array
     */
    public function transform(Blog $model)
    {
        return [
            'id'         => (int) $model->blogId,
            'content'    => $model->content,
            'updateTime'    => $model->updateTime,
            'user'       => $model->user()->select('username')->first(),


            /* place your other model properties here */

//            'created_at' => $model->created_at,
//            'updated_at' => $model->updated_at
        ];
    }
}
