<?php

namespace App\Transformers\Bar;

use App\Entities\Bar\Banner;
use League\Fractal\TransformerAbstract;

/**
 * Class BannerTransformer
 * @package namespace App\Transformers;
 */
class BannerTransformer extends TransformerAbstract
{

    /**
     * Transform the \Banner entity
     * @param \Banner $model
     *
     * @return array
     */
    public function transform(Banner $model)
    {
        return [
            'id'         => (int) $model->id,
            'title'      => $model->title,
            'content'      => $model->content,
            'image'      => asset('storage/' . $model->image),
            'status'      => $model->status,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
