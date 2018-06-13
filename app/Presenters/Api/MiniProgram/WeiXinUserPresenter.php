<?php

namespace App\Presenters\Api\MiniProgram;

use App\Transformers\Api\MiniProgram\WeiXinUserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class WeiXinUserPresenter
 *
 * @package namespace App\Presenters\Api\MiniProgram;
 */
class WeiXinUserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new WeiXinUserTransformer();
    }
}
