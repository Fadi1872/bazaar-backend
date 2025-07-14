<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Auth::user());
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
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ];
    }
}
