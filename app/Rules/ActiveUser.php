<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveUser implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);

        if (!$user) {
            $fail('El usuario seleccionado no existe.');
            return;
        }

        if (!$user->is_active) {
            $fail('No se puede asignar la tarea a un usuario inactivo.');
        }
    }
}
