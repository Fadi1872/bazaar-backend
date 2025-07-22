<?php

namespace App\Http\Requests;

use App\Models\Store;

class StoreFilterRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Store::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating'         => ['nullable', 'numeric', 'min:0', 'max:5'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:store_categories,id'],
            'cities'         => ['nullable', 'array'],
            'cities.*'       => ['string', 'max:255'],
        ];
    }
}
