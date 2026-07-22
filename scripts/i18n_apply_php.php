<?php

$root = dirname(__DIR__);
$paths = [$root . '/app', $root . '/routes'];
$ptBrJson = $root . '/lang/pt_BR.json';
$ptBr = file_exists($ptBrJson) ? json_decode(file_get_contents($ptBrJson), true) : [];
if (!is_array($ptBr)) {
    $ptBr = [];
}

function php_i18n_key(string $text): string
{
    $text = preg_replace('/\s+/u', ' ', html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    return trim($text);
}

function php_i18n_expr(string $text): string
{
    $key = str_replace(['\\', "'"], ['\\\\', "\\'"], php_i18n_key($text));
    return "__('{$key}')";
}

function php_i18n_keep(string $text): bool
{
    $key = php_i18n_key($text);
    if ($key === '' || mb_strlen($key) < 2) {
        return false;
    }

    if (preg_match('/[\$@{};]/u', $key)) {
        return false;
    }

    return (bool) preg_match('/[\p{L}]/u', $key);
}

function filesFrom(string $path): Generator
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        yield $file->getPathname();
    }
}

$changedFiles = 0;
$changedStrings = 0;

foreach ($paths as $path) {
    foreach (filesFrom($path) as $file) {
        $original = file_get_contents($file);
        $content = $original;
        $localChanges = 0;

        $patterns = [
            '/(->with\(\s*[\'\"](?:success|error|warning|status)[\'\"]\s*,\s*)([\'\"])(.*?[\p{L}].*?)\2/us',
            '/(flash\(\s*[\'\"](?:success|error|warning|status)[\'\"]\s*,\s*)([\'\"])(.*?[\p{L}].*?)\2/us',
            '/(\$fail\(\s*)([\'\"])(.*?[\p{L}].*?)\2/us',
        ];

        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function ($match) use (&$ptBr, &$localChanges) {
                $prefix = $match[1];
                $text = $match[3];

                if (str_contains($text, "__('") || str_contains($text, '__("') || !php_i18n_keep($text)) {
                    return $match[0];
                }

                $key = php_i18n_key($text);
                $ptBr[$key] ??= $key;
                $localChanges++;

                return $prefix . php_i18n_expr($key);
            }, $content);
        }

        if ($content !== $original) {
            file_put_contents($file, $content);
            $changedFiles++;
            $changedStrings += $localChanges;
        }
    }
}

ksort($ptBr, SORT_NATURAL | SORT_FLAG_CASE);
file_put_contents($ptBrJson, json_encode($ptBr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

echo "Arquivos PHP alterados: {$changedFiles}" . PHP_EOL;
echo "Mensagens convertidas: {$changedStrings}" . PHP_EOL;
