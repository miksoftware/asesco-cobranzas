<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_lineamientos', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('orden');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_lineamientos', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
