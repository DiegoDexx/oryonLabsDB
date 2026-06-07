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

            // Contact data
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');

            // Entry channel
            $table->enum('channel', [
                'form',
                'chatbot',
                'whatsapp',
                'phone',
                'email',
                'instagram',
                'tikTok',
                'other',
            ])->default('form');

            // Lead context
            $table->string('challenge')->nullable();
            $table->string('client_volume')->nullable();
            $table->string('tools')->nullable();
            $table->string('urgency')->nullable();

            // AI-enriched fields
            $table->string('category')->nullable();
            $table->string('project_name')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('suggested_plan')->nullable();
            $table->text('commercial_summary')->nullable();

            // Lead status
            $table->enum('status', [
                'new',
                'contacted',
                'qualified',
                'converted',
                'discarded',
            ])->default('new');

            // Puntuación del lead (0-100) para scoring automático
            $table->integer('lead_score')->default(0);

            // Siguiente acción recomendada por la IA
            $table->string('next_action')->nullable();

            // Tags para segmentación
            $table->string('tags')->nullable(); // "startup,saas,urgente"

            // UTM para tracking de origen de marketing
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();

            // Fecha de último contacto
            $table->timestamp('last_contacted_at')->nullable();

            // Quién gestiona el lead (para cuando tengas equipo)
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
                    

            // Manual sales notes
            $table->text('notes')->nullable();

            // Relations after conversion
            $table->foreignId('client_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
            $table->foreignId('project_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
