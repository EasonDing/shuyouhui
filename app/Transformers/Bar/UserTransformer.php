<?php

namespace App\Transformers\Bar;

use League\Fractal\TransformerAbstract;
use App\Entities\Bar\User;

/**
 * Class UserTransformer
 * @package namespace App\Transformers\Bar;
 */
class UserTransformer extends TransformerAbstract
{

    /**
     * Transform the \User entity
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id'         => (int) $model->userid,
            'UserLogo'   => $model->UserLogo,
            'username'   => $model->username,
            'sex'        => $model->sex,
            'signature'  => $model->signature,
            'Birthday'   => $model->Birthday,
            'city'       => $model->city,
            'bindPhone'  => $model->bindPhone,
            'data'       => $model->group,

            /* place your other model properties here */

//            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
//            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }
}
