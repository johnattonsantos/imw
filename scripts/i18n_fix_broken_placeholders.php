<?php

$root = dirname(__DIR__);
$viewsPath = $root . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$changedFiles = 0;
$changed = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;

    $content = preg_replace_callback('/placeholder="([^"{}]+?)\'\) \}\}"/', function ($match) use (&$changed) {
        $text = trim($match[1]);
        $text = str_replace(["\\", "'"], ["\\\\", "\\'"], $text);
        $changed++;
        return 'placeholder="{{ __(' . "'{$text}'" . ') }}"';
    }, $content);

    $content = preg_replace_callback('/value="\{\{ old\(\'([A-Za-z0-9_\-]+)"/', function ($match) use (&$changed) {
        $changed++;
        return 'value="{{ old(\'' . $match[1] . '\') }}"';
    }, $content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changedFiles++;
    }
}

echo "Arquivos corrigidos: {$changedFiles}" . PHP_EOL;
echo "Trechos corrigidos: {$changed}" . PHP_EOL;
