<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MembresiaDisciplina extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'membresia_disciplinas';

    protected $fillable = [
        'dt_inicio',
        'dt_termino',
        'modo_disciplina_id',
        'pastor_id',
        'observacao',
        'membro_id',
        'distrito_id',
        'igreja_id',
        'regiao_id',
        'congregacao_id'
    ];

    protected $casts = [
        'dt_inicio'  => 'date',
        'dt_termino' => 'date',
    ];

    public function membro()
    {
        return $this->belongsTo(MembresiaMembro::class, 'membro_id', 'id');
    }

    public function distrito()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'distrito_id', 'id')
            ->where('instituicoes_instituicoes.tipo_instituicao_id', InstituicoesTipoInstituicao::DISTRITO);
    }

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id', 'id')
            ->where('instituicoes_instituicoes.tipo_instituicao_id', InstituicoesTipoInstituicao::REGIAO);
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id', 'id')
            ->where('instituicoes_instituicoes.tipo_instituicao_id', InstituicoesTipoInstituicao::IGREJA_LOCAL);
    }

    public function congregacao()
    {
        return $this->belongsTo(CongregacoesCongregacao::class, 'congregacao_id', 'id');
    }

    public function pastor()
    {
        return $this->belongsTo(PessoasPessoa::class, 'pastor_id', 'id');
    }

    public function modo()
    {
        return $this->belongsTo(MembresiaSituacao::class, 'modo_disciplina_id', 'id');
    }
}