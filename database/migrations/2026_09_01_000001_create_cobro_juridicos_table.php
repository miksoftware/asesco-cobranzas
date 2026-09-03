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
        Schema::create('cobro_juridicos', function (Blueprint $table) {
            $table->id();

            // Sección 1: Datos Generales del Proceso
            $table->string('estado_proceso')->nullable();
            $table->string('departamento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('especialidad')->nullable();
            $table->string('juzgado_conocimiento')->nullable();
            $table->string('no_radicado')->nullable();

            // Sección 2 - Pestaña 1: Datos Generales
            $table->string('demandado_1')->nullable();
            $table->string('demandado_2')->nullable();
            $table->string('demandado_3')->nullable();
            $table->string('demandado_4')->nullable();
            $table->date('fecha_etapa')->nullable();
            $table->string('etapa_procesal')->nullable();
            $table->date('fecha_actividad')->nullable();
            $table->string('actividad')->nullable();
            $table->string('concepto_viabilidad_juridica')->nullable();
            $table->string('garantias_juridica')->nullable();
            $table->text('anotacion_abogado')->nullable();

            // Valor del proceso para cálculos de saldo en depósitos
            $table->decimal('valor_total_proceso', 15, 2)->nullable();

            // Bloqueos de sección para auditoría
            $table->boolean('is_section1_locked')->default(false);
            $table->boolean('is_section2_locked')->default(false);
            $table->boolean('is_depositos_locked')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobro_juridicos');
    }
};
