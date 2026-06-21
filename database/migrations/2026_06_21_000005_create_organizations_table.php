<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('plan', 20)->default('starter');
            $table->string('business_model', 20)->default('project_based');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the agency org so all existing rows (organization_id = 1) resolve correctly.
        DB::table('organizations')->insert([
            'id'             => 1,
            'name'           => 'Oryon Labs',
            'slug'           => 'oryon-labs',
            'plan'           => 'professional',
            'business_model' => 'project_based',
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
