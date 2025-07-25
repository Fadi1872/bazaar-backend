<?php

namespace App\Http\Requests;


class LogInRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email|max:255',
            'password' => 'required|string|min:8|max:255',
            "app_source" => "required|string|in:main,admin"
        ];
    }
}
