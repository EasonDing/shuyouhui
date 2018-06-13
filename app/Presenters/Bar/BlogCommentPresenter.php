<?php

namespace App\Presenters\Bar;

use App\Transformers\Bar\BlogCommentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BlogCommentPresenter
 *
 * @package namespace App\Presenters\Bar;
 */
class BlogCommentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BlogCommentTransformer();
    }
}
