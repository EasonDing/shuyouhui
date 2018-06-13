<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\FinanceTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FinancePresenter
 *
 * @package namespace App\Presenters\Bar;
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
