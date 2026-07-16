<?php

$root = dirname(__DIR__);
$viewsPath = $root . '/resources/views';

function decode_blade_code_fragment(string $value): string
{
    return str_replace(["\\'", "\\\\"], ["'", "\\"], $value);
}

function unwrap_code_translations(string $line, int &$count): string
{
    $isLogicLine = (bool) preg_match('/@(?:if|elseif|unless|foreach|forelse|while|isset|empty|error|switch|case)\b/', $line);
    $hasObjectFragment = str_contains($line, '->{{ __(');

    if (!$isLogicLine && !$hasObjectFragment) {
        return $line;
    }

    return preg_replace_callback('/\{\{\s*__\(\'((?:\\\'|[^\'])*)\'\)\s*\}\}/', function ($match) use (&$count) {
        $fragment = decode_blade_code_fragment($match[1]);

        $looksLikeCode = preg_match('/[\$()=!<>\[\],]|^\w+\(|^[A-Za-z_][A-Za-z0-9_]*$/', $fragment);
        if (!$looksLikeCode) {
            return $match[0];
        }

        $count++;
        return $fragment;
    }, $line);
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$changedFiles = 0;
$changedFragments = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $lines = file($path);
    $original = implode('', $lines);
    $newLines = [];

    foreach ($lines as $line) {
        $newLines[] = unwrap_code_translations($line, $changedFragments);
    }

    $content = implode('', $newLines);
    if ($content !== $original) {
        file_put_contents($path, $content);
        $changedFiles++;
    }
}

echo "Arquivos corrigidos: {$changedFiles}" . PHP_EOL;
echo "Fragmentos de codigo corrigidos: {$changedFragments}" . PHP_EOL;
