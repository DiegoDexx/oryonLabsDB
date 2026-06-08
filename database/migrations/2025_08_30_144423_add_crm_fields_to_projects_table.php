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
                $table->text('commercial_summary')->nullable()->after('estimated_delivery');
            }
            if (!Schema::hasColumn('projects', 'canal')) {
                $table->string('channel')->nullable()->after('commercial_summary');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('projects')) {
            return;
        }
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'commercial_summary')) {
                $table->dropColumn('commercial_summary');
            }
            if (Schema::hasColumn('projects', 'channel')) {
                $table->dropColumn('channel');
            }
        });
    }
};
