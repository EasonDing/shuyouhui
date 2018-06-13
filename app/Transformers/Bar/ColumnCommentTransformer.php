<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\ColumnComment;

/**
 * Class ColumnCommentTransformer
 * @package namespace App\Transformers\Bar;
 */
class ColumnCommentTransformer extends TransformerAbstract
{

    /**
     * Transform the \ColumnComment entity
     * @param \ColumnComment $model
     *
     * @return array
     */
    public function transform(ColumnComment $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
