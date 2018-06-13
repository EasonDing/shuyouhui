<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CurrentUploadCriteria
 * @package namespace App\Criteria;
 */
class CurrentUploadCriteria implements CriteriaInterface
{
    protected $upload_id;

    public function __construct($id)
    {
        $this->upload_id = $id;
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
        $model = $model->where('upload_id','=', $this->upload_id);
        return $model;
    }
}
