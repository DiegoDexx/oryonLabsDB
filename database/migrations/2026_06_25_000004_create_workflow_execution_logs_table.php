<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('workflow_execution_logs')) {
            Schema::create('workflow_execution_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->nullable()->index();
                $table->string('n8n_execution_id')->unique();
                $table->string('workflow_name');
                $table->string('status'); // success | error | waiting | running | canceled
                $table->integer('duration_ms')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_execution_logs');
    }
};
