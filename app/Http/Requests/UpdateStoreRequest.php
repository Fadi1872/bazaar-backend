<?php

namespace App\Http\Requests;


class UpdateStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $store = $this->route('store');
        return $this->user()->can('update', $store);
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
            'store_category_id' => ['required', 'exists:store_categories,id'],
            'location_type'     => ['required', 'in:online,onsite'],
            'address_id'        => ['nullable', 'exists:addresses,id'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }
}
