<?php

namespace App\Presenters\Api;

use App\Transformers\Api\BookCodeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookCodePresenter.
 *
 * @package namespace App\Presenters\Api;
 */
class BookCodePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookCodeTransformer();
    }
}
