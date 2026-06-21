<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!$this->indexExists('leads', 'leads_email_index')) {
                $table->index('email', 'leads_email_index');
            }
            if (!$this->indexExists('leads', 'leads_phone_index')) {
                $table->index('phone', 'leads_phone_index');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if (!$this->indexExists('clients', 'clients_email_index')) {
                $table->index('email', 'clients_email_index');
            }
            if (!$this->indexExists('clients', 'clients_phone_index')) {
                $table->index('phone', 'clients_phone_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if ($this->indexExists('leads', 'leads_email_index')) {
                $table->dropIndex('leads_email_index');
            }
            if ($this->indexExists('leads', 'leads_phone_index')) {
                $table->dropIndex('leads_phone_index');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if ($this->indexExists('clients', 'clients_email_index')) {
                $table->dropIndex('clients_email_index');
            }
            if ($this->indexExists('clients', 'clients_phone_index')) {
                $table->dropIndex('clients_phone_index');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = Schema::getConnection()->select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
