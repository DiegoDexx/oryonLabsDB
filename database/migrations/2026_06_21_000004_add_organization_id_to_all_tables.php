<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tables that must be tenant-scoped. Order matters: users first.
    private array $tables = [
        'users',
        'leads',
        'clients',
        'projects',
        'subscriptions',
        'activities',
        'invoices',
        'project_fields',
        'project_requirements',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'organization_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                // NOT NULL DEFAULT 1 → existing rows are backfilled to org 1 (Oryon Labs)
                // by MySQL when the column is added.
                $blueprint->unsignedBigInteger('organization_id')
                    ->default(1)
                    ->after('id');

                $blueprint->index('organization_id', "idx_{$table}_org_id");
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'organization_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropIndex("idx_{$table}_org_id");
                $blueprint->dropColumn('organization_id');
            });
        }
    }
};
