<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tramos de tarifas (vigente y castigo)
        Schema::create('empresa_tarifas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre_tramo'); // Ej: "1 a 30", "31 a 60"
            $table->integer('dias_desde')->nullable();
            $table->integer('dias_hasta')->nullable();
            $table->decimal('porcentaje_vigente', 5, 2)->default(0);
            $table->decimal('porcentaje_castigada', 5, 2)->default(0);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_tarifas');
    }
};
