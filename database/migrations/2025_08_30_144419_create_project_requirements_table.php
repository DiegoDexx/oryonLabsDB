<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_requirements')) {
            return;
        }

        Schema::create('project_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->default(1);
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained('project_fields')->onDelete('cascade');
            $table->string('field_value');
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requirements');
    }
};
