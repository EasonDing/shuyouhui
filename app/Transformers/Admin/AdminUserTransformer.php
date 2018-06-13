<?php

namespace App\Transformers\Admin;

use League\Fractal\TransformerAbstract;
use App\Entities\Admin\AdminUser;

/**
 * Class AdminUserTransformer
 * @package namespace App\Transformers\Admin;
 */
class AdminUserTransformer extends TransformerAbstract
{

    /**
     * Transform the \AdminUser entity
     * @param \AdminUser $model
     *
     * @return array
     */
    public function transform(AdminUser $model)
    {
        return [
            'id'         => (int)$model->id,
            'username'   => $model->username,
            'name'   => $model->name,
            'group_name' => $model->group_name,
            'phone'      => $model->mobile,

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
