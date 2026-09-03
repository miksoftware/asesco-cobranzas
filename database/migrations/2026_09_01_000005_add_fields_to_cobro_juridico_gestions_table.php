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
        Schema::table('cobro_juridico_gestions', function (Blueprint $table) {
            $table->date('fecha_etapa')->nullable()->after('user_id');
            $table->string('etapa_procesal')->nullable()->after('fecha_etapa');
            $table->date('fecha_actividad')->nullable()->after('etapa_procesal');
            $table->string('actividad')->nullable()->after('fecha_actividad');
            $table->timestamp('fecha_gestion')->nullable()->after('actividad');
            $table->text('gestion')->nullable()->after('fecha_gestion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobro_juridico_gestions', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_etapa',
                'etapa_procesal',
                'fecha_actividad',
                'actividad',
                'fecha_gestion',
                'gestion',
            ]);
        });
    }
};
