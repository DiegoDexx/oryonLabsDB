<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('stage', [
                'lead', 'contacted', 'proposal', 'negotiation',
                'onboarding', 'active', 'closed_won', 'closed_lost'
            ])->default('lead');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('estimated_delivery')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};