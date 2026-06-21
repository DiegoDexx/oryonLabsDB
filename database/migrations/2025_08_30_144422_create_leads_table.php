<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads')) {
            return;
        }

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->default(1);

            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->enum('channel', [
                'form', 'chatbot', 'whatsapp', 'phone',
                'email', 'instagram', 'tikTok', 'other',
            ])->default('form');

            $table->string('challenge')->nullable();
            $table->string('client_volume')->nullable();
            $table->string('tools')->nullable();
            $table->string('urgency')->nullable();

            $table->string('category')->nullable();
            $table->string('project_name')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('suggested_plan')->nullable();
            $table->text('commercial_summary')->nullable();

            $table->enum('status', [
                'new', 'contacted', 'qualified', 'converted', 'discarded',
            ])->default('new');

            $table->integer('lead_score')->default(0);
            $table->string('next_action')->nullable();
            $table->string('tags')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamp('last_contacted_at')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->text('notes')->nullable();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
