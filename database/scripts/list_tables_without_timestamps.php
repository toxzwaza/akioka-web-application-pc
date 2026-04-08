<?php

$c = file_get_contents(__DIR__.'/../schema_extracted/01_creates.sql');
preg_match_all('/CREATE TABLE `([^`]+)`([\s\S]*?)\) ENGINE=InnoDB[^;]*;/', $c, $m, PREG_SET_ORDER);
$noTs = [];
foreach ($m as $row) {
    $name = $row[1];
    $body = $row[2];
    if (! preg_match('/`created_at`/', $body) || ! preg_match('/`updated_at`/', $body)) {
        $noTs[] = $name;
    }
}
echo implode("\n", $noTs);
