<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leads')) return;

        Schema::table('leads', function (Blueprint $table) {

            // Rename columns Spanish → English (skip if already renamed)
            if (Schema::hasColumn('leads', 'nombre'))
                $table->renameColumn('nombre', 'name');
            if (Schema::hasColumn('leads', 'empresa'))
                $table->renameColumn('empresa', 'company');
            if (Schema::hasColumn('leads', 'telefono'))
                $table->renameColumn('telefono', 'phone');
            if (Schema::hasColumn('leads', 'canal'))
                $table->renameColumn('canal', 'channel');
            if (Schema::hasColumn('leads', 'reto'))
                $table->renameColumn('reto', 'challenge');
            if (Schema::hasColumn('leads', 'volumen_clientes'))
                $table->renameColumn('volumen_clientes', 'client_volume');
            if (Schema::hasColumn('leads', 'herramientas'))
                $table->renameColumn('herramientas', 'tools');
            if (Schema::hasColumn('leads', 'urgencia'))
                $table->renameColumn('urgencia', 'urgency');
            if (Schema::hasColumn('leads', 'categoria'))
                $table->renameColumn('categoria', 'category');
            if (Schema::hasColumn('leads', 'prioridad'))
                $table->renameColumn('prioridad', 'priority');
            if (Schema::hasColumn('leads', 'plan_sugerido'))
                $table->renameColumn('plan_sugerido', 'suggested_plan');
            if (Schema::hasColumn('leads', 'resumen_comercial'))
                $table->renameColumn('resumen_comercial', 'commercial_summary');
            if (Schema::hasColumn('leads', 'notas'))
                $table->renameColumn('notas', 'notes');

            // Add new columns if they don't exist yet
            if (!Schema::hasColumn('leads', 'lead_score'))
                $table->integer('lead_score')->default(0)->after('commercial_summary');
            if (!Schema::hasColumn('leads', 'next_action'))
                $table->string('next_action')->nullable()->after('lead_score');
            if (!Schema::hasColumn('leads', 'tags'))
                $table->string('tags')->nullable()->after('next_action');
            if (!Schema::hasColumn('leads', 'utm_source'))
                $table->string('utm_source')->nullable()->after('tags');
            if (!Schema::hasColumn('leads', 'utm_campaign'))
                $table->string('utm_campaign')->nullable()->after('utm_source');
            if (!Schema::hasColumn('leads', 'last_contacted_at'))
                $table->timestamp('last_contacted_at')->nullable()->after('utm_campaign');
            if (!Schema::hasColumn('leads', 'assigned_to'))
                $table->foreignId('assigned_to')
                      ->nullable()
                      ->after('last_contacted_at')
                      ->constrained('users')
                      ->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('leads')) return;

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'assigned_to'))
                $table->dropForeign(['assigned_to']);
            $table->dropColumn(array_filter([
                Schema::hasColumn('leads', 'lead_score')       ? 'lead_score'       : null,
                Schema::hasColumn('leads', 'next_action')      ? 'next_action'      : null,
                Schema::hasColumn('leads', 'tags')             ? 'tags'             : null,
                Schema::hasColumn('leads', 'utm_source')       ? 'utm_source'       : null,
                Schema::hasColumn('leads', 'utm_campaign')     ? 'utm_campaign'     : null,
                Schema::hasColumn('leads', 'last_contacted_at')? 'last_contacted_at': null,
                Schema::hasColumn('leads', 'assigned_to')      ? 'assigned_to'      : null,
            ]));

            if (Schema::hasColumn('leads', 'name'))               $table->renameColumn('name',               'nombre');
            if (Schema::hasColumn('leads', 'company'))            $table->renameColumn('company',            'empresa');
            if (Schema::hasColumn('leads', 'phone'))              $table->renameColumn('phone',              'telefono');
            if (Schema::hasColumn('leads', 'channel'))            $table->renameColumn('channel',            'canal');
            if (Schema::hasColumn('leads', 'challenge'))          $table->renameColumn('challenge',          'reto');
            if (Schema::hasColumn('leads', 'client_volume'))      $table->renameColumn('client_volume',      'volumen_clientes');
            if (Schema::hasColumn('leads', 'tools'))              $table->renameColumn('tools',              'herramientas');
            if (Schema::hasColumn('leads', 'urgency'))            $table->renameColumn('urgency',            'urgencia');
            if (Schema::hasColumn('leads', 'category'))           $table->renameColumn('category',           'categoria');
            if (Schema::hasColumn('leads', 'priority'))           $table->renameColumn('priority',           'prioridad');
            if (Schema::hasColumn('leads', 'suggested_plan'))     $table->renameColumn('suggested_plan',     'plan_sugerido');
            if (Schema::hasColumn('leads', 'commercial_summary')) $table->renameColumn('commercial_summary', 'resumen_comercial');
            if (Schema::hasColumn('leads', 'notes'))              $table->renameColumn('notes',              'notas');
        });
    }
};