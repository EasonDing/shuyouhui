<?php

namespace App\Transformers\Bar;

use App\Entities\Bar\Order;
use League\Fractal\TransformerAbstract;

/**
 * Class OrderTransformer
 * @package namespace App\Transformers;
 */
class OrderTransformer extends TransformerAbstract
{

    /**
     * Transform the \Order entity
     * @param \Order $model
     *
     * @return array
     */
    public function transform(Order $model)
    {
        return [
            'id'          => (int)$model->id,
            'orderNo'     => $model->orderNo,
            'money'       => $model->money,
            'orderStatus' => $this->formatStatus($model->orderStatus),
            'updateTime'  => $model->updateTime,
            'phone'       => $model->user->bindPhone,
            'book'        => $model->code->book,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }

    public function formatStatus($orderStatus)
    {
        if ($orderStatus == 3) {
            return '已完成';
        } else {
            return '未完成';
        }
    }
}
