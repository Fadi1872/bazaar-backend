<?php

namespace App\Http\Requests;

use App\Models\Bazaar;

class StoreBazaarRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Bazaar::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['required', 'string'],
            'start_date'            => ['required', 'date', 'after_or_equal:today'],
            'end_date'              => ['required', 'date', 'after_or_equal:start_date'],
            'start_requesting_date' => ['required', 'date', 'before_or_equal:end_requesting_date'],
            'end_requesting_date'   => ['required', 'date', 'after_or_equal:start_requesting_date'],
            'address_id'            => ['required', 'integer', 'exists:addresses,id'],
            'location_type'         => ['required', 'in:online,onsite'],
            'category_id'           => ['required', 'integer', 'exists:bazaar_categories,id'],
            'image'                 => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }
}
