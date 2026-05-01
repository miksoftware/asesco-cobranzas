<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->foreignId('modified_by')->nullable()->after('fuente')->constrained('users')->nullOnDelete();
            $table->timestamp('modified_at')->nullable()->after('modified_by');
        });
    }

    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['modified_by', 'modified_at']);
        });
    }
};
