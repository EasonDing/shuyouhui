<?php

namespace App\Transformers\Api\MiniProgram;

use League\Fractal\TransformerAbstract;
use App\Entities\BuyBook;

/**
 * Class BuyBookTransformer
 * @package namespace App\Transformers\Api\MiniProgram;
 */
class BuyBookTransformer extends TransformerAbstract
{

    /**
     * Transform the BuyBook entity
     * @param App\Entities\BuyBook $model
     *
     * @return array
     */
    public function transform(BuyBook $model)
    {
        return [
            'id'             => (int)$model->id,
            'isbn'           => $model->isbn,
            'title'          => $model->title,
            'author'         => $model->author,
            'image'          => asset('storage/' . $model->image),
            'image_text'     => asset('storage/' . $model->image_text),
            'publisher'      => $model->publisher,
            'introduction'   => $model->introduction,
            'price'          => $model->price,
            'activity_price' => $model->activity_price,
            'real_price'     => $model->real_price,

            'order' => $model->order,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : ''
        ];
    }
}
