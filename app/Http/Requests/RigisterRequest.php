<?php

namespace App\Http\Requests;

class RigisterRequest extends BaseRequest
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
            'name' => 'required|string|max:255',
            'age' => "required|integer|max:100|min:18",
            "number" => "required|string|regex:/^\\d{10}$/",
            "gender" => "required|boolean",
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|confirmed|min:8|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            "app_source" => "required|string|in:main,admin"
        ];
    }
}
