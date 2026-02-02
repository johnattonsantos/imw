<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ControleImportacaoCsv extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'controle_importacoes_csv';

    protected $fillable = ['alias', 'file', 'static_method', 'target_table', 'executed'];
}
