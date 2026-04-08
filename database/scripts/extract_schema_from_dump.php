<?php

/**
 * Extract CREATE TABLE (except migrations) and ALTER blocks from phpMyAdmin dump.
 * Run: php database/scripts/extract_schema_from_dump.php
 */
$path = __DIR__.'/../../sql/akioka_db_20260402.sql';
$outDir = __DIR__.'/../schema_extracted';
if (! is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$f = fopen($path, 'r');
$current = null;
$depth = 0;
$creates = [];

while (($line = fgets($f)) !== false) {
    if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
        $current = ['name' => $m[1], 'lines' => [$line]];
        $depth = substr_count($line, '(') - substr_count($line, ')');

        continue;
    }
    if ($current !== null) {
        $current['lines'][] = $line;
        $depth += substr_count($line, '(') - substr_count($line, ')');
        if ($depth <= 0 && preg_match('/\) ENGINE=/', $line)) {
            $name = $current['name'];
            if ($name !== 'migrations') {
                $creates[$name] = implode('', $current['lines']);
            }
            $current = null;
        }
    }
}
fclose($f);

file_put_contents($outDir.'/01_creates.sql', implode("\n", $creates)."\n");

$all = file_get_contents($path);

$markers = [
    'indexes' => "--\n-- ダンプしたテーブルのインデックス\n--\n",
    'autoinc' => "--\n-- ダンプしたテーブルの AUTO_INCREMENT\n--\n",
    'fkeys' => "--\n-- ダンプしたテーブルの制約\n--\n",
];
$pos = [];
foreach ($markers as $k => $m) {
    $pos[$k] = strpos($all, $m);
    if ($pos[$k] === false) {
        fwrite(STDERR, "Marker not found: $k\n");
        exit(1);
    }
}
$commitPos = strrpos($all, 'COMMIT;');
if ($commitPos === false) {
    fwrite(STDERR, "COMMIT not found\n");
    exit(1);
}

$indexSql = substr($all, $pos['indexes'], $pos['autoinc'] - $pos['indexes']);
$autoSql = substr($all, $pos['autoinc'], $pos['fkeys'] - $pos['autoinc']);
$fkSql = substr($all, $pos['fkeys'], $commitPos - $pos['fkeys']);

$stripMigrationsBlocks = function (string $sql): string {
    return preg_replace(
        '/--\s*\n--\s*テーブルの[^`]*`migrations`[^-]*--\s*\nALTER TABLE `migrations`[^;]*;\s*/s',
        '',
        $sql
    ) ?? $sql;
};

$indexSql = $stripMigrationsBlocks($indexSql);
$autoSql = $stripMigrationsBlocks($autoSql);
// No FK on migrations in dump typically

// Fresh installs: do not pin AUTO_INCREMENT seeds from production
$autoSql = preg_replace('/,\s*AUTO_INCREMENT=\d+/', '', $autoSql) ?? $autoSql;

file_put_contents($outDir.'/02_index_alters.sql', $indexSql);
file_put_contents($outDir.'/03_auto_increment_alters.sql', $autoSql);
file_put_contents($outDir.'/04_foreign_key_alters.sql', $fkSql);

$tableList = array_keys($creates);
sort($tableList);
file_put_contents($outDir.'/table_list.json', json_encode($tableList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo 'Tables (excl. migrations): '.count($creates).PHP_EOL;
echo 'Written to '.$outDir.PHP_EOL;
