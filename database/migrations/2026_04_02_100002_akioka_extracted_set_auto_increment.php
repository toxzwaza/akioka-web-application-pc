<?php

use Illuminate\Database\Migrations\Migration;

require_once __DIR__.'/../schema_extracted/sql_runner.php';

return new class extends Migration
{
    public function up(): void
    {
        akioka_run_regex_sql_file(
            'schema_extracted/03_auto_increment_alters.sql',
            '/ALTER TABLE `[^`]+`[\s\S]*?;/'
        );
    }

    public function down(): void
    {
        // Irreversible; full rollback drops tables in 100000.
    }
};
