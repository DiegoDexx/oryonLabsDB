<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('llm_usage_logs')) {
            Schema::create('llm_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->nullable()->index();
                $table->foreignId('lead_id')->nullable();
                $table->string('generation_id')->unique();
                $table->string('model');
                $table->integer('prompt_tokens')->default(0);
                $table->integer('completion_tokens')->default(0);
                $table->decimal('cost_usd', 10, 6)->default(0);
                $table->string('channel'); // web | whatsapp | voice
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('llm_usage_logs');
    }
};
