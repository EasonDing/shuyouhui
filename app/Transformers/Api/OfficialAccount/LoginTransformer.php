<?php

namespace App\Transformers\Api\OfficialAccount;

use League\Fractal\TransformerAbstract;
use App\Entities\Login;

/**
 * Class LoginTransformer.
 *
 * @package namespace App\Transformers\Api\OfficialAccount;
 */
class LoginTransformer extends TransformerAbstract
{
    /**
     * Transform the Login entity.
     *
     * @param \App\Entities\Login $model
     *
     * @return array
     */
    public function transform(Login $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
