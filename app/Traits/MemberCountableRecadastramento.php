<?php

namespace App\Traits;

use App\Models\MembresiaMembroRecadastramento;

trait MemberCountableRecadastramento
{
    use Identifiable;

    public static function countAtivos($vinculo)
    {
        return MembresiaMembroRecadastramento::where('vinculo', $vinculo)
            ->when($vinculo == 'M', function ($query) {
                $query->whereRelation('rolAtualSessionIgreja', 'status', 'A');
            }, function ($query) {
                $query->where('status', 'A');
            })
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->withTrashed()
            ->count();
    }
    public static function countInativos($vinculo)
    {
        return MembresiaMembroRecadastramento::where('vinculo', $vinculo)
            ->when($vinculo == 'M', function ($query) {
                $query->whereRelation('rolAtualSessionIgreja', 'status', 'I');
            }, function ($query) {
                $query->where('status', 'I');
            })
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->withTrashed()
            ->count();
    }    
    public static function countTotal($vinculo)
    {
        return MembresiaMembroRecadastramento::with('rolAtualSessionIgreja')
            ->where('vinculo', $vinculo)
            ->has('rolAtualSessionIgreja')
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->withTrashed()
            ->count();
    }

    public static function countExcluidos($vinculo) 
    {
        return MembresiaMembroRecadastramento::where('vinculo', $vinculo)
            ->where('status', 'I')
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->withTrashed()
            ->count();
    }

    public static function countHasErrors($vinculo)
    {
        return MembresiaMembroRecadastramento::where('vinculo', $vinculo)->where('has_errors', 1)
            ->where('igreja_id', Identifiable::fetchSessionIgrejaLocal()->id)
            ->count();
    }
}