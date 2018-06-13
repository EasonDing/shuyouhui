<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\Group;

/**
 * Class GroupTransformer
 * @package namespace App\Transformers;
 */
class GroupTransformer extends TransformerAbstract
{

    /**
     * Transform the \Group entity
     * @param \Group $model
     *
     * @return array
     */
    public function transform(Group $model)
    {
        return [
            'id'         => (int) $model->groupId,

            /* place your other model properties here */

//            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
//            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
