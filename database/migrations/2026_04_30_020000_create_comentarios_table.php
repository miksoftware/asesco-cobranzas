<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');                                // Fecha del comentario
            $table->string('hora', 20)->nullable();               // Hora del comentario
            $table->string('gestor');                              // Nombre del gestor
            $table->text('comentario');                            // Texto del comentario
            $table->string('canal')->nullable();                   // Canal (GESTOR, etc.)
            $table->string('tipo_contacto')->nullable();           // Tipo de contacto
            $table->string('efecto_gestion')->nullable();          // Efecto de gestión
            $table->string('accion_cobro')->nullable();            // Acción de cobro
            $table->string('cedula', 20)->index();                 // Cédula del titular
            $table->string('nombre');                              // Nombre del titular
            $table->string('empresa');                             // Empresa
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            // Índice para relación con terceros
            $table->index(['cedula', 'empresa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
