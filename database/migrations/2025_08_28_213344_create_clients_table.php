<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clients')) {
            return;
        }

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->default(1);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('status', ['active', 'inactive', 'churned'])->default('active');
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
