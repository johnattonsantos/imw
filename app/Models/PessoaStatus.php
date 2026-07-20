<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PessoaStatus extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'pessoas_status';

    protected $fillable = ['descricao', 'codigo'];

    public static function cpfOpcionalParaDescricao(?string $descricao): bool
    {
        $descricaoNormalizada = Str::of($descricao ?? '')->ascii()->lower()->trim()->toString();

        return Str::contains($descricaoNormalizada, [
            'jubilado',
            'jubilados',
            'descontinuado',
            'falecido',
            'falecimento',
        ]);
    }

    public function cpfOpcional(): bool
    {
        return self::cpfOpcionalParaDescricao($this->descricao);
    }
}
