<?php

namespace App\Transformers\Api\OfficialAccount;

use League\Fractal\TransformerAbstract;
use App\Entities\Auth;

/**
 * Class AuthTransformer.
 *
 * @package namespace App\Transformers\Api\OfficialAccount;
 */
class AuthTransformer extends TransformerAbstract
{
    /**
     * Transform the Auth entity.
     *
     * @param \App\Entities\Auth $model
     *
     * @return array
     */
    public function transform(Auth $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
