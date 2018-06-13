<?php

namespace App\Transformers\Api;

use League\Fractal\TransformerAbstract;
use App\Entities\BookCode;

/**
 * Class BookCodeTransformer.
 *
 * @package namespace App\Transformers\Api;
 */
class BookCodeTransformer extends TransformerAbstract
{
    /**
     * Transform the BookCode entity.
     *
     * @param \App\Entities\BookCode $model
     *
     * @return array
     */
    public function transform(BookCode $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
