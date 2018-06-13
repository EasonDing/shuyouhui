<?php

namespace App\Presenters\Admin;

use App\Transformers\Admin\BookTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookPresenter
 *
 * @package namespace App\Presenters\Admin;
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
