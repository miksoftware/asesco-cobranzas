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
            $table->string('no_consecutivo')->nullable()->after('id');
        });

        // Migrar valores previos tipo CJ-xxxxxx a no_consecutivo
        \Illuminate\Support\Facades\DB::table('cobro_juridicos')
            ->whereNotNull('no_radicado')
            ->where('no_radicado', 'like', 'CJ-%')
            ->update([
                'no_consecutivo' => \Illuminate\Support\Facades\DB::raw('no_radicado'),
                'no_radicado' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobro_juridicos', function (Blueprint $table) {
            $table->dropColumn('no_consecutivo');
        });
    }
};
