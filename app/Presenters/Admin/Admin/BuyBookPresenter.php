<?php

namespace App\Presenters\Admin\Admin;

use App\Transformers\Admin\Admin\BuyBookTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BuyBookPresenter
 *
 * @package namespace App\Presenters\Admin\Admin;
 */
class BuyBookPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BuyBookTransformer();
    }
}
