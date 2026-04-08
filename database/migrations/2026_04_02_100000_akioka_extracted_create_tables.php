<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/../schema_extracted/sql_runner.php';

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET NAMES utf8mb4');
        DB::statement("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");

        akioka_run_regex_sql_file(
            'schema_extracted/01_creates.sql',
            '/CREATE TABLE `[^`]+`[\s\S]*?\) ENGINE=InnoDB[^;]*;/'
        );
    }

    public function down(): void
    {
        $listPath = database_path('schema_extracted/table_list.json');
        if (! is_file($listPath)) {
            return;
        }
        $tables = json_decode(file_get_contents($listPath), true);
        if (! is_array($tables)) {
            return;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
