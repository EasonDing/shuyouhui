<?php

namespace App\Http\Requests\Bar;

use App\Http\Requests\FormatErrorRequest;

class BannerUpdateRequest extends FormatErrorRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'     => 'required',
            'content'   => 'required',
            'image'     => 'required',
        ];
    }
}
