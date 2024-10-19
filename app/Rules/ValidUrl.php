<?php

namespace App\Rules;

use App\Data\Factories\UrlFactory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Please provide a valid URL');

            return;
        }

        try {
            app(UrlFactory::class)->fromString($value);
        } catch (\InvalidArgumentException $e) {
            $fail('Please provide a valid URL');
        }
    }
}
