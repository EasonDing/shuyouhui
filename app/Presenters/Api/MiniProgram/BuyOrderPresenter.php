<?php

namespace App\Presenters\Api\MiniProgram;

use App\Transformers\Api\MiniProgram\BuyOrderTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BuyOrderPresenter
 *
 * @package namespace App\Presenters\Api\MiniProgram;
 */
class BuyOrderPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BuyOrderTransformer();
    }
}
