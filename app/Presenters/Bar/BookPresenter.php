<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\BookTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class BookPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookTransformer();
    }
}
