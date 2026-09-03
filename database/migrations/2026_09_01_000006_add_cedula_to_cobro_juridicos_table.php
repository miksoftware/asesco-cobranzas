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
        Schema::table('cobro_juridicos', function (Blueprint $table) {
            $table->string('cedula')->nullable()->after('no_radicado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobro_juridicos', function (Blueprint $table) {
            $table->dropColumn('cedula');
        });
    }
};
