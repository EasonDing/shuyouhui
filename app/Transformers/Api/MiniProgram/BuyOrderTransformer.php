<?php

namespace App\Transformers\Api\MiniProgram;

use League\Fractal\TransformerAbstract;
use App\Entities\BuyOrder;

/**
 * Class BuyOrderTransformer
 * @package namespace App\Transformers\Api\MiniProgram;
 */
class BuyOrderTransformer extends TransformerAbstract
{

    /**
     * Transform the BuyOrder entity
     * @param App\Entities\BuyOrder $model
     *
     * @return array
     */
    public function transform(BuyOrder $model)
    {
        return [
            'id'              => (int)$model->id,
            'activity_status' => $model->activity_status,
            'address'         => $model->address,
            'area'            => $model->area,
            'book_id'         => $model->book_id,
            'price'           => $model->price,
            'real_price'      => $model->real_price,
            'user_id'         => $model->user_id,

            'buy_invite' => $this->numberPeople($model),
            'buy_book'   => $this->buyBook($model),

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : ''
        ];
    }

    public function buyBook($model)
    {
        $buyBook = $model->buyBook;

        return [
            'activity_price' => $buyBook->activity_price,
            'author'         => $buyBook->author,
            'id'             => $buyBook->id,
            'image'          => asset('storage/' . $buyBook->image),
            'introduction'   => $buyBook->introduction,
            'isbn'           => $buyBook->isbn,
            'price'          => $buyBook->price,
            'real_price'     => $buyBook->real_price,
            'title'          => $buyBook->title,
        ];
    }

    //计算还需要邀请多少人
    public function numberPeople($model)
    {
        $invite_total = $model->buyBook->invite_total;
        $invite = count($model->buyInvite);

        return $invite_total - $invite;
    }
}
