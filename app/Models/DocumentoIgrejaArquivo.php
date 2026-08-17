<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentoIgrejaArquivo extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'documentos_igrejas_arquivos';

    protected $fillable = [
        'documento_igreja_id',
        'nome_original',
        'caminho',
        'mime_type',
        'tamanho',
        'ordem',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoIgreja::class, 'documento_igreja_id');
    }

    public function isPreviewable(): bool
    {
        $mimeType = strtolower((string) $this->mime_type);

        return str_starts_with($mimeType, 'image/')
            || str_starts_with($mimeType, 'text/')
            || $mimeType === 'application/pdf';
    }
}
