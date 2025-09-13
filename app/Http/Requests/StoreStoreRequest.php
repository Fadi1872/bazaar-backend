<?php

namespace App\Http\Requests;

use App\Models\Store;

class StoreStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Store::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string', 'max:10000'],
            'store_category'    => ['required', 'string', 'max:255'],
            'location_type'     => ['required', 'in:online,onsite'],
            'address_id'        => ['nullable', 'exists:addresses,id'],
            'image'             => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ];
    }
}
