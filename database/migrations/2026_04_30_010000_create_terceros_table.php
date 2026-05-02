<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('terceros')) {
            return;
        }

        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            $table->string('referencia', 20)->index();          // Cédula del titular
            $table->string('cedula_tercero', 20)->index();      // Cédula del tercero
            $table->string('nombre_tercero');                    // Nombre completo del tercero
            $table->enum('calidad', ['TT', 'CD']);              // TT = Titular, CD = Codeudor
            $table->string('empresa');                           // Empresa a la que debe
            $table->string('dato');                              // Teléfono, correo, etc.
            $table->string('tipo_dato');                         // celular, fijo, correo, etc.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Quién subió el registro
            $table->timestamps();

            // Índice único para evitar duplicados exactos
            $table->unique(['referencia', 'cedula_tercero', 'empresa', 'dato', 'tipo_dato'], 'terceros_unique_record');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};
