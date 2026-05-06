<?php
// arquivo: sync_migrations_history.php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob(database_path('migrations') . '/*.php') ?: [];
sort($files);

$allMigrations = array_map(
    fn($f) => pathinfo($f, PATHINFO_FILENAME),
    $files
);

$existing = DB::table('migrations')->pluck('migration')->all();
$existingMap = array_fill_keys($existing, true);
$nextBatch = ((int) (DB::table('migrations')->max('batch') ?? 0)) + 1;

$toInsert = [];
foreach ($allMigrations as $migration) {
    if (!isset($existingMap[$migration])) {
        $toInsert[] = [
            'migration' => $migration,
            'batch' => $nextBatch,
        ];
    }
}

if (!empty($toInsert)) {
    DB::table('migrations')->insert($toInsert);
}

echo "Batch: {$nextBatch}\n";
echo "Inseridas: " . count($toInsert) . "\n";
