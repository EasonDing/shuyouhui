<?php

namespace App\Transformers\Api\MiniProgram;

use League\Fractal\TransformerAbstract;
use App\Entities\WeiXinUser;

/**
 * Class WeiXinUserTransformer
 * @package namespace App\Transformers\Api\MiniProgram;
 */
class WeiXinUserTransformer extends TransformerAbstract
{

    /**
     * Transform the WeiXinUser entity
     * @param App\Entities\WeiXinUser $model
     *
     * @return array
     */
    public function transform(WeiXinUser $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
