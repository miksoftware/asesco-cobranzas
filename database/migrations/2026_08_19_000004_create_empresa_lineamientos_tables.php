<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lineamientos de negociación
        Schema::create('empresa_lineamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->enum('tipo', ['porcentaje', 'tramo']); // Tipo de lineamiento
            $table->string('concepto'); // Capital, Interés Corriente, Interés Mora, Otros
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Valores de lineamientos por porcentaje
        Schema::create('empresa_lineamiento_porcentajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lineamiento_id')->constrained('empresa_lineamientos')->onDelete('cascade');
            $table->decimal('porcentaje_vigente', 5, 2)->default(0);
            $table->decimal('porcentaje_castigado', 5, 2)->default(0);
            $table->timestamps();
        });

        // Valores de lineamientos por tramo
        Schema::create('empresa_lineamiento_tramos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lineamiento_id')->constrained('empresa_lineamientos')->onDelete('cascade');
            $table->string('nombre_tramo'); // Ej: "2. 1 a 30"
            $table->enum('tipo_cartera', ['vigente', 'castigado']);
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_lineamiento_tramos');
        Schema::dropIfExists('empresa_lineamiento_porcentajes');
        Schema::dropIfExists('empresa_lineamientos');
    }
};
