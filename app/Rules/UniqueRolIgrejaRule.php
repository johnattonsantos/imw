<?php

namespace App\Rules;

use App\Models\MembresiaRolPermanente;
use App\Traits\Identifiable;
use Illuminate\Contracts\Validation\Rule;

class UniqueRolIgrejaRule implements Rule
{
    use Identifiable;

    private $membroId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($membroId = null)
    {
        $this->membroId = $membroId;
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
        $hasRolPermanente = (booL) MembresiaRolPermanente::where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->where('numero_rol', $value)
            ->when($this->membroId, fn ($query) => $query->where('membro_id', '<>', $this->membroId))
            ->exists();
        
        return !$hasRolPermanente;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Este Nº Rol já está sendo utilizado por outro membro.';
    }
}
