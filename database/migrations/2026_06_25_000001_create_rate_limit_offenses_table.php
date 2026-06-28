<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rate_limit_offenses')) {
            Schema::create('rate_limit_offenses', function (Blueprint $table) {
                $table->id();
                $table->string('phone')->unique();
                $table->unsignedInteger('violation_count')->default(0);
                $table->timestamp('blocked_until')->nullable();
                $table->timestamp('last_violation_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_limit_offenses');
    }
};
