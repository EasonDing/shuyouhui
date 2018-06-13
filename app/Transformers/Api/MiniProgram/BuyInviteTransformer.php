<?php

namespace App\Transformers\Api\MiniProgram;

use League\Fractal\TransformerAbstract;
use App\Entities\BuyInvite;

/**
 * Class BuyInviteTransformer
 * @package namespace App\Transformers\Api\MiniProgram;
 */
class BuyInviteTransformer extends TransformerAbstract
{

    /**
     * Transform the BuyInvite entity
     * @param App\Entities\BuyInvite $model
     *
     * @return array
     */
    public function transform(BuyInvite $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
