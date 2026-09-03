<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroJuridicoDeposito extends Model
{
    protected $fillable = [
        'cobro_juridico_id',
        'fecha_descuento',
        'valor',
        'fecha_consignacion',
        'reportado',
        'soporte',
        'aplicado',
    ];

    protected $casts = [
        'reportado' => 'boolean',
        'aplicado' => 'boolean',
    ];

    public function cobroJuridico()
    {
        return $this->belongsTo(CobroJuridico::class);
    }
}
