<?php

namespace App\Http\Requests;

use App\Models\Store;

class StoreCommentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('comment', Store::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|min:1|max:10000',
            'rating' => 'required|integer|min:0|max:5',
        ];
    }
}
