<?php

namespace App\Presenters\Admin;

use App\Transformers\Admin\FinanceTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FinancePresenter
 *
 * @package namespace App\Presenters\Admin;
 */
class FinancePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new FinanceTransformer();
    }
}
