<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\ColumnTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ColumnPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class ColumnPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ColumnTransformer();
    }
}
