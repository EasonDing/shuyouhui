<?php

namespace App\Presenters\Api\OfficialAccount;

use App\Transformers\Api\OfficialAccount\AuthTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class AuthPresenter.
 *
 * @package namespace App\Presenters\Api\OfficialAccount;
 */
class AuthPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new AuthTransformer();
    }
}
