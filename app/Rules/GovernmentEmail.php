<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GovernmentEmail implements ValidationRule
{
    /**
     * Dominios de correo permitidos para el sistema gubernamental
     */
    protected array $allowedDomains = [
        'lapaz.gob.bo',
        'lapazeste.gob.bo',
        'gobierno.gob.bo',
        // Dominios temporales para desarrollo/pruebas
        'gmail.com',
        'hotmail.com',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('El :attribute debe ser un correo electrónico válido.');
            return;
        }

        $domain = substr(strrchr($value, "@"), 1);

        if (!in_array(strtolower($domain), $this->allowedDomains)) {
            $fail('El :attribute debe ser un correo electrónico gubernamental oficial (@lapaz.gob.bo, @lapazeste.gob.bo, etc.).');
        }
    }
}
