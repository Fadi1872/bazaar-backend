<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SearchBazaarRequest extends BaseRequest
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
            'name'           => ['nullable', 'max:255'],
            'status' => [
                'nullable',
                Rule::in(['upcoming', 'ongoing', 'past']),
            ],

            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],

            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:bazaar_categories,id'],

            'cities'        => 'nullable|array',
            'cities.*'      => 'string|max:255',
        ];
    }
}
