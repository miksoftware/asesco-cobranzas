<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eps_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Ej: "ADRES", "Coosalud"
            $table->string('slug')->unique();    // Ej: "adres", "coosalud"
            $table->string('base_url');           // Ej: "http://localhost:8001"
            $table->string('api_token');           // Bearer token de Sanctum
            $table->string('endpoint_path')->default('/api/consulta/cedula/{cedula}');
            $table->integer('timeout')->default(15);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eps_systems');
    }
};
