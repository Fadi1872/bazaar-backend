<?php

namespace App\Http\Requests;


class UpdateBazaarRequesst extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $bazaar = $this->route('bazaar');
        return $this->user()->can('update', $bazaar);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'start_date'            => ['nullable', 'date', 'after_or_equal:today'],
            'end_date'              => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_requesting_date' => ['nullable', 'date', 'before_or_equal:end_requesting_date'],
            'end_requesting_date'   => ['nullable', 'date', 'after_or_equal:start_requesting_date'],
            'address_id'            => ['nullable', 'integer', 'exists:addresses,id'],
            'location_type'         => ['nullable', 'in:online,onsite'],
            'category_id'           => ['nullable', 'integer', 'exists:bazaar_categories,id'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }
}
