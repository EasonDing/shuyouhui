<?php

namespace App\Transformers\Admin\Admin;

use League\Fractal\TransformerAbstract;
use App\Entities\BuyBook;

/**
 * Class BuyBookTransformer
 * @package namespace App\Transformers\Admin\Admin;
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
            'id'           => (int)$model->id,
            'image'        => asset('storage/' . $model->image),
            'image_text'   => asset('storage/' . $model->image_text),
            'title'        => $model->title,
            'author'       => $model->author,
            'publisher'    => $model->publisher,
            'isbn'         => $model->isbn,
            'price'        => $model->price,
            'introduction' => $model->introduction,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
