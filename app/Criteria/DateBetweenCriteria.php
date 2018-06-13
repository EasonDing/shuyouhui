<?php

namespace App\Criteria;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MyCriteria
 * @package namespace App\Criteria\Admin;
 */
class DateBetweenCriteria implements CriteriaInterface
{
    protected $request;

    protected $betweenField;

    public function __construct(Request $request, $betweenField = 'created_at')
    {
        $this->request = $request;
        $this->betweenField = $betweenField;
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
        //根据日期区间筛选
        $date = $this->request->get('date');
        if (!empty($date)){
            $startTime = $date[0] . ' 00:00:00';
            $endTime = $date[1] . ' 23:59:59';
            $model = $model->whereBetween($this->betweenField, [$startTime, $endTime]);
        }
        //列表排序
        return $model;
    }
}
