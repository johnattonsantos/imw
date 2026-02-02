<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Anexo extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'nome', 
        'caminho',
        'descricao',
        'lancamento_id',
    ];

    public function getMimeAttribute()
    {
        return collect(explode('.', $this->caminho))->last();
    }

    public function lancamento()
    {
        return $this->belongsTo(FinanceiroLancamento::class, 'lancamento_id');
    }
}
