<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroJuridicoGestion extends Model
{
    protected $fillable = [
        'cobro_juridico_id',
        'user_id',
        'fecha_etapa',
        'etapa_procesal',
        'fecha_actividad',
        'actividad',
        'fecha_gestion',
        'gestion',
        'detalle',
    ];

    protected $casts = [
        'fecha_gestion' => 'datetime',
    ];

    public function cobroJuridico()
    {
        return $this->belongsTo(CobroJuridico::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
