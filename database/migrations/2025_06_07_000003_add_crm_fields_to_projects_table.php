<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'resumen_comercial')) {
                $table->text('resumen_comercial')->nullable()->after('estimated_delivery');
            }
            if (!Schema::hasColumn('projects', 'canal')) {
                $table->string('canal')->nullable()->after('resumen_comercial');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['resumen_comercial', 'canal']);
        });
    }
};
