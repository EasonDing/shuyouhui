<?php

namespace App\Presenters\Admin;

use App\Transformers\Admin\AdminUserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AdminUserPresenter
 *
 * @package namespace App\Presenters\Admin;
 */
class AdminUserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AdminUserTransformer();
    }
}
