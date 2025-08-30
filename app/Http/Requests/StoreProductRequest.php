<?php

namespace App\Http\Requests;

use App\Models\Product;

class StoreProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'cost'                => ['required', 'numeric', 'min:0', 'lte:price'],
            'stock_qty'           => ['required', 'integer', 'min:0'],
            'show_in_store'       => ['sometimes', 'boolean'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }
}
