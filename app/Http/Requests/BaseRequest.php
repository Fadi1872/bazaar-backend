<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'error in credentials provided',
            'data' => $validator->errors()
        ], 422);

        throw new HttpResponseException($response);
    }

    protected function failedAuthorization()
    {
        $response = response()->json([
            'success' => false,
            'message' => "you don't have permission!",
        ], 403);

        throw new HttpResponseException($response);
    }
}
