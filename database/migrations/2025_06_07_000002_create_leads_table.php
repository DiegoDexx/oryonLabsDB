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

            // Datos de contacto
            $table->string('nombre');
            $table->string('empresa')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono');

            // Canal de entrada
            $table->enum('canal', [
                'formulario',
                'chatbot',
                'whatsapp',
                'telefono',
                'manual',
            ])->default('formulario');

            // Contexto del lead
            $table->string('reto')->nullable();
            $table->string('volumen_clientes')->nullable();
            $table->string('herramientas')->nullable();
            $table->string('urgencia')->nullable();

            // Campos enriquecidos por IA
            $table->string('categoria')->nullable();
            $table->string('project_name')->nullable();
            $table->enum('prioridad', ['low', 'medium', 'high'])->default('medium');
            $table->string('plan_sugerido')->nullable();
            $table->text('resumen_comercial')->nullable();

            // Estado del lead
            $table->enum('status', [
                'nuevo',
                'contactado',
                'calificado',
                'convertido',
                'descartado',
            ])->default('nuevo');

            // Notas manuales del comercial
            $table->text('notas')->nullable();

            // Relaciones cuando convierte
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
