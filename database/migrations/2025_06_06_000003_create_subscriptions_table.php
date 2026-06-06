<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['starter', 'pro', 'professional', 'voice_ai']);
            $table->decimal('setup_fee', 8, 2);
            $table->decimal('monthly_fee', 8, 2);
            $table->enum('status', ['active', 'paused', 'cancelled', 'pending'])->default('pending');
            $table->date('start_date');
            $table->date('next_billing_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
