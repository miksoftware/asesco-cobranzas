<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetencionAbono extends Model
{
    protected $fillable = [
        'retencion_id',
        'fecha_descuento',
        'valor',
        'fecha_consignacion',
        'reportado',
        'aplicado',
        'soporte',
    ];

    protected $casts = [
        'reportado' => 'boolean',
        'aplicado' => 'boolean',
    ];

    public function retencion()
    {
        return $this->belongsTo(Retencion::class);
    }
}
