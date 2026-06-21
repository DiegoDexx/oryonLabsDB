<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('activities')) {
            return;
        }

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->default(1);
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['call', 'email', 'whatsapp', 'meeting', 'note', 'system']);
            $table->text('description');
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
