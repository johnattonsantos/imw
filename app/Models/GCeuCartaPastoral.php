<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class GCeuCartaPastoral extends Model implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

     // status
    const STATUS_ATIVO = 'A';
    const STATUS_INATIVO = 'I';

    protected $table = 'gceu_cartas_pastorais';
    protected $guarded = [];
}
