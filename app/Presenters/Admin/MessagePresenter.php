<?php

namespace App\Presenters\Admin;

use App\Transformers\Admin\MessageTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class MessagePresenter
 *
 * @package namespace App\Presenters\Admin;
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
