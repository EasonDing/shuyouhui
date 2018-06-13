<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\BlogTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BlogPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class BlogPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BlogTransformer();
    }
}
