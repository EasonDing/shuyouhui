<?php

namespace App\Presenters\Api\OfficialAccount;

use App\Transformers\Api\OfficialAccount\LoginTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LoginPresenter.
 *
 * @package namespace App\Presenters\Api\OfficialAccount;
 */
class LoginPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LoginTransformer();
    }
}
