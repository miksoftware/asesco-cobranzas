<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('retencions', function (Blueprint $table) {
            $table->id();
            
            // Sección 1: Datos Generales
            $table->string('no_radicacion')->nullable();
            $table->string('portafolio_empresa')->nullable();
            $table->string('tipo_descuento')->nullable();
            $table->string('cedula_tt')->nullable();
            $table->string('nombre_tt')->nullable();
            $table->string('nombre_sujeto_retencion')->nullable();
            $table->string('calidad_sujeto')->nullable();
            $table->unsignedBigInteger('gestor_encargado_id')->nullable();
            $table->string('etapa_gestor_encargado')->nullable();
            $table->unsignedBigInteger('gestor_radicador_id')->nullable();
            $table->string('tipo_negociacion')->nullable();

            // Sección 2: Datos Generales de la Retención
            $table->string('nit_empresa')->nullable();
            $table->string('empresa')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->string('rango_salarial')->nullable();
            $table->date('fecha_radicacion')->nullable();
            $table->string('ind')->nullable();
            $table->decimal('valor_retencion_total', 15, 2)->nullable();
            $table->string('correo_empresa')->nullable();
            $table->string('nombre_contacto_empresa')->nullable();
            $table->string('telefono_contacto_empresa')->nullable();
            $table->string('telefono_sujeto_retencion')->nullable();
            $table->string('telefono_2_sujeto_retencion')->nullable();
            $table->string('correo_sujeto_retencion')->nullable();
            $table->string('estado_retencion')->nullable();
            $table->string('efecto_gestion_retencion')->nullable();
            $table->string('recaudo_retencion')->nullable();
            $table->string('relacion_recaudo_reportado')->nullable();
            $table->string('eliminada')->nullable();
            $table->string('tipo_cartera')->nullable();
            $table->string('dias_ini')->nullable();
            $table->string('dias_ed')->nullable();
            $table->string('recaudo')->nullable();

            // Bloqueos de sección
            $table->boolean('is_section1_locked')->default(false);
            $table->boolean('is_section2_locked')->default(false);
            $table->boolean('is_abonos_locked')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retencions');
    }
};
