<?php

namespace App\Http\Requests\Concerns;

use App\Models\User;

trait DetectsProfileOnlyUpdate
{
    protected function isProfileOnlyUpdate(User $user): bool
    {
        foreach (['name', 'email', 'pessoa_id'] as $field) {
            if ((string) $this->input($field) !== (string) $user->{$field}) {
                return false;
            }
        }

        foreach (['cpf', 'telefone'] as $field) {
            $submitted = preg_replace('/\D/', '', (string) $this->input($field));
            $stored = preg_replace('/\D/', '', (string) $user->{$field});
            if ($submitted !== $stored) {
                return false;
            }
        }

        return !$this->filled('password') && !$this->filled('password_confirmation');
    }
}
