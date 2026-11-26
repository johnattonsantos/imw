<?php

/**
 * Gera a documentacao do schema configurado, sem alterar o banco nem exportar dados pessoais.
 * Uso: php docs/base_dados/gerar.php [--sem-pdf]
 * As consultas de dados ficam restritas aos catalogos e aos campos de dominio listados abaixo.
 */
chdir(dirname(__DIR__, 2));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$catalogo = require __DIR__.'/catalogo.php';
$out = __DIR__;
$diagramDir = $out.'/diagramas';
if (!is_dir($diagramDir)) mkdir($diagramDir, 0755, true);
$db = DB::connection();
$schema = $db->getDatabaseName();
$generated = now()->timezone('America/Sao_Paulo')->format('d/m/Y H:i:s');
$tables = $db->table('information_schema.tables')->where('table_schema', $schema)
    ->select('table_name', 'table_type', 'table_comment', 'engine')->orderBy('table_name')->get()->map(fn ($r) => (array) $r)->all();
$columns = $db->table('information_schema.columns')->where('table_schema', $schema)
    ->select('table_name', 'column_name', 'column_type', 'is_nullable', 'column_default', 'column_key', 'extra', 'column_comment', 'ordinal_position')
    ->orderBy('table_name')->orderBy('ordinal_position')->get()->map(fn ($r) => (array) $r)->all();
$fkRows = $db->table('information_schema.key_column_usage as k')
    ->leftJoin('information_schema.referential_constraints as r', function ($j) {
        $j->on('r.constraint_schema', '=', 'k.constraint_schema')->on('r.constraint_name', '=', 'k.constraint_name')->on('r.table_name', '=', 'k.table_name');
    })->where('k.table_schema', $schema)->whereNotNull('k.referenced_table_name')
    ->select('k.table_name', 'k.column_name', 'k.constraint_name', 'k.referenced_table_name', 'k.referenced_column_name', 'k.ordinal_position', 'r.delete_rule', 'r.update_rule')
    ->orderBy('k.table_name')->orderBy('k.constraint_name')->orderBy('k.ordinal_position')->get();
$indexes = $db->table('information_schema.statistics')->where('table_schema', $schema)
    ->select('table_name', 'index_name', 'non_unique', 'seq_in_index', 'column_name', 'index_type')
    ->orderBy('table_name')->orderBy('index_name')->orderBy('seq_in_index')->get()->map(fn ($r) => (array) $r)->all();
$views = $db->table('information_schema.views')->where('table_schema', $schema)
    ->select('table_name', 'view_definition', 'check_option', 'is_updatable')->orderBy('table_name')->get()->map(fn ($r) => (array) $r)->all();
$triggers = $db->table('information_schema.triggers')->where('trigger_schema', $schema)
    ->select('trigger_name', 'event_manipulation', 'event_object_table', 'action_timing', 'action_statement')->get()->map(fn ($r) => (array) $r)->all();
