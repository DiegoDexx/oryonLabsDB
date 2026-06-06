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
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'company')) {
                    $table->string('company')->nullable()->after('name');
                }
                if (!Schema::hasColumn('clients', 'status')) {
                    $table->enum('status', ['active','inactive','churned'])
                        ->default('active')->after('phone');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn(['company', 'status']);
            });
        }
    }

    
    };