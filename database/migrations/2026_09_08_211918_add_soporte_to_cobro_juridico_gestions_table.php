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
            $table->string('soporte')->nullable()->after('actividad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobro_juridico_gestions', function (Blueprint $table) {
            $table->dropColumn('soporte');
        });
    }
};
