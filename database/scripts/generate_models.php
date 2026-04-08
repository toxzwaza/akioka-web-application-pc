<?php

use Illuminate\Support\Str;

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = json_decode(file_get_contents(__DIR__.'/../schema_extracted/table_list.json'), true);
sort($tables);

$classOverrides = [
    'data' => 'EnvironmentDatum',
    'project_group_user' => 'ProjectGroupUser',
    'cache' => 'CacheEntry', // avoid clash with Cache facade
    'raspi_data' => 'RaspiData',
];

$softDeleteTables = ['announcements', 'appointments', 'interview_phones'];

$noTimestamps = ['cache', 'failed_jobs', 'password_resets', 'sessions'];

$modelsDir = __DIR__.'/../../app/Models';
if (! is_dir($modelsDir)) {
    mkdir($modelsDir, 0755, true);
}

$tableToClass = [];
foreach ($tables as $table) {
    $tableToClass[$table] = $classOverrides[$table] ?? Str::studly(Str::singular($table));
}

foreach ($tables as $table) {
    $class = $tableToClass[$table];
    if ($class === 'User') {
        continue; // written manually
    }

    $uses = [];
    $traits = [];
    $body = '';

    if (in_array($table, $softDeleteTables, true)) {
        $uses[] = 'Illuminate\Database\Eloquent\SoftDeletes';
        $traits[] = 'SoftDeletes';
    }

    $useTimestamps = ! in_array($table, $noTimestamps, true);

    $tableProp = '';
    if ($table === 'groups' || in_array($class, ['EnvironmentDatum', 'CacheEntry', 'ProjectGroupUser', 'DeliveryInitialOrder'], true)) {
        // delivery_initial_order -> DeliveryInitialOrder from singular
    }
    // Explicit $table for non-conventional names
    if ($class === 'Group' || $class === 'EnvironmentDatum' || $class === 'CacheEntry' || $class === 'ProjectGroupUser' || $class === 'DeliveryInitialOrder') {
        $tableProp = "\n    protected \$table = '{$table}';\n";
    }

    $imports = "namespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n";
    foreach ($uses as $u) {
        $imports .= "use {$u};\n";
    }
    $imports .= "\n";

    $traitLine = $traits ? "\n    use ".implode(', ', $traits).";\n" : '';

    $tsLine = $useTimestamps ? '' : "\n    public \$timestamps = false;\n";

    $php = <<<PHP
<?php

{$imports}class {$class} extends Model
{{$traitLine}{$tableProp}{$tsLine}
    protected \$guarded = [];
}

PHP;

    file_put_contents($modelsDir.'/'.$class.'.php', $php);
}

echo 'Generated models (except User): '.(count($tables) - 1).PHP_EOL;
