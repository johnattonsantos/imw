<?php

$root = dirname(__DIR__);
$locales = array_keys(require $root . '/config/locales.php')['supported'] ?? [];
if (!$locales) {
    $config = require $root . '/config/locales.php';
    $locales = array_keys($config['supported']);
}

$baseFile = $root . '/lang/pt_BR.json';
$base = file_exists($baseFile) ? json_decode(file_get_contents($baseFile), true) : [];
if (!is_array($base)) {
    fwrite(STDERR, "Catalogo base pt_BR invalido.\n");
    exit(1);
}

$report = [];
foreach ($locales as $locale) {
    $file = $root . "/lang/{$locale}.json";
    $catalog = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($catalog)) {
        $catalog = [];
    }

    $missing = [];
    foreach (array_keys($base) as $key) {
        if (!array_key_exists($key, $catalog) || trim((string) $catalog[$key]) === '') {
            $missing[] = $key;
        }
    }

    $report[$locale] = [
        'total_base' => count($base),
        'missing' => count($missing),
        'items' => $missing,
    ];
}

file_put_contents($root . '/storage/app/i18n/missing-translations.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

foreach ($report as $locale => $data) {
    echo $locale . ': ' . $data['missing'] . ' pendentes de ' . $data['total_base'] . PHP_EOL;
}

echo 'Relatorio: storage/app/i18n/missing-translations.json' . PHP_EOL;
