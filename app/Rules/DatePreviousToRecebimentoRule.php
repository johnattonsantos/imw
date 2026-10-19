<?php

namespace App\Rules;

use App\Exceptions\MembroNotFoundException;
use App\Models\MembresiaMembro;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DatePreviousToRecebimentoRule implements Rule
{
    private Carbon | null $dtRecepcao;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($membroId)
    {
        $this->dtRecepcao = MembresiaMembro::findOr($membroId, fn() => throw new MembroNotFoundException('Membro não encontrado'))
            ->rolAtualSessionIgreja
            ->dt_recepcao;
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
        return Carbon::parse($value)->gte($this->dtRecepcao);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "A data informada deve ser igual ou posterior à data de recebimento do membro ({$this->dtRecepcao->format('d/m/Y')})";
    }
}
