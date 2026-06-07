<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'stage')) {
                    $table->enum('stage', [
                        'lead', 'contacted', 'proposal', 'negotiation',
                        'onboarding', 'active', 'closed_won', 'closed_lost'
                    ])->default('lead')->after('category');
                }
                if (!Schema::hasColumn('projects', 'priority')) {
                    $table->enum('priority', ['low', 'medium', 'high'])
                        ->default('medium')->after('stage');
                }
                if (!Schema::hasColumn('projects', 'estimated_delivery')) {
                    $table->date('estimated_delivery')->nullable()->after('priority');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn(['stage', 'priority', 'estimated_delivery']);
            });
        }
    }
};