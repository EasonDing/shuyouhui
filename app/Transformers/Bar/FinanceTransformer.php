<?php

namespace App\Transformers\Bar;

use App\Entities\Bar\Finance;
use League\Fractal\TransformerAbstract;

/**
 * Class FinanceTransformer
 * @package namespace App\Transformers;
 */
class FinanceTransformer extends TransformerAbstract
{

    /**
     * Transform the \Finance entity
     * @param \Finance $model
     *
     * @return array
     */
    public function transform(Finance $model)
    {
        return [
            'id'         => (int) $model->id,
            'money'         => $model->money . '元',
            'updateTime'         => $model->updateTime,
            'orderStatus'         => $model->orderStatus,
            'content'         => '吧友购买商品',
            'type'         => 1,
            'phone'     => $model->user->bindPhone,

            /* place your other model properties here */

//            'created_at' => $model->created_at,
//            'updated_at' => $model->updated_at
        ];
    }
}
