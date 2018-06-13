<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\Column;

/**
 * Class ColumnTransformer
 * @package namespace App\Transformers\Bar;
 */
class ColumnTransformer extends TransformerAbstract
{

    /**
     * Transform the \Column entity
     * @param \Column $model
     *
     * @return array
     */
    public function transform(Column $model)
    {
        return [
            'id'         => (int) $model->id,
            'title'      => $model->title,
            'content'    => $model->content,
            'image'    => asset('storage/' . $model->image),
            'created_at'    => $model->created_at,
            'status'    => $model->status,
            'reading'    => $model->reading,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
