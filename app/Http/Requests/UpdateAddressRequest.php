<?php

namespace App\Http\Requests;

use App\Models\Address;

class UpdateAddressRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $address = $this->route('address');
        return $this->user()->can('update', $address);
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
            "phone_number" => "required|string|regex:/^\\d{10}$/",
            "latitude"  => "nullable|numeric|between:-90,90",
            "longitude" => "nullable|numeric|between:-180,180",
            "label"     => "required|string|max:255"
        ];
    }
}