try {
    $checks = $db->table('information_schema.table_constraints as t')
        ->join('information_schema.check_constraints as c', function ($j) {
            $j->on('t.constraint_schema', '=', 'c.constraint_schema')->on('t.constraint_name', '=', 'c.constraint_name');
        })->where('t.table_schema', $schema)->where('t.constraint_type', 'CHECK')
        ->select('t.table_name', 't.constraint_name', 'c.check_clause')->get()->map(fn ($r) => (array) $r)->all();
} catch (Throwable $e) {
    $checks = [];
}
$byTable = collect($columns)->groupBy('table_name')->map(fn ($c) => $c->keyBy('column_name')->all())->all();
$byIndex = collect($indexes)->groupBy('table_name')->map(fn ($c) => $c->groupBy('index_name')->map(fn ($v) => $v->all())->all())->all();
foreach ($tables as $t) {
    if (!isset($catalogo['entities'][$t['table_name']])) throw new RuntimeException('Entidade sem descricao revisada: '.$t['table_name']);
}
function h($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function code($value): string { return '<code>'.h($value).'</code>'; }
function tableLink(string $table): string { return '<a href="#t-'.h($table).'">'.code($table).'</a>'; }
function uniqueColumns(string $table, array $cols): bool
{
    global $byIndex;
    foreach ($byIndex[$table] ?? [] as $index) {
        $fields = array_column($index, 'column_name');
        if (!(int) $index[0]['non_unique'] && count($fields) === count($cols) && !array_diff($fields, $cols)) return true;
    }
    return false;
}
$relations = [];
foreach ($fkRows->groupBy(fn ($r) => $r->table_name.'.'.$r->constraint_name) as $group) {
    $first = $group->first();
    $fields = $group->pluck('column_name')->all();
    $targetFields = $group->pluck('referenced_column_name')->all();
    $nullable = collect($fields)->contains(fn ($col) => $byTable[$first->table_name][$col]['is_nullable'] === 'YES');
    $relations[] = ['from' => $first->table_name, 'columns' => $fields, 'to' => $first->referenced_table_name,
        'target_columns' => $targetFields, 'kind' => 'FK', 'constraint' => $first->constraint_name,
        'nullable' => $nullable, 'unique' => uniqueColumns($first->table_name, $fields),
        'target_unique' => uniqueColumns($first->referenced_table_name, $targetFields),
        'delete_rule' => $first->delete_rule, 'update_rule' => $first->update_rule, 'sources' => ['information_schema']];
}

// Descobre modelos sem instancia-los: getDefaultProperties nao executa boot/observers.
$models = []; $modelWarnings = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Models')) as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') continue;
    $source = file_get_contents($file);
    if (!preg_match('/namespace\s+([^;]+);/', $source, $ns) || !preg_match('/class\s+(\w+)\s+extends/', $source, $cl)) continue;
    $class = trim($ns[1]).'\\'.$cl[1];
    try {
        if (!class_exists($class)) continue;
        $reflection = new ReflectionClass($class);
        if (!$reflection->isSubclassOf(Model::class) || realpath($reflection->getFileName()) !== realpath((string) $file)) continue;
        $defaults = $reflection->getDefaultProperties();
        $table = $defaults['table'] ?? Str::snake(Str::pluralStudly($cl[1]));
        $models[$class] = ['class' => $class, 'table' => $table, 'file' => (string) $file,
            'primary_key' => $defaults['primaryKey'] ?? 'id', 'soft_deletes' => in_array(Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($class), true),
            'casts' => $defaults['casts'] ?? [], 'namespace' => trim($ns[1]), 'source' => $source];
    } catch (Throwable $e) {
        $modelWarnings[] = (string) $file.': metadados do modelo nao carregados ('.get_class($e).').';
    }
}
function addLogical(string $from, string $col, string $to, string $target, string $source): void
{
    global $relations, $byTable;
    if (!isset($byTable[$from][$col], $byTable[$to][$target])) return;
    foreach ($relations as &$r) {
        if ($r['from'] === $from && $r['to'] === $to && $r['columns'] === [$col] && $r['target_columns'] === [$target]) {
            if (!in_array($source, $r['sources'])) $r['sources'][] = $source;
            return;
        }
    }
    unset($r);
    $relations[] = ['from' => $from, 'columns' => [$col], 'to' => $to, 'target_columns' => [$target], 'kind' => 'Logica',
        'constraint' => null, 'nullable' => $byTable[$from][$col]['is_nullable'] === 'YES', 'unique' => uniqueColumns($from, [$col]),
        'target_unique' => uniqueColumns($to, [$target]), 'delete_rule' => null, 'update_rule' => null, 'sources' => [$source]];
}
foreach ($models as $model) {
    if (!isset($byTable[$model['table']])) continue;
    preg_match_all('/^use\s+([^;]+);/m', $model['source'], $imports);
    $aliases = [];
    foreach ($imports[1] as $import) {
        $parts = preg_split('/\s+as\s+/i', trim($import));
        $aliases[$parts[1] ?? basename(str_replace('\\', '/', $parts[0]))] = $parts[0];
    }
    preg_match_all('/function\s+(\w+)\s*\([^)]*\)[^{]*\{(.*?)\n\s*\}/s', $model['source'], $methods, PREG_SET_ORDER);
    foreach ($methods as $method) {
        if (!preg_match('/\$this->(belongsTo|hasOne|hasMany|belongsToMany)\(\s*([\\\\\w]+)::class([^;]*?)\)/s', $method[2], $match)) continue;
        $short = $match[2];
        $related = $short === 'self' ? $model['class'] : ($aliases[$short] ?? $model['namespace'].'\\'.$short);
        if (!isset($models[$related])) continue;
        $other = $models[$related];
        preg_match_all('/[\x27\"]([^\x27\"]+)[\x27\"]/', $match[3], $args);
        $args = $args[1];
        $src = $model['file'].':'.$method[1];
        if (str_contains($match[3], '[')) continue; // Chaves compostas exigem revisao especifica.
        if ($match[1] === 'belongsTo') {
            addLogical($model['table'], $args[0] ?? Str::snake($method[1]).'_id', $other['table'], $args[1] ?? $other['primary_key'], $src);
        } elseif ($match[1] === 'belongsToMany') {
            if (!isset($args[0])) continue;
            addLogical($args[0], $args[1] ?? Str::snake(class_basename($model['class'])).'_id', $model['table'], $model['primary_key'], $src);
            addLogical($args[0], $args[2] ?? Str::snake(class_basename($other['class'])).'_id', $other['table'], $other['primary_key'], $src);
        } else {
            $foreign = $args[0] ?? Str::snake(class_basename($model['class'])).'_id';
            $local = $args[1] ?? $model['primary_key'];
            $inverseFk = false;
            foreach ($relations as &$r) {
                if ($r['kind'] === 'FK' && $r['from'] === $model['table'] && $r['to'] === $other['table']
                    && $r['columns'] === [$local] && $r['target_columns'] === [$foreign]) {
                    if (!in_array($src, $r['sources'])) $r['sources'][] = $src;
                    $inverseFk = true;
                    break;
                }
            }
            unset($r);
            if (!$inverseFk) addLogical($other['table'], $foreign, $model['table'], $local, $src);
        }
    }
}
addLogical('pastor_acessos_motivacionais', 'user_id', 'users', 'id', 'app/Services/MensagemPastorService.php');
addLogical('audits', 'user_id', 'users', 'id', 'app/Http/Controllers/AuthController.php (somente quando user_type representa User)');
addLogical('audits', 'instituicao_id', 'instituicoes_instituicoes', 'id', 'app/Services/ServiceRegiaoRelatorios/GestaoAcessosService.php');
addLogical('users', 'pessoa_id', 'pessoas_pessoas', 'id', 'app/Models/User.php; vinculo de conta ao clerigo');
usort($relations, fn ($a, $b) => [$a['from'], $a['to'], $a['columns']] <=> [$b['from'], $b['to'], $b['columns']]);

