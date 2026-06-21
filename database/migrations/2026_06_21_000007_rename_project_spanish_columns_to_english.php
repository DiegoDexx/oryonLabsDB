<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 2025_08_30_144423 checks for 'resumen_comercial' but adds 'commercial_summary',
// meaning fresh installs get the English name while existing DBs keep the Spanish name.
// This migration normalises existing DBs to the English column names so the model works
// in both cases.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'resumen_comercial')) {
                $table->renameColumn('resumen_comercial', 'commercial_summary');
            }
            if (Schema::hasColumn('projects', 'canal')) {
                $table->renameColumn('canal', 'channel');
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
                $table->renameColumn('commercial_summary', 'resumen_comercial');
            }
            if (Schema::hasColumn('projects', 'channel')) {
                $table->renameColumn('channel', 'canal');
            }
        });
    }
};
