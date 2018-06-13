<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\FormatErrorRequest;

class AdminUserCreateRequest extends FormatErrorRequest
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
            'username'  => 'required|unique:users,username',
            'password'  => 'required',
            'group_id'  => 'required',
        ];
    }
}