$catalogFields = [
    'instituicoes_tiposinstituicao' => ['id','nome','sigla','hierarquia'],
    'membresia_situacoes' => ['id','nome','tipo','descricao','deleted_at'],
    'pessoas_status' => ['id','descricao','codigo','deleted_at'],
    'pessoas_funcaoministerial' => ['id','funcao','onus','vinculo','titular','excluido'],
    'membresia_tiposatuacao' => ['id','descricao','ordem'],
    'membresia_funcoeseclesiasticas' => ['id','descricao','titulo','ordem'],
    'membresia_setores' => ['id','descricao','sigla','ordem'], 'gceu_funcoes' => ['id','funcao'],
    'financeiro_tipos_pagantes_favorecidos' => ['id','nome'], 'financeiro_plano_contas_categoria' => ['id','nome'],
    'formacoes' => ['id','nivel'], 'membresia_formacoes' => ['id','descricao'],
    'patrimonio_configuracoes' => ['id','tipo','nome','ativo'], 'perfils' => ['id','nome','nivel','deleted_at'],
    'meses' => ['id','descricao'], 'tipo_arquivo' => ['id','extensao'],
];
$catalogs = [];
foreach ($catalogFields as $table => $fields) {
    if (!isset($byTable[$table])) continue;
    $fields = array_values(array_intersect($fields, array_keys($byTable[$table])));
    $catalogs[$table] = $db->table($table)->select($fields)->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
}
function isDomain(array $c): bool
{
    global $catalogo;
    return isset($catalogo['domains'][$c['table_name'].'.'.$c['column_name']]) || isset($catalogo['flags'][$c['column_name']])
        || preg_match('/^(status|situacao|ativo|vinculo|tipo|nivel|sexo|estado_civil|categoria|origem|lastrec|validado|has_errors|conciliado|estornado|estorno|auditado|selecionavel|essencial|executed|presenca|presente|onus|titular|excluido|status_entrega|resultado|semestre|tipo_documento|periodo_aula|cargo|novo_convertido|decimo_terceiro)$/', $c['column_name'])
        || str_starts_with($c['column_type'], 'enum(') || str_starts_with($c['column_type'], 'tinyint(1)');
}
$domains = [];
foreach ($columns as $c) {
    if (str_starts_with($c['table_name'], 'vw_') || !isDomain($c)) continue;
    $values = $db->table($c['table_name'])->whereNotNull($c['column_name'])->select($c['column_name'])->distinct()->limit(41)->pluck($c['column_name'])->all();
    $domains[$c['table_name'].'.'.$c['column_name']] = ['values' => array_slice($values, 0, 40), 'truncated' => count($values) > 40];
}
function domainDescription(array $c): array
{
    global $catalogo;
    $key = $c['table_name'].'.'.$c['column_name'];
    if (isset($catalogo['domains'][$key])) return $catalogo['domains'][$key];
    if (isset($catalogo['flags'][$c['column_name']])) return [$catalogo['flags'][$c['column_name']], 'Indicador identificado no schema/modelos; interpretar junto ao fluxo do modulo.'];
    if (str_starts_with($c['column_type'], 'enum(')) {
        preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $c['column_type'], $matches);
        return ['Valores permitidos pelo enum SQL: '.implode(', ', $matches[1]).'. Labels iguais ao valor salvo, salvo tradução no formulário.', 'information_schema.columns.column_type'];
    }
    if (preg_match('/^membresia_migracao/', $c['table_name'])) return ['Campo de estrutura legada de migração. Não assumir que os códigos tenham a mesma semântica do cadastro principal sem validar a rotina de importação.', 'Schema e finalidade da tabela; interpretação específica não confirmada.'];
    return ['Não foi confirmado um domínio fechado/legenda funcional para este campo. Os valores observados abaixo são evidência do ambiente, não uma lista completa de estados permitidos.', 'Schema e valores distintos do campo; sem significado funcional confirmado.'];
}
function fieldDescription(array $c): string
{
    global $relations, $catalogo;
    $n = $c['column_name']; $table = $c['table_name'];
    $special = [
        'created_at' => 'Data/hora de criação do registro.', 'updated_at' => 'Data/hora da última atualização.',
        'deleted_at' => 'Exclusão lógica: NULL = não excluído por este marcador; preenchido = excluído logicamente. O filtro automático depende de SoftDeletes no modelo. Não equivale a ativo.',
        'id' => 'Identificador do registro; verifique PK e tipo físico, pois nem todo ID é inteiro.',
        'login_id' => 'UUID de contagem motivacional. Não é o ID de audits nem o token da sessão autenticada.',
        'password' => 'Hash de senha. Dado sensível, não exportado nesta documentação.', 'remember_token' => 'Token de lembrança de autenticação; dado sensível.',
        'token' => 'Token/valor de autenticação ou recuperação; dado sensível. Consulte o mecanismo Laravel correspondente.',
        'email_verified_at' => 'NULL = e-mail sem verificação registrada; data preenchida = verificação registrada.',
        'abilities' => 'Habilidades/permissões associadas ao token Sanctum, não ao pivot perfil_regra.',
        'expires_at' => 'Data de expiração do token; ausência de data deve ser interpretada conforme a configuração de autenticação.',
        'last_used_at' => 'Última utilização observada do token.',
        'auditable_id' => 'Identificador do objeto auditado; o tipo é auditable_type. Relação polimórfica, não FK universal.',
        'auditable_type' => 'Classe/tipo do objeto auditado. Deve ser interpretado junto a auditable_id.',
        'old_values' => 'Valores anteriores em estrutura JSON/texto de auditoria; dados reais não são exportados aqui.',
        'new_values' => 'Valores posteriores/metadados do evento em JSON/texto; dados reais não são exportados aqui.',
        'user_type' => 'Tipo/classe do autor da auditoria; dá contexto a user_id.',
        'event' => 'Evento de auditoria, como created/updated/deleted/restored e login/logout/login_failed. A lista não é enum SQL fechado.',
        'documentavel_id' => 'ID do ativo documentado, interpretado com documentavel_type. Não é FK universal.',
        'documentavel_type' => 'Tipo/classe do ativo documentado no relacionamento polimórfico.',
        'tokenable_id' => 'ID do titular do token, interpretado com tokenable_type.', 'tokenable_type' => 'Classe/tipo do titular do token Sanctum.',
        'dt_aceite' => 'Data do aceite da transferência; NULL significa que não há aceite registrado.',
        'dt_rejeicao' => 'Data da rejeição da transferência; NULL significa que não há rejeição registrada.',
        'dt_exclusao' => 'Data de exclusão do rol; analisar com status, modo de exclusão e lastrec.',
        'dt_recepcao' => 'Data de recepção no rol e na igreja registrada.',
        'data_termino' => 'Término da nomeação; nomeacoesAtivas no modelo considera NULL como nomeação sem término.',
        'data_conciliacao' => 'Data da conciliação financeira; deve ser interpretada com conciliado.',
        'lido_em' => 'Data/hora de leitura. Ausência de linha/data de leitura pode representar pendência conforme o fluxo.',
        'qr_token' => 'Token de credenciamento do inscrito; não é chave estrangeira.',
        'data_saida' => 'Fim/saída do vínculo; NULL pode representar vínculo ainda sem saída registrada.',
        'data_validade' => 'Validade do documento. Uma situação textual não substitui a verificação desta data.',
    ];
    if (isset($catalogo['domains'][$table.'.'.$n])) return $catalogo['domains'][$table.'.'.$n][0];
    if (isset($special[$n])) return $special[$n];
    if (isDomain($c)) return domainDescription($c)[0];
    $refs = [];
    foreach ($relations as $r) if ($r['from'] === $table && in_array($n, $r['columns'])) $refs[] = $r['to'].' ('.$r['kind'].')';
    if ($refs) return 'Referência a '.implode('; ', array_unique($refs)).'. Ver detalhes e cardinalidade nos relacionamentos.';
    if (str_ends_with($n, '_id')) return 'Identificador de referência. Não foi confirmada uma FK/modelo correspondente neste levantamento; não inferir destino apenas pelo nome.';
    if (in_array($n, ['cpf','cnpj','cpfcnpj','identidade','documento','pispasep','inss','ctps','titulo_eleitor'])) return 'Identificação cadastral/documental; dado pessoal/fiscal. Não contém exemplos de valores reais nesta documentação.';
    if (str_contains($n, 'telefone') || str_contains($n, 'email') || in_array($n, ['celular','contato'])) return 'Dado de contato do cadastro; não é estado/status.';
    if (in_array($n, ['arquivo','caminho','documento_anexo','documento_comprobatorio','foto'])) return 'Referência/caminho de arquivo. Não implica armazenamento do binário na tabela; pode apontar para S3 ou disco configurado.';
    if (str_starts_with($n, 'codigo_host') || str_ends_with($n, '_host')) return 'Identificador/referência de integração legada (host); não presumir equivalência à PK local.';
    if (preg_match('/^(valor|total_|saldo_|aux_|aliquota|deducao)/', $n)) return 'Valor/parâmetro numérico do domínio da entidade. Conferir unidade, sinal e cálculo no serviço responsável.';
    if (in_array($n, ['jan','fev','mar','abr','mai','jun','jul','ago','set','out','nov','dez'])) return 'Valor da grade no mês indicado, dentro do ano do registro.';
    if ($n === 'o13') return 'Parcela adicional da grade identificada como décimo terceiro; não é mês civil.';
    if (preg_match('/^(data_|dt_)/', $n)) return 'Data do evento indicado pelo nome do campo, no contexto desta entidade.';
    if (in_array($n, ['nome','descricao','titulo','comentario','conteudo','observacoes','observacao','historico'])) return 'Conteúdo descritivo de '.str_replace('_', ' ', $n).' no contexto desta entidade.';
    return 'Atributo '.str_replace('_', ' ', $n).' da entidade. Papel estrutural confirmado pelo schema; regra funcional específica não presumida.';
}

