<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CurrentEnterpriseCriteria
 * @package namespace App\Criteria;
 */
class CurrentEnterpriseCriteria implements CriteriaInterface
{

    protected $company_id;

    public function __construct($id)
    {
        $this->company_id = $id;
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
        $model = $model->where('company_id','=', $this->company_id);
        return $model;
    }
}
