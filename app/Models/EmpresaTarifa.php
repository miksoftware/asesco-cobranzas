<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaTarifa extends Model
{
    protected $table = 'empresa_tarifas';

    protected $fillable = [
        'empresa_id', 'nombre_tramo', 'dias_desde', 'dias_hasta',
        'porcentaje_vigente', 'porcentaje_castigada', 'orden',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
