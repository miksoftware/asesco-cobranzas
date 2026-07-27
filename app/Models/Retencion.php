<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retencion extends Model
{
    protected $fillable = [
        // Section 1
        'no_radicacion', 'portafolio_empresa', 'tipo_descuento', 'cedula_tt', 'nombre_tt', 
        'nombre_sujeto_retencion', 'calidad_sujeto', 'gestor_encargado_id', 'etapa_gestor_encargado', 
        'gestor_radicador_id', 'tipo_negociacion',
        // Section 2
        'nit_empresa', 'empresa', 'tipo_contrato', 'rango_salarial', 'fecha_radicacion', 'ind', 
        'valor_retencion_total', 'correo_empresa', 'nombre_contacto_empresa', 'telefono_contacto_empresa', 
        'telefono_sujeto_retencion', 'telefono_2_sujeto_retencion', 'correo_sujeto_retencion', 
        'estado_retencion', 'efecto_gestion_retencion', 'recaudo_retencion', 'relacion_recaudo_reportado', 
        'eliminada', 'tipo_cartera', 'dias_ini', 'dias_ed', 'recaudo',
        // Locks
        'is_section1_locked', 'is_section2_locked', 'is_abonos_locked'
    ];

    protected $casts = [
        'is_section1_locked' => 'boolean',
        'is_section2_locked' => 'boolean',
        'is_abonos_locked' => 'boolean',
    ];

    public function abonos()
    {
        return $this->hasMany(RetencionAbono::class);
    }

    public function gestiones()
    {
        return $this->hasMany(RetencionGestion::class);
    }

    public function histories()
    {
        return $this->hasMany(RetencionHistory::class)->orderBy('created_at', 'desc');
    }

    public function gestorEncargado()
    {
        return $this->belongsTo(User::class, 'gestor_encargado_id');
    }

    public function gestorRadicador()
    {
        return $this->belongsTo(User::class, 'gestor_radicador_id');
    }
}
