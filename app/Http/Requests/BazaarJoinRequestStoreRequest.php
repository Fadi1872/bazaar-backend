<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class BazaarJoinRequestStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userId = Auth::id();

            $invalidProducts = collect($this->products ?? [])
                ->filter(fn($id) => !Product::where('id', $id)->where('user_id', $userId)->exists());

            if ($invalidProducts->isNotEmpty()) {
                $validator->errors()->add('products', 'Some products do not belong to the authenticated user.');
            }
        });
    }
}
