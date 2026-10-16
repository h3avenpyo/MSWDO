<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContactNumber implements ValidationRule
{
    public function __construct(private ?int $length = null, private ?int $maximum = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !preg_match('/^[0-9]+$/D', $value)) {
            $fail('The :attribute must contain digits only (0–9), without spaces or symbols.');
        } elseif ($this->length !== null && strlen($value) !== $this->length) {
            $fail('The :attribute must be exactly '.$this->length.' digits.');
        } elseif ($this->maximum !== null && strlen($value) > $this->maximum) {
            $fail('The :attribute must contain at most '.$this->maximum.' digits.');
        }
    }
}
