<?php

namespace App\Presenters\Api\MiniProgram;

use App\Transformers\Api\MiniProgram\BuyBookTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BuyBookPresenter
 *
 * @package namespace App\Presenters\Api\MiniProgram;
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
