<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\BlogComment;

/**
 * Class BlogCommentTransformer
 * @package namespace App\Transformers\Bar;
 */
class BlogCommentTransformer extends TransformerAbstract
{

    /**
     * Transform the \BlogComment entity
     * @param \BlogComment $model
     *
     * @return array
     */
    public function transform(BlogComment $model)
    {
        return [
            'id'         => (int) $model->commentId,
            'content'    => $model->content,
            'updateTime' => $model->updateTime,
            'user'       => $model->user()->select(['username', 'UserLogo'])->first()

            /* place your other model properties here */

//            'created_at' => $model->created_at,
//            'updated_at' => $model->updated_at
        ];
    }
}
