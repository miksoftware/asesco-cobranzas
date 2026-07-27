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
        Schema::create('retencion_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retencion_id')->constrained('retencions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('seccion'); // ej: 'Datos Generales', 'Abonos', 'Gestiones'
            $table->string('accion');  // ej: 'CREADO', 'EDITADO', 'ELIMINADO'
            $table->string('campo')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retencion_histories');
    }
};
