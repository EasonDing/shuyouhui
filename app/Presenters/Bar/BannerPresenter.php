<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\BannerTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BannerPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class BannerPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BannerTransformer();
    }
}
