<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaCanal extends Model
{
    protected $table = 'empresa_canales';

    protected $fillable = [
        'empresa_id', 'nombre_canal', 'numero_canal', 'medio_pago', 'orden',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
