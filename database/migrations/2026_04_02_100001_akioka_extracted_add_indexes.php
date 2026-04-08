<?php

use Illuminate\Database\Migrations\Migration;

require_once __DIR__.'/../schema_extracted/sql_runner.php';

return new class extends Migration
{
    public function up(): void
    {
        akioka_run_regex_sql_file(
            'schema_extracted/02_index_alters.sql',
            '/ALTER TABLE `[^`]+`[\s\S]*?;/'
        );
    }

    public function down(): void
    {
        // Irreversible without storing previous state; full rollback drops tables in 100000.
    }
};
