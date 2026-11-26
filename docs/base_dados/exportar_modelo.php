<?php

/** Exporta o snapshot SVG existente para PDF vetorial e PNG; nao acessa o banco. */
chdir(dirname(__DIR__, 2));
require 'vendor/autoload.php';

$directory = __DIR__;
$svg = file_get_contents($directory.'/modelo_relacional.svg');
$snapshot = json_decode(file_get_contents($directory.'/schema.json'), true, 512, JSON_THROW_ON_ERROR);
$xml = new DOMDocument;
if (!$xml->loadXML($svg, LIBXML_NONET)) {
    throw new RuntimeException('SVG do modelo relacional invalido.');
}
$box = preg_split('/\s+/', trim($xml->documentElement->getAttribute('viewBox')));
$width = (float) $box[2];
$height = (float) $box[3];
$margin = 24;
$pageWidth = $width + 2 * $margin;
$pageHeight = $height + 2 * $margin + 75;
$tables = count(array_filter($snapshot['entities'], fn ($entity) => $entity['table_type'] === 'BASE TABLE'));
$views = count($snapshot['views']);
$fks = count(array_filter($snapshot['relations'], fn ($relation) => $relation['kind'] === 'FK'));
$logical = count($snapshot['relations']) - $fks;
$escape = fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$html = '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><style>'
    .'@page { margin: '.$margin.'pt; } body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #25344a; } '
    .'img { display: block; width: '.$width.'pt; height: '.$height.'pt; } '
    .'p { font-size: 11pt; margin: 10pt 0 0; line-height: 1.4; } '
    .'</style></head><body><img src="data:image/svg+xml;base64,'.base64_encode($svg).'" alt="Modelo relacional completo">'
    .'<p><strong>IMWplus — '.$tables.' tabelas, '.$views.' views, '.$fks.' FKs e '.$logical.' vínculos lógicos.</strong> '
    .'Snapshot: '.$escape($snapshot['generated_at']).' (Brasília).</p>'
    .'<p>Setas: dependente → referência. Múltiplas referências entre as mesmas entidades são agrupadas. '
    .'Campos, cardinalidades e regras individuais estão em documentacao_base_completa.pdf.</p></body></html>';

ini_set('memory_limit', '1024M');
$options = new Dompdf\Options;
$options->set('isRemoteEnabled', false);
$options->set('isPhpEnabled', false);
$options->set('defaultMediaType', 'print');
$options->set('defaultFont', 'DejaVu Sans');
$pdf = new Dompdf\Dompdf($options);
$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper([0, 0, $pageWidth, $pageHeight]);
$pdf->render();
if ($pdf->getCanvas()->get_page_count() !== 1) {
    throw new RuntimeException('O diagrama excedeu uma folha. Revise as dimensoes de exportacao.');
}
file_put_contents($directory.'/modelo_relacional_completo.pdf', $pdf->output());

$process = new Symfony\Component\Process\Process([
    'pdftoppm', '-png', '-singlefile', '-scale-to', '6500',
    $directory.'/modelo_relacional_completo.pdf', $directory.'/modelo_relacional_completo',
]);
$process->setTimeout(180);
$process->mustRun();
$size = getimagesize($directory.'/modelo_relacional_completo.png');
echo 'PDF: uma folha vetorial de '.round($pageWidth * 25.4 / 72).' × '.round($pageHeight * 25.4 / 72)." mm.\n";
echo 'PNG: '.$size[0].' × '.$size[1]." pixels.\n";
echo 'Entidades representadas: '.count($snapshot['entities']).".\n";
