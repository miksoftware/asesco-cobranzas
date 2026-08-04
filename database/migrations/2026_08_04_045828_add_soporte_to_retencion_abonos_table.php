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
        Schema::table('retencion_abonos', function (Blueprint $table) {
            $table->string('soporte')->nullable()->after('aplicado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retencion_abonos', function (Blueprint $table) {
            $table->dropColumn('soporte');
        });
    }
};
