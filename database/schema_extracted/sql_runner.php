<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

if (! function_exists('akioka_run_regex_sql_file')) {
    /**
     * Execute SQL statements matched by PCRE (one execution per match).
     *
     * @param  string  $relativePath  Path relative to database/ (e.g. schema_extracted/01_creates.sql)
     */
    function akioka_run_regex_sql_file(string $relativePath, string $pattern): void
    {
        $path = database_path($relativePath);
        if (! is_file($path)) {
            throw new RuntimeException("Missing SQL file: {$path}");
        }
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("Cannot read: {$path}");
        }
        if (! preg_match_all($pattern, $content, $matches)) {
            return;
        }
        foreach ($matches[0] as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                DB::unprepared($statement);
            }
        }
    }
}
