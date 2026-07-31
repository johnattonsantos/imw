<?php

namespace App\Rules;

use App\Models\MembresiaMembro;
use App\Traits\Identifiable;
use Illuminate\Contracts\Validation\Rule;

class UniqueCPFInIgrejaRule implements Rule
{
    use Identifiable;

    private $ignoreMembroId;
    private bool $allowInactiveDuplicate;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($ignoreMembroId = null, bool $allowInactiveDuplicate = false)
    {
        $this->ignoreMembroId = $ignoreMembroId;
        $this->allowInactiveDuplicate = $allowInactiveDuplicate;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        $cpf = preg_replace('/[^0-9]/', '', $value);
        if ($cpf === '') {
            return true;
        }

        $query = MembresiaMembro::withTrashed()
            ->where('cpf', $cpf)
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id);

        if ($this->ignoreMembroId) {
            $query->where('id', '!=', $this->ignoreMembroId);
        }

        $duplicados = $query->get(['id', 'status', 'deleted_at']);

        if ($duplicados->isEmpty()) {
            return true;
        }

        if ($this->allowInactiveDuplicate) {
            return $duplicados->every(function ($membro) {
                return $membro->status !== MembresiaMembro::STATUS_ATIVO || $membro->deleted_at !== null;
            });
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Já existe um membro com este CPF nesta Igreja.';
    }
}
