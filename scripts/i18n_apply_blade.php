<?php

$root = dirname(__DIR__);
$viewsPath = $root . '/resources/views';
$ptBrJson = $root . '/lang/pt_BR.json';
$ptBr = file_exists($ptBrJson) ? json_decode(file_get_contents($ptBrJson), true) : [];
if (!is_array($ptBr)) {
    $ptBr = [];
}

$ignorePatterns = [
    '/^\s*$/u',
    '/^[0-9\s\-\/\.\:\(\)]+$/u',
    '/^https?:\/\//i',
    '/[\$@{};]/u',
    '/\s[+.]=?\s/u',
    '/(?:->|::|=>|&&|\|\|)/u',
    '/\b(?:let|const|var|function|return|foreach|endforeach|endif|if|else|response|request|route|asset|old|csrf|class|style)\b/u',
];

function normalize_i18n_text(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

function should_translate_i18n(string $text, array $ignorePatterns): bool
{
    $text = normalize_i18n_text($text);
    if ($text === '' || mb_strlen($text) < 2) {
        return false;
    }

    foreach ($ignorePatterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return false;
        }
    }

    return (bool) preg_match('/[\p{L}]/u', $text);
}

function blade_translation_expr(string $text): string
{
    $key = normalize_i18n_text($text);
    $key = str_replace(['\\', "'"], ['\\\\', "\\'"], $key);

    return "{{ __('{$key}') }}";
}

function protect_blocks(string $content, array &$blocks): string
{
    return preg_replace_callback('/<(script|style)\b[^>]*>.*?<\/\1>/isu', function ($match) use (&$blocks) {
        $token = '___I18N_BLOCK_' . count($blocks) . '___';
        $blocks[$token] = $match[0];
        return $token;
    }, $content);
}

function restore_blocks(string $content, array $blocks): string
{
    return strtr($content, $blocks);
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$changedFiles = 0;
$changedStrings = 0;

foreach ($iterator as $file) {
    if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $original = file_get_contents($path);
    $blocks = [];
    $content = protect_blocks($original, $blocks);
    $localChanges = 0;

    $content = preg_replace_callback('/\b(placeholder|title|alt|aria-label)="([^"]*[\p{L}][^"]*)"/u', function ($match) use ($ignorePatterns, &$ptBr, &$localChanges) {
        $attribute = $match[1];
        $value = $match[2];
        if (!should_translate_i18n($value, $ignorePatterns)) {
            return $match[0];
        }

        $key = normalize_i18n_text($value);
        $ptBr[$key] ??= $key;
        $localChanges++;

        return $attribute . '="' . blade_translation_expr($key) . '"';
    }, $content);

    $content = preg_replace_callback('/>([^<>{}@]*[\p{L}][^<>{}]*)</u', function ($match) use ($ignorePatterns, &$ptBr, &$localChanges) {
        $full = $match[0];
        $text = $match[1];
        if (!should_translate_i18n($text, $ignorePatterns)) {
            return $full;
        }

        preg_match('/^\s*/u', $text, $leading);
        preg_match('/\s*$/u', $text, $trailing);
        $key = normalize_i18n_text($text);
        $ptBr[$key] ??= $key;
        $localChanges++;

        return '>' . ($leading[0] ?? '') . blade_translation_expr($key) . ($trailing[0] ?? '') . '<';
    }, $content);

    $content = restore_blocks($content, $blocks);

    if ($content !== $original) {
        file_put_contents($path, $content);
        $changedFiles++;
        $changedStrings += $localChanges;
    }
}

ksort($ptBr, SORT_NATURAL | SORT_FLAG_CASE);
file_put_contents($ptBrJson, json_encode($ptBr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

echo "Arquivos Blade alterados: {$changedFiles}" . PHP_EOL;
echo "Textos convertidos: {$changedStrings}" . PHP_EOL;
