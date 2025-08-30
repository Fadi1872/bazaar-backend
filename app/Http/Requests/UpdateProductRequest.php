<?php

namespace App\Http\Requests;


class UpdateProductRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        return $this->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                => ['sometimes', 'string', 'max:255'],
            'description'         => ['sometimes', 'string'],
            'price'               => ['sometimes', 'numeric', 'min:0'],
            'cost'                => ['sometimes', 'numeric', 'min:0', 'lte:price'],
            'stock_qty'           => ['sometimes', 'integer', 'min:0'],
            'show_in_store'       => ['sometimes', 'boolean'],
            'product_category_id' => ['sometimes', 'exists:product_categories,id'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }
}
