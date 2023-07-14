<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderRule implements ValidationRule
{
    const ORDER_BY_DESC = 'desc';

    const ORDER_BY_ASC = 'asc';

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === self::ORDER_BY_ASC || $value === self::ORDER_BY_DESC) {
            return;
        }

        $fail(sprintf(':attribute must be either %s or %s', self::ORDER_BY_DESC, self::ORDER_BY_ASC));
    }
}
