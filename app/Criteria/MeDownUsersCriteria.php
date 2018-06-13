<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MeDownUsersCriteria
 * @package namespace App\Criteria;
 */
class MeDownUsersCriteria implements CriteriaInterface
{

    protected $user_ids;

    public function __construct($user_ids)
    {
        $this->user_ids = $user_ids;
    }

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->whereIn('uid', $this->user_ids);

        return $model;
    }
}
