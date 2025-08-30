<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class StoreAddressRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Address::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "city"      => "required|string|max:100",
            "phone_number" =>"required|string|regex:/^\\d{10}$/",
            "latitude"  => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "label"     => "required|string|max:255"
        ];
    }
}
