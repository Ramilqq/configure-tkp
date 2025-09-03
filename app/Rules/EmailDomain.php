<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Разрешаем только адреса в домене test.ru
        if (!is_string($value) || !preg_match('/@ru-drive\.com$/i', $value)) {
            $fail('Используйте корпоративную почту в домене.');
        }
    }
}
