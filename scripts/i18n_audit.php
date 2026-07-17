<?php

$root = dirname(__DIR__);
$paths = [
    $root . '/resources/views',
    $root . '/app',
    $root . '/routes',
];

$ignorePatterns = [
    '/^\s*$/u',
    '/^[0-9\s\-\/\.\:\(\)]+$/u',
    '/^https?:\/\//i',
    '/^#[0-9A-Fa-f]{3,8}$/',
    '/^[A-Za-z0-9_\-\.\/]+\.(js|css|png|jpg|jpeg|svg|gif|pdf|xlsx?)$/i',
    '/^fa[srb]?\s/u',
    '/^col-/u',
    '/^btn-/u',
    '/^form-/u',
    '/[\$@{};]/u',
    '/\s[+.]=?\s/u',
    '/(?:->|::|=>|&&|\|\|)/u',
    '/\b(?:let|const|var|function|return|foreach|endforeach|endif|if|else|response|request|route|asset|old|csrf|class|style)\b/u',
];

function shouldKeep(string $text, array $ignorePatterns): bool
{
    $text = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

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

function normalizeText(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

function filesFrom(string $path): Generator
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $pathname = $file->getPathname();
        if (!preg_match('/\.(blade\.php|php)$/', $pathname)) {
            continue;
        }

        yield $pathname;
    }
}

$strings = [];

foreach ($paths as $path) {
    foreach (filesFrom($path) as $file) {
        $relative = str_replace($root . '/', '', $file);
        $content = file_get_contents($file);

        if (str_ends_with($file, '.blade.php')) {
            $scanContent = preg_replace('/<script\b[^>]*>.*?<\/script>/isu', '', $content);
            $scanContent = preg_replace('/<style\b[^>]*>.*?<\/style>/isu', '', $scanContent);

            if (preg_match_all('/>([^<>{}@]*[\p{L}][^<>{}]*)</u', $scanContent, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as [$match, $offset]) {
                    $text = normalizeText($match);
                    if (shouldKeep($text, $ignorePatterns)) {
                        $line = substr_count(substr($scanContent ?? $content, 0, $offset), "\n") + 1;
                        $strings[$text]['locations'][] = $relative . ':' . $line;
                    }
                }
            }

            if (preg_match_all('/\b(?:placeholder|title|alt|aria-label)="([^"]*[\p{L}][^"]*)"/u', $scanContent, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as [$match, $offset]) {
                    $text = normalizeText($match);
                    if (shouldKeep($text, $ignorePatterns)) {
                        $line = substr_count(substr($scanContent ?? $content, 0, $offset), "\n") + 1;
                        $strings[$text]['locations'][] = $relative . ':' . $line;
                    }
                }
            }
        }

        if (preg_match_all('/(?:with\(\s*[\'\"](?:success|error|warning|status)[\'\"]\s*,\s*|flash\(\s*[\'\"](?:success|error|warning|status)[\'\"]\s*,\s*|\$fail\(\s*)([\'\"])([^\1]*?[\p{L}][^\1]*?)\1/u', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[2] as [$match, $offset]) {
                $text = normalizeText($match);
                if (shouldKeep($text, $ignorePatterns)) {
                    $line = substr_count(substr($scanContent ?? $content, 0, $offset), "\n") + 1;
                    $strings[$text]['locations'][] = $relative . ':' . $line;
                }
            }
        }
    }
}

ksort($strings, SORT_NATURAL | SORT_FLAG_CASE);

$catalog = [];
foreach ($strings as $text => $data) {
    $catalog[$text] = [
        'pt_BR' => $text,
        'locations' => array_values(array_unique($data['locations'])),
    ];
}

file_put_contents($root . '/storage/app/i18n/static-strings.json', json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

$ptBrJson = $root . '/lang/pt_BR.json';
$ptBr = file_exists($ptBrJson) ? json_decode(file_get_contents($ptBrJson), true) : [];
if (!is_array($ptBr)) {
    $ptBr = [];
}
foreach (array_keys($catalog) as $text) {
    $ptBr[$text] ??= $text;
}
ksort($ptBr, SORT_NATURAL | SORT_FLAG_CASE);
file_put_contents($ptBrJson, json_encode($ptBr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

echo 'Frases estaticas encontradas: ' . count($catalog) . PHP_EOL;
echo 'Relatorio: storage/app/i18n/static-strings.json' . PHP_EOL;
echo 'Catalogo pt_BR atualizado: lang/pt_BR.json' . PHP_EOL;
