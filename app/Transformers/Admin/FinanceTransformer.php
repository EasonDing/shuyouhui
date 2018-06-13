<?php

namespace App\Transformers\Admin;

use App\Entities\Admin\Finance;
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
            'type'       => '收入',
            'content'     => '会员充值',
            'money'     => $model->money,
            'phone'     => $model->phone,
            'updateTime'     => $model->updateTime,

            /* place your other model properties here */

//            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
//            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
