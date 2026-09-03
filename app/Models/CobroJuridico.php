<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroJuridico extends Model
{
    protected $fillable = [
        // Sección 1: Datos Generales del Proceso
        'estado_proceso',
        'departamento',
        'municipio',
        'especialidad',
        'juzgado_conocimiento',
        'no_radicado',
        'cedula',

        // Sección 2 - Pestaña 1: Datos Generales
        'demandado_1',
        'demandado_2',
        'demandado_3',
        'demandado_4',
        'fecha_etapa',
        'etapa_procesal',
        'fecha_actividad',
        'actividad',
        'concepto_viabilidad_juridica',
        'garantias_juridica',
        'anotacion_abogado',

        'valor_total_proceso',

        // Locks
        'is_section1_locked',
        'is_section2_locked',
        'is_depositos_locked',
    ];

    protected $casts = [
        'is_section1_locked' => 'boolean',
        'is_section2_locked' => 'boolean',
        'is_depositos_locked' => 'boolean',
    ];

    protected $appends = [
        'recaudo_total',
        'saldo_pendiente',
        'recaudo_mes_actual',
    ];

    public function depositos()
    {
        return $this->hasMany(CobroJuridicoDeposito::class);
    }

    public function gestiones()
    {
        return $this->hasMany(CobroJuridicoGestion::class)->orderBy('created_at', 'desc');
    }

    public function latestGestion()
    {
        return $this->hasOne(CobroJuridicoGestion::class)->latestOfMany();
    }

    public function histories()
    {
        return $this->hasMany(CobroJuridicoHistory::class)->orderBy('created_at', 'desc');
    }

    public function getRecaudoTotalAttribute()
    {
        return $this->depositos()->sum('valor');
    }

    public function getSaldoPendienteAttribute()
    {
        return floatval($this->valor_total_proceso) - $this->recaudo_total;
    }

    public function getRecaudoMesActualAttribute()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return $this->depositos()
            ->whereMonth('fecha_descuento', $currentMonth)
            ->whereYear('fecha_descuento', $currentYear)
            ->sum('valor');
    }
}
