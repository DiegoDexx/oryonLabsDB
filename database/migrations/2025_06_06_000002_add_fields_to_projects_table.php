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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('stage', [
                'lead', 'contacted', 'proposal', 'negotiation',
                'onboarding', 'active', 'closed_won', 'closed_lost'
            ])->default('lead')->after('category');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('stage');
            $table->date('estimated_delivery')->nullable()->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['stage', 'priority', 'estimated_delivery']);
        });
    }
};
