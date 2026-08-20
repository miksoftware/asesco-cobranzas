<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaLineamientoPorcentaje extends Model
{
    protected $table = 'empresa_lineamiento_porcentajes';

    protected $fillable = ['lineamiento_id', 'porcentaje_vigente', 'porcentaje_castigado'];

    public function lineamiento(): BelongsTo
    {
        return $this->belongsTo(EmpresaLineamiento::class, 'lineamiento_id');
    }
}
