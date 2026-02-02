<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class GCeuDiario extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'gceu_diario';
    protected $guarded = [];
}
