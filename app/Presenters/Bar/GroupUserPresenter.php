<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\GroupUserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class GroupPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class GroupUserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new GroupUserTransformer();
    }
}
