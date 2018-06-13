<?php

namespace App\Transformers\Admin;

use App\Entities\Admin\Group;
use League\Fractal\TransformerAbstract;

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
            'id'           => (int)$model->groupId,
            'groupName'    => $model->groupName,
            'updateTime'   => $model->updateTime,
            'type'         => $this->formatType($model->type),
            'barAdmin' => $this->barAdmin($model->users),
            'books'        => $model->books->count(),
            'users'        => $model->users->count(),

            /* place your other model properties here */

//            'created_at' => $model->created_at ? $model->created_at->toDateTimeString() : null,
//            'updated_at' => $model->updated_at ? $model->updated_at->toDateTimeString() : null
        ];
    }

    public function formatType($type)
    {
        switch ($type) {
            case 1:
                return '校园';
                break;
            case 2:
                return '兴趣';
                break;
            case 3:
                return '城市';
                break;
            default:
                return '其它';
                break;
        }
    }

    public function barAdmin($users)
    {
        foreach ($users as $user) {
            if ($user->pivot->type == 1) {
                return [
                    'username'  =>$user->username,
                    'phone'  =>$user->bindPhone,
                ];
            }
        }
    }
}
