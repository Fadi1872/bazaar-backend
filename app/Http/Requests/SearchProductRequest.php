<?php

namespace App\Http\Requests;

use App\Models\Product;

class SearchProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'nullabale|max:255',
            'min_rating'    => 'nullable|numeric|min:0|max:5',
            'price_min'     => 'nullable|numeric|min:0',
            'price_max'     => 'nullable|numeric|min:0',
            'category_ids'  => 'nullable|array',
            'category_ids.*'=> 'integer|exists:product_categories,id',
            'cities'        => 'nullable|array',
            'cities.*'      => 'string|max:255',
        ];
    }
}
