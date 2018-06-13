<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\ColumnCommentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ColumnCommentPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class ColumnCommentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ColumnCommentTransformer();
    }
}
