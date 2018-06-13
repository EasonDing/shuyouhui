<?php

namespace App\Transformers\Admin;

use League\Fractal\TransformerAbstract;
use App\Entities\Admin\User;

/**
 * Class UserTransformer
 * @package namespace App\Transformers\Admin;
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
            'id'         => (int)$model->userid,
            'UserLogo'   => $model->UserLogo,
            'username'   => $model->username,
            'sex'        => $this->formatSex($model->sex),
            'bindPhone'  => $model->bindPhone,
            'Birthday'   => $this->formatAge($model->Birthday),
            'updateTime' => $model->updateTime,
            'identity'   => count($model->groups) ? '吧主' : '会员',

            /* place your other model properties here */

            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }

    public function formatSex($sex)
    {
        switch ($sex) {
            case 1:
                return '男';
                break;
            case 2:
                return '女';
                break;
            default:
                return '未知';
                break;
        }
    }

    public function formatAge($birthday)
    {
        if (!empty($birthday)){
            $year1 = date('Y');
            $year2 = substr($birthday, 0, 4);

            return $year1 - $year2;
        }
    }
}