$cleanModels = array_map(function ($model) { unset($model['source'], $model['namespace']); return $model; }, array_values($models));
$missingModels = array_values(array_filter($cleanModels, fn ($m) => !isset($byTable[$m['table']])));
$payload = ['generated_at' => $generated, 'timezone' => 'America/Sao_Paulo', 'schema' => $schema,
    'scope' => 'Metadados, dominios e catalogos; sem linhas de usuarios, membros, pessoas ou movimentacoes.',
    'entities' => $tables, 'columns' => $columns, 'indexes' => $indexes, 'relations' => $relations, 'views' => $views,
    'checks' => $checks, 'triggers' => $triggers, 'models' => $cleanModels, 'models_without_table' => $missingModels,
    'domains' => $domains, 'catalogs' => $catalogs];
file_put_contents($out.'/schema.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

// Diagrama ER navegavel em SVG. Setas apontam do registro dependente para o referenciado.
function diagramSvg(array $nodes, array $links, string $title): string
{
    $nodes = array_values(array_unique($nodes)); sort($nodes);
    $columns = count($nodes) > 35 ? 6 : 3;
    $width = $columns * 285 + 30;
    $height = (int) ceil(count($nodes) / $columns) * 104 + 85;
    $positions = [];
    foreach ($nodes as $i => $name) $positions[$name] = [20 + ($i % $columns) * 285, 65 + intdiv($i, $columns) * 104];
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">';
    $svg .= '<defs><marker id="arrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="5" markerHeight="5" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z" fill="#435b79"/></marker></defs>';
    $svg .= '<rect x="0" y="0" width="'.$width.'" height="'.$height.'" fill="#ffffff"/><text x="20" y="23" font-size="15" font-family="sans-serif" fill="#173257">'.h($title).'</text>';
    $svg .= '<text x="20" y="44" font-size="9" font-family="sans-serif" fill="#526176">Seta: dependente → referência. Azul: FK declarada. Laranja tracejado: vínculo no código. Detalhes no dicionário.</text>';
    $pairs = [];
    foreach ($links as $r) {
        if (!isset($positions[$r['from']], $positions[$r['to']])) continue;
        $key = $r['from'].'|'.$r['to'];
        if (!isset($pairs[$key])) $pairs[$key] = $r;
        elseif ($r['kind'] === 'FK') $pairs[$key]['kind'] = 'FK';
    }
    foreach ($pairs as $r) {
        [$x1,$y1] = $positions[$r['from']]; [$x2,$y2] = $positions[$r['to']];
        $color = $r['kind'] === 'FK' ? '#6685aa' : '#c28b3b';
        $dash = $r['kind'] === 'FK' ? '' : ' stroke-dasharray="5 3"';
        if ($r['from'] === $r['to']) $path = 'M '.($x1+245).' '.($y1+17).' C '.($x1+280).' '.($y1-10).' '.($x1+280).' '.($y1+60).' '.($x1+245).' '.($y1+44);
        else $path = 'M '.($x1+125).' '.($y1+57).' C '.($x1+125).' '.($y1+83).' '.($x2+125).' '.($y2-22).' '.($x2+125).' '.$y2;
        $svg .= '<path d="'.$path.'" fill="none" stroke="'.$color.'" stroke-width="1.3" opacity="0.65"'.$dash.' marker-end="url(#arrow)"><title>'.h($r['from'].'.'.implode(',',$r['columns']).' → '.$r['to']).'</title></path>';
    }
    global $catalogo, $byTable;
    foreach ($nodes as $name) {
        [$x,$y] = $positions[$name]; $type = $catalogo['entities'][$name][0] ?? 'Referência';
        $svg .= '<rect x="'.$x.'" y="'.$y.'" width="250" height="58" rx="5" fill="#f6f8fb" stroke="#516d91"/>';
        $size = strlen($name) > 36 ? 8 : 9.2;
        $svg .= '<text x="'.($x+8).'" y="'.($y+19).'" font-family="monospace" font-size="'.$size.'" fill="#142b47">'.h($name).'</text>';
        $pk = array_keys(array_filter($byTable[$name] ?? [], fn ($c) => $c['column_key'] === 'PRI'));
        $svg .= '<text x="'.($x+8).'" y="'.($y+36).'" font-family="sans-serif" font-size="8" fill="#526176">PK: '.h($pk ? implode(', ', $pk) : 'não declarada').'</text>';
        $svg .= '<text x="'.($x+8).'" y="'.($y+49).'" font-family="sans-serif" font-size="8" fill="#526176">'.h($type).'</text>';
    }
    return $svg.'</svg>';
}
$allNames = array_column($tables, 'table_name');
file_put_contents($out.'/modelo_relacional.svg', diagramSvg($allNames, $relations, 'Modelo relacional completo - IMWplus'));
$mermaid = "erDiagram\n";
foreach ($tables as $t) {
    $name = $t['table_name']; $mermaid .= '    '.$name." {\n";
    $mermaidFields = 0;
    foreach ($byTable[$name] as $c) {
        $isFk = collect($relations)->contains(fn ($r) => $r['from'] === $name && in_array($c['column_name'], $r['columns']) && $r['kind'] === 'FK');
        if ($c['column_key'] !== 'PRI' && !$isFk) continue;
        preg_match('/^[a-z]+/', $c['column_type'], $type);
        $key = $c['column_key'] === 'PRI' ? ' PK' : ' FK';
        $mermaid .= '        '.($type[0] ?? 'string').' '.$c['column_name'].$key."\n";
        $mermaidFields++;
    }
    if (!$mermaidFields) {
        $firstColumn = reset($byTable[$name]);
        preg_match('/^[a-z]+/', $firstColumn['column_type'], $firstType);
        $mermaid .= '        '.($firstType[0] ?? 'string').' '.$firstColumn['column_name'].' "Sem PK/FK declarada"'."\n";
    }
    $mermaid .= "    }\n";
}
foreach ($relations as $r) {
    $left = $r['target_unique'] ? ($r['nullable'] ? '|o' : '||') : '}o';
    $right = $r['unique'] ? 'o|' : 'o{';
    $line = $r['kind'] === 'FK' ? '--' : '..';
    $mermaid .= '    '.$r['to'].' '.$left.$line.$right.' '.$r['from'].' : "'.$r['kind'].' '.implode(',', $r['columns']).'"'."\n";
}
file_put_contents($out.'/modelo_relacional.mmd', $mermaid);
$modules = [];
foreach ($tables as $t) $modules[$catalogo['entities'][$t['table_name']][0]][] = $t['table_name'];
ksort($modules);
$diagrams = [];
foreach ($modules as $module => $nodes) {
    $external = [];
    foreach ($relations as $r) if (in_array($r['from'], $nodes) && !in_array($r['to'], $nodes)) $external[] = $r['to'];
    $svg = diagramSvg(array_merge($nodes, $external), array_filter($relations, fn ($r) => in_array($r['from'], $nodes)), $module.' - entidades e referências');
    $filename = Str::slug($module).'.svg'; file_put_contents($diagramDir.'/'.$filename, $svg); $diagrams[$module] = $svg;
}
$fkCount = count(array_filter($relations, fn ($r) => $r['kind'] === 'FK'));
$logicalCount = count($relations) - $fkCount;
$baseCount = count(array_filter($tables, fn ($t) => $t['table_type'] === 'BASE TABLE'));
$logo = file_exists('public/auth/images/login.png') ? '<img class="logo" src="data:image/png;base64,'.base64_encode(file_get_contents('public/auth/images/login.png')).'" alt="IMWplus">' : '';
$html = '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>Documentação da Base de Dados - IMWplus</title><style>
@page { margin: 80px 32px 42px; }
body { font-family: DejaVu Sans, sans-serif; color: #25344a; font-size: 9px; line-height: 1.4; }
header { position: fixed; top: -67px; left: 0; right: 0; height: 56px; border-bottom: 1px solid #c5cfdf; }
.logo { width: 112px; height: 63px; position: absolute; top: -6px; left: 0; }
.header-title { margin-left: 132px; padding-top: 9px; font-size: 12px; color: #173257; }
.header-subtitle { margin-left: 132px; font-size: 8px; color: #66768d; }
h1 { font-size: 28px; line-height: 1.2; color: #183b6c; margin: 20px 0; }
h2 { font-size: 17px; color: #183b6c; border-bottom: 2px solid #d4ddeb; padding-bottom: 6px; }
h3 { font-size: 12px; color: #214b7b; margin-top: 15px; }
h4 { font-size: 10px; margin-bottom: 5px; }
a { color: #225998; text-decoration: none; }
code, pre { font-family: DejaVu Sans Mono, monospace; font-size: 8px; }
code { word-wrap: break-word; } pre { white-space: pre-wrap; word-wrap: break-word; background: #f4f6f9; padding: 8px; }
table { border-collapse: collapse; width: 100%; margin: 8px 0 14px; table-layout: fixed; }
th, td { border: 1px solid #d4ddea; padding: 5px; vertical-align: top; word-wrap: break-word; }
th { background: #edf2f8; color: #173257; font-weight: bold; }
tr { page-break-inside: avoid; } thead { display: table-header-group; }
.note { padding: 9px 12px; background: #f4f7fb; border-left: 3px solid #3b6fa5; margin: 12px 0; }
.warning { background: #fff8e8; border-left-color: #bc882a; }
.muted { color: #62728a; } .small { font-size: 8px; }
.section, .entity { page-break-before: always; }
.diagram { width: 100%; height: auto; margin: 10px 0; }
.metrics td { text-align: center; padding: 12px 3px; } .metrics strong { display: block; font-size: 21px; color: #214b7b; }
.col-name { width: 22%; } .col-type { width: 17%; } .col-null { width: 6%; } .col-default { width: 10%; } .col-key { width: 8%; } .col-desc { width: 37%; }
@media screen { body { max-width: 1160px; margin: 24px auto; padding: 24px; background: white; font-size: 13px; } header { position: relative; top: auto; height: 64px; } code, pre { font-size: 11px; } .entity, .section { border-top: 1px solid #d4ddea; padding-top: 20px; margin-top: 40px; } .small { font-size: 11px; } }
</style></head><body><header>'.$logo.'<div class="header-title">Base de Dados e Modelo Relacional</div><div class="header-subtitle">IMWplus | uso interno | geração: '.h($generated).' (Brasília)</div></header>';
$html .= '<h1>Documentação completa<br>da base de dados</h1><p>Entidades, relacionamentos, dicionário de campos, catálogos e códigos de status.</p><p><strong>Schema consultado:</strong> '.code($schema).'<br><strong>Referência:</strong> '.h($generated).' — America/Sao_Paulo.</p>';
$html .= '<table class="metrics"><tr><td><strong>'.$baseCount.'</strong>Tabelas</td><td><strong>'.count($views).'</strong>Views</td><td><strong>'.count($columns).'</strong>Colunas</td><td><strong>'.$fkCount.'</strong>FKs</td><td><strong>'.$logicalCount.'</strong>Vínculos lógicos</td></tr></table>';
$html .= '<div class="note">Levantamento do banco configurado neste ambiente, confrontado com o código atual. Não é uma declaração de que produção possui a mesma estrutura. Nenhuma tabela, status ou dado de negócio foi alterado para produzir este documento.</div>';
$html .= '<div class="note warning">Não contém senhas, tokens, CPFs, nomes de usuários/membros/clérigos nem linhas de movimentação. Valores publicados são metadados e catálogos de configuração/domínios. É documentação interna: caminhos, permissões e estrutura devem ser tratados com controle de acesso.</div>';
$html .= '<h2>Como ler o modelo</h2><p><strong>Entidade:</strong> tabela ou view. <strong>PK:</strong> identificador físico declarado. <strong>FK:</strong> referência garantida pelo banco. <strong>Vínculo lógico:</strong> relação encontrada no modelo/serviço sem a mesma garantia física. <strong>Pivot:</strong> tabela que materializa associação entre entidades.</p><p>Cardinalidade é descrita do registro dependente para o referenciado e em sentido inverso. NULL permite ausência de vínculo; UNIQUE limita a quantidade de dependentes por referência. Um método hasOne no PHP não cria, sozinho, uma restrição UNIQUE no banco.</p><p>As setas SVG apontam do dependente para a referência. Nos arquivos Mermaid, linhas pontilhadas distinguem vínculos lógicos nesta documentação; não devem ser interpretadas como uma nova restrição no banco.</p>';
$html .= '<h3>Estados que não são equivalentes</h3><ul><li>ativo/status não equivale a deleted_at: inativação e exclusão lógica são eixos diferentes.</li><li>NULL significa ausência de informação ou valor opcional conforme o campo; não é automaticamente 0, inativo ou isento.</li><li>pessoas_status.id e pessoas_status.codigo não usam a mesma numeração.</li><li>membresia_membros.status, membresia_rolpermanente.status e membresia_situacoes.tipo descrevem fatos distintos.</li><li>Vínculo integral/parcial do clérigo vem de pessoas_funcaoministerial.vinculo, não da quantidade de prebendas.</li><li>Perfil atual e instituição atual não comprovam o contexto de um login antigo sem esse dado gravado.</li></ul>';
$html .= '<h2>Índice de módulos e entidades</h2>';
foreach ($modules as $module => $names) {
    $html .= '<h3><a href="#m-'.Str::slug($module).'">'.h($module).'</a></h3><p>'.implode(' · ', array_map('tableLink', $names)).'</p>';
}
$html .= '<p><a href="#status">Guia consolidado de status</a> · <a href="#catalogos">Catálogos de domínio</a> · <a href="#ressalvas">Ressalvas e divergências</a> · <a href="#views">Definições das views</a></p>';
$html .= '<div class="section" id="ressalvas"><h2>Ressalvas, legados e integridade</h2><ul>';
$html .= '<li>As quatro tabelas gestao_acessos, gestao_acessos_configuracao, gestao_acessos_sessoes e gestao_acessos_sessao_perfis não existem no snapshot atual. Não são entidades ativas deste modelo. Acesso é consultado em audits; a contagem motivacional dos pastores é independente.</li>';
$html .= '<li>As entidades de chat existem no banco, mas não foram localizados modelos atuais correspondentes na varredura. Os valores REGIAO, ativa e enviada observados não autorizam supor outros estados nem regras de transição.</li>';
$html .= '<li>Há tipos institucionais semelhantes em IDs diferentes (congregação, órgão e contabilidade). Os catálogos mostram todos os IDs; o código possui constantes específicas. Nome igual não significa ID intercambiável.</li>';
$html .= '<li>O catálogo gceu_funcoes deste ambiente descreve o ID 7 como Lider em Treinamento; serviços como app/Services/ServiceGCeu/VisualizarGCeuService.php usam gceu_funcao_id=7 como anfitrião. É divergência a revisar, não regra corrigida por esta documentação.</li>';
$html .= '<li>patrimonio_configuracoes.igreja_id ainda está presente no banco consultado. O fato de opções serem usadas globalmente no código não implica remoção física desse campo.</li>';
$html .= '<li>Campos textuais patrimoniais podem guardar nomes de opções, sem FK para o catálogo. Alterar nome/configuração pode exigir avaliação de vínculos; não aplicar cascade por suposição.</li>';
$html .= '<li>membresia_migracao, membresia_migracao0 e perfil_regra_ são estruturas presentes. Não apagá-las nem tratá-las como equivalentes às tabelas principais sem verificar a rotina responsável.</li>';
$html .= '<li>Não foi executada verificação integral de órfãos, valores inválidos, duplicidades nem integridade dos dados. O levantamento identifica restrições e domínios, não certifica que todo dado esteja correto.</li></ul>';
if ($missingModels) {
    $html .= '<h3>Modelos cujo objeto físico não foi encontrado</h3><table><thead><tr><th>Modelo</th><th>Objeto esperado</th><th>Fonte</th></tr></thead><tbody>';
    foreach ($missingModels as $m) $html .= '<tr><td>'.code($m['class']).'</td><td>'.code($m['table']).'</td><td>'.code($m['file']).'</td></tr>';
    $html .= '</tbody></table><p class="muted">Pode indicar funcionalidade retirada, migration não aplicada, conexão especial ou modelo legado. Não é prova de que uma migration deva ser executada.</p>';
}
if ($modelWarnings) $html .= '<p>'.h(implode(' ', $modelWarnings)).'</p>';
$html .= '</div>';

foreach ($modules as $module => $names) {
    $html .= '<div class="section" id="m-'.Str::slug($module).'"><h2>'.h($module).'</h2><p>Entidades deste módulo: '.implode(', ', array_map('tableLink', $names)).'. Referências de outros módulos aparecem no diagrama quando necessárias.</p>';
    $html .= '<img class="diagram" src="data:image/svg+xml;base64,'.base64_encode($diagrams[$module]).'" alt="Diagrama '.h($module).'">';
    $html .= '<p class="small muted">Diagramas resumem conexões por par de entidades; podem existir várias FKs entre o mesmo par. O quadro de cada entidade abaixo mantém as colunas, regras e cardinalidades individuais.</p></div>';
    foreach ($names as $name) {
        $t = collect($tables)->firstWhere('table_name', $name);
        $entityModels = array_values(array_filter($cleanModels, fn ($m) => $m['table'] === $name));
        $html .= '<div class="entity" id="t-'.h($name).'"><h2>'.h($name).'</h2><p>'.h($catalogo['entities'][$name][1]).'</p><p class="small"><strong>Tipo:</strong> '.h($t['table_type']).' | <strong>Engine:</strong> '.h($t['engine'] ?? 'view').' | <strong>Colunas:</strong> '.count($byTable[$name]).'</p>';
        if ($t['table_comment']) $html .= '<p><strong>Comentário SQL:</strong> '.h($t['table_comment']).'</p>';
        if ($entityModels) {
            $html .= '<p class="small"><strong>Modelos/fontes:</strong> '.implode('; ', array_map(fn ($m) => code($m['file']).($m['soft_deletes'] ? ' (SoftDeletes)' : ''), $entityModels)).'.</p>';
        } else $html .= '<p class="small muted">Nenhum modelo Eloquent correspondente foi localizado nesta varredura. Isso não exclui uso por SQL/serviços/migrations.</p>';
        $html .= '<h3>Campos</h3><table><thead><tr><th class="col-name">Campo</th><th class="col-type">Tipo físico</th><th class="col-null">NULL</th><th class="col-default">Padrão</th><th class="col-key">Chave</th><th class="col-desc">Descrição / interpretação</th></tr></thead><tbody>';
        foreach ($byTable[$name] as $c) {
            $default = $c['column_default'] === null ? '—' : (string) $c['column_default'];
            $keys = $c['column_key'] === 'PRI' ? 'PK' : ($c['column_key'] === 'UNI' ? 'UNIQUE' : ($c['column_key'] === 'MUL' ? 'INDEX' : '—'));
            foreach ($relations as $r) if ($r['from'] === $name && $r['kind'] === 'FK' && in_array($c['column_name'], $r['columns'])) { $keys .= ' / FK'; break; }
            $desc = fieldDescription($c).($c['extra'] ? ' Extra SQL: '.$c['extra'].'.' : '').($c['column_comment'] ? ' Comentário SQL: '.$c['column_comment'] : '');
            $html .= '<tr><td>'.code($c['column_name']).'</td><td>'.code($c['column_type']).'</td><td>'.($c['is_nullable'] === 'YES' ? 'Sim' : 'Não').'</td><td>'.code($default).'</td><td>'.h($keys).'</td><td>'.h($desc).'</td></tr>';
        }
        $html .= '</tbody></table><p class="small muted">“—” em Padrão significa ausência de literal de default exposto pelo driver; não se deve concluir automaticamente DEFAULT NULL. Consultas específicas podem tratar NULL de outra forma.</p>';
        $outgoing = array_filter($relations, fn ($r) => $r['from'] === $name);
        $incoming = array_filter($relations, fn ($r) => $r['to'] === $name && $r['from'] !== $name);
        $html .= '<h3>Relacionamentos de saída</h3>';
        if (!$outgoing) $html .= '<p>Sem FK/relação de saída confirmada. IDs sem destino confirmado permanecem explicitamente sem associação neste documento.</p>';
        else {
            $html .= '<table><thead><tr><th>Colunas → referência</th><th>Cardinalidade / garantia</th><th>Regras físicas e origem</th></tr></thead><tbody>';
            foreach ($outgoing as $r) {
                $parent = $r['target_unique'] ? ($r['nullable'] ? '0..1' : '1') : 'alvo não único: revisar';
                $child = $r['unique'] ? '0..1' : '0..N';
                $html .= '<tr><td>'.code(implode(', ', $r['columns'])).' → '.tableLink($r['to']).'.'.code(implode(', ', $r['target_columns'])).'</td><td>Dependente → referência: '.h($parent).'<br>Referência → dependentes: '.h($child).'<br><strong>'.h($r['kind']).'</strong></td><td>'.($r['kind'] === 'FK' ? 'DELETE: '.h($r['delete_rule']).'; UPDATE: '.h($r['update_rule']).'<br>'.code($r['constraint']) : 'Sem ON DELETE/UPDATE garantido pelo banco.').'<br><span class="small">'.h(implode('; ', $r['sources'])).'</span></td></tr>';
            }
            $html .= '</tbody></table>';
        }
        $html .= '<h3>Entidades que referenciam esta entidade</h3><p>'.($incoming ? implode('; ', array_map(fn ($r) => tableLink($r['from']).'.'.code(implode(',', $r['columns'])).' ['.h($r['kind']).']', $incoming)) : 'Nenhuma referência confirmada nesta varredura.').'</p>';
        if (!empty($byIndex[$name])) {
            $html .= '<h3>Índices e unicidade</h3><table><thead><tr><th>Nome</th><th>Colunas, em ordem</th><th>Restrição</th></tr></thead><tbody>';
            foreach ($byIndex[$name] as $indexName => $index) $html .= '<tr><td>'.code($indexName).'</td><td>'.code(implode(', ', array_column($index, 'column_name'))).'</td><td>'.($indexName === 'PRIMARY' ? 'PRIMARY KEY' : ((int) $index[0]['non_unique'] ? 'Índice não único' : 'UNIQUE')).' / '.h($index[0]['index_type']).'</td></tr>';
            $html .= '</tbody></table>';
        }
        foreach ($checks as $check) if ($check['table_name'] === $name) $html .= '<p><strong>CHECK:</strong> '.code($check['constraint_name']).' '.code($check['check_clause']).'</p>';
        $entityDomains = array_filter($byTable[$name], 'isDomain');
        if ($entityDomains && $t['table_type'] === 'BASE TABLE') {
            $html .= '<h3>Domínios e status desta entidade</h3>';
            foreach ($entityDomains as $c) {
                [$meaning,$source] = domainDescription($c); $domain = $domains[$name.'.'.$c['column_name']] ?? ['values' => [], 'truncated' => false];
                $html .= '<p><strong>'.code($c['column_name']).':</strong> '.h($meaning).'<br><span class="small muted">Fonte: '.h($source).'. Valores observados: '.h($domain['values'] ? json_encode($domain['values'], JSON_UNESCAPED_UNICODE) : 'nenhum valor não NULL').($domain['truncated'] ? ' (amostra limitada; mais de 40 valores distintos)' : '').'.</span></p>';
            }
        }
        $html .= '<p class="small"><a href="#m-'.Str::slug($module).'">Voltar ao módulo</a></p></div>';
    }
}
$html .= '<div class="section" id="status"><h2>Guia consolidado de status e domínios</h2><p>Este guia reúne todos os campos identificados como estados, tipos, indicadores ou enums. Valores observados não substituem enum, validação ou catálogo. Campos sem legenda confirmada são indicados expressamente.</p><table><thead><tr><th>Entidade / campo</th><th>Significado e fonte</th><th>Valores observados</th></tr></thead><tbody>';
foreach ($domains as $key => $domain) {
    [$table,$column] = explode('.', $key, 2); [$meaning,$source] = domainDescription($byTable[$table][$column]);
    $html .= '<tr><td>'.tableLink($table).'<br>'.code($column).'</td><td>'.h($meaning).'<br><span class="small muted">'.h($source).'</span></td><td>'.code(json_encode($domain['values'], JSON_UNESCAPED_UNICODE)).($domain['truncated'] ? '<br>Mais valores existentes.' : '').'</td></tr>';
}
$html .= '</tbody></table><h3>Estados implícitos em datas</h3><ul><li>Transferência pendente: dt_aceite e dt_rejeicao ausentes. Aceita: dt_aceite preenchida. Rejeitada: dt_rejeicao preenchida. Se ambas existirem, requer revisão; não foi presumida uma constraint de exclusão mútua.</li><li>Nomeação sem término: pessoas_nomeacoes.data_termino NULL, conforme o relacionamento nomeacoesAtivas.</li><li>Exclusão lógica: deleted_at preenchido. Não significa exclusão física nem inatividade em todos os módulos.</li><li>Leitura, validade e expiração dependem de lido_em, data_validade/expires_at e da regra do módulo; não se deduzem de um status genérico.</li></ul></div>';
$html .= '<div class="section" id="catalogos"><h2>Catálogos de referência</h2><p>Valores de configuração consultados neste ambiente. IDs e descrições podem variar entre bases; não substituir os IDs das FKs pelos códigos de negócio. deleted_at preenchido significa linha logicamente excluída.</p>';
foreach ($catalogs as $table => $rows) {
    $html .= '<h3>'.h($table).'</h3>';
    if (!$rows) { $html .= '<p>Catálogo sem linhas neste ambiente.</p>'; continue; }
    $html .= '<table><thead><tr>';
    foreach (array_keys($rows[0]) as $key) $html .= '<th>'.h($key).'</th>';
    $html .= '</tr></thead><tbody>';
    foreach ($rows as $row) { $html .= '<tr>'; foreach ($row as $value) $html .= '<td>'.h($value ?? 'NULL').'</td>'; $html .= '</tr>'; }
    $html .= '</tbody></table>';
}
$html .= '</div><div class="section" id="views"><h2>Views e regras físicas adicionais</h2><p>Definições SQL consultadas no schema. Uma view não cria automaticamente novas FKs nas tabelas de origem.</p>';
foreach ($views as $view) $html .= '<h3>'.h($view['table_name']).'</h3><p>Atualizável segundo o banco: '.h($view['is_updatable']).'; CHECK OPTION: '.h($view['check_option']).'.</p><pre>'.h($view['view_definition'] ?? 'Definição não acessível com as permissões atuais.').'</pre>';
$html .= '<h3>Triggers</h3>';
if (!$triggers) $html .= '<p>Nenhum trigger informado pelo information_schema para este schema e usuário de conexão.</p>';
foreach ($triggers as $trigger) $html .= '<h4>'.h($trigger['trigger_name']).'</h4><p>'.h($trigger['action_timing'].' '.$trigger['event_manipulation'].' em '.$trigger['event_object_table']).'</p><pre>'.h($trigger['action_statement']).'</pre>';
$html .= '<h3>Reprodução e manutenção</h3><p>Execute '.code('php docs/base_dados/gerar.php').' na raiz do projeto para gerar um novo snapshot do banco configurado. '.code('--sem-pdf').' omite a renderização PDF. O script não executa migrations, seeders, inserts, updates ou deletes no banco. Revise '.code('docs/base_dados/catalogo.php').' quando os significados funcionais mudarem.</p><p>Artefatos: '.code('documentacao_base_completa.html').', '.code('documentacao_base_completa.pdf').', '.code('base_dados/schema.json').', '.code('base_dados/modelo_relacional.mmd').', '.code('base_dados/modelo_relacional.svg').' e diagramas por módulo. O HTML inclui as imagens e não depende de CDN ou acesso externo para leitura.</p></div></body></html>';
file_put_contents(dirname($out).'/documentacao_base_completa.html', $html);
echo 'HTML e modelo gerados: '.$baseCount.' tabelas, '.count($views).' views, '.count($columns).' colunas, '.$fkCount.' FKs, '.$logicalCount.' vinculos logicos, '.count($domains)." campos de dominio.\n";
if (!in_array('--sem-pdf', $argv, true)) {
    ini_set('memory_limit', '1024M');
    $options = new Dompdf\Options;
    $options->set('isRemoteEnabled', false); $options->set('isPhpEnabled', false); $options->set('defaultFont', 'DejaVu Sans');
    $options->set('defaultMediaType', 'print');
    $pdf = new Dompdf\Dompdf($options); $pdf->loadHtml($html, 'UTF-8'); $pdf->setPaper('A4', 'portrait'); $pdf->render();
    $canvas = $pdf->getCanvas();
    $font = $pdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
    $canvas->page_text(32, 814, 'IMWplus | Base de dados | Uso interno', $font, 7, [0.35,0.4,0.48]);
    $canvas->page_text(455, 814, 'Pagina {PAGE_NUM} de {PAGE_COUNT}', $font, 7, [0.35,0.4,0.48]);
    file_put_contents(dirname($out).'/documentacao_base_completa.pdf', $pdf->output());
    echo 'PDF gerado: '.$canvas->get_page_count()." paginas.\n";
}
