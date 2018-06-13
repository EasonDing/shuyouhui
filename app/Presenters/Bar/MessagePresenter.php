<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\MessageTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class MessagePresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class MessagePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new MessageTransformer();
    }
}
