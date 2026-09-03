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
        Schema::create('cobro_juridico_depositos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cobro_juridico_id')->constrained('cobro_juridicos')->onDelete('cascade');
            $table->date('fecha_descuento')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->date('fecha_consignacion')->nullable();
            $table->boolean('reportado')->default(false);
            $table->string('soporte')->nullable();
            $table->boolean('aplicado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobro_juridico_depositos');
    }
};
