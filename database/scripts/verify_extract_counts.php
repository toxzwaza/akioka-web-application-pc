<?php

$base = __DIR__.'/../schema_extracted/';
$c = file_get_contents($base.'01_creates.sql');
preg_match_all('/CREATE TABLE `[^`]+`[\s\S]*?\) ENGINE=InnoDB[^;]*;/', $c, $m);
echo 'creates: '.count($m[0]).PHP_EOL;

foreach (['02_index_alters.sql', '03_auto_increment_alters.sql', '04_foreign_key_alters.sql'] as $f) {
    $c = file_get_contents($base.$f);
    preg_match_all('/ALTER TABLE `[^`]+`[\s\S]*?;/', $c, $m);
    echo $f.': '.count($m[0]).PHP_EOL;
}
