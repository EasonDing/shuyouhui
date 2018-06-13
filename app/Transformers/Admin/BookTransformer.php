<?php

namespace App\Transformers\Admin;

use League\Fractal\TransformerAbstract;
use App\Entities\Admin\Book;

/**
 * Class BookTransformer
 * @package namespace App\Transformers;
 */
class BookTransformer extends TransformerAbstract
{

    /**
     * Transform the \Book entity
     * @param \Book $model
     *
     * @return array
     */
    public function transform(Book $model)
    {
        return [
            'id'         => (int) $model->id,
            'image'      => asset('storage/' . $model->image),
            'title'      => $model->title,
            'author'      => $model->author,
            'price'      => $model->price,
            'summary'      => $model->summary,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
