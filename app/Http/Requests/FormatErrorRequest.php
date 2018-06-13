<?php
namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
class FormatErrorRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response(
            $this->formatErrors($validator),
            422
        ));
    }
    protected function formatErrors(Validator $validator)
    {
        $message = $validator->errors()->first();
        $result = [
            'message' => $message,
            'code' => 422
        ];
        return $result;
    }
}