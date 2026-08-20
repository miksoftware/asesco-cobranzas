<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaLineamientoTramo extends Model
{
    protected $table = 'empresa_lineamiento_tramos';

    protected $fillable = ['lineamiento_id', 'nombre_tramo', 'tipo_cartera', 'porcentaje', 'orden'];

    public function lineamiento(): BelongsTo
    {
        return $this->belongsTo(EmpresaLineamiento::class, 'lineamiento_id');
    }
}
