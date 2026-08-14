<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GestionPago extends Model
{
    protected $table = 'gestion_pagos';

    protected $fillable = [
        'cedula',
        'fecha_descuento',
        'valor',
        'fecha_consignacion',
        'reportado',
        'aplicado',
        'soporte',
        'user_id',
    ];

    protected $casts = [
        'reportado' => 'boolean',
        'aplicado'  => 'boolean',
        'valor'     => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
