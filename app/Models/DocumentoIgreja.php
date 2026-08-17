<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentoIgreja extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'documentos_igrejas';

    protected $fillable = [
        'regiao_id',
        'igreja_id',
        'user_id',
        'titulo',
    ];

    public function regiao()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'regiao_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function igreja()
    {
        return $this->belongsTo(InstituicoesInstituicao::class, 'igreja_id');
    }

    public function arquivos()
    {
        return $this->hasMany(DocumentoIgrejaArquivo::class, 'documento_igreja_id')->orderBy('ordem')->orderBy('id');
    }
}
