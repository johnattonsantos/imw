<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class VwIgreja extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'vw_igrejas';

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
