<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class HasStoreForShowInStore implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        // Only validate if true
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) && !$user?->store()->exists()) {
            $fail("You must create a store before setting {$attribute} to true.");
        }
    }
}
