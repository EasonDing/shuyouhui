<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\GroupTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class GroupPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class GroupPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GroupTransformer();
    }
}
