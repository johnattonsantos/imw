<?php

$root = dirname(__DIR__);
$viewsPath = $root . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$changedFiles = 0;
$changedBlocks = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);
    $original = $content;

    foreach (['script', 'style'] as $tag) {
        $pattern = '/\{\{\s*__\(\'((?:<' . $tag . '\b[\s\S]*?<\/' . $tag . '>\s*)+)\'\)\s*\}\}/i';
        $content = preg_replace_callback($pattern, function ($match) use (&$changedBlocks) {
            $changedBlocks++;
            return $match[1];
        }, $content);
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changedFiles++;
    }
}

echo "Arquivos corrigidos: {$changedFiles}" . PHP_EOL;
echo "Blocos script/style corrigidos: {$changedBlocks}" . PHP_EOL;
