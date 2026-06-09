<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 // database/migrations/2026_06_09_000001_add_language_to_leads_and_clients.php

        public function up(): void
        {
            // Añadir language a leads si no existe
            if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'language')) {
                Schema::table('leads', function (Blueprint $table) {
                    $table->string('language', 5)->default('es')->after('urgency');
                });
            }

            // Añadir language a clients si no existe
            if (Schema::hasTable('clients') && !Schema::hasColumn('clients', 'language')) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->string('language', 5)->default('es')->after('status');
                });
            }
        }

        public function down(): void
        {
            if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'language')) {
                Schema::table('leads', function (Blueprint $table) {
                    $table->dropColumn('language');
                });
            }
            if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'language')) {
                Schema::table('clients', function (Blueprint $table) {
                    $table->dropColumn('language');
                });
            }
        }
};
