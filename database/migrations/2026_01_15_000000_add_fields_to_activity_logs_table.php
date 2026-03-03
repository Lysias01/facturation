<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->decimal('impact_financier', 15, 2)->nullable()->after('description');
            $table->enum('niveau_gravite', ['info', 'warning', 'error', 'critical'])->default('info')->after('impact_financier');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['impact_financier', 'niveau_gravite']);
        });
    }
};
