<?php

namespace App\Presenters\Api\MiniProgram;

use App\Transformers\Api\MiniProgram\BuyInviteTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BuyInvitePresenter
 *
 * @package namespace App\Presenters\Api\MiniProgram;
 */
class BuyInvitePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BuyInviteTransformer();
    }
}
