<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('voice_call_logs')) {
            Schema::create('voice_call_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->nullable()->index();
                $table->foreignId('lead_id')->nullable();
                $table->string('vapi_call_id')->unique();
                $table->integer('duration_seconds')->default(0);
                $table->decimal('cost_usd', 10, 4)->default(0);
                $table->string('ended_reason')->nullable();
                $table->text('summary')->nullable();
                $table->string('recording_url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_call_logs');
    }
};
