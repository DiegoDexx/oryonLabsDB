<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_fields')) {
            return;
        }

        Schema::create('project_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->default(1);
            $table->string('category');
            $table->string('field_name');
            $table->string('label');
            $table->string('type');
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_fields');
    }
};
