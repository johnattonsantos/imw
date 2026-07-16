#!/usr/bin/env python3
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
FILES = [
    ROOT / 'public/custom/js/imw_datatables.js',
    ROOT / 'public/igrejas/js/index.js',
    ROOT / 'public/igrejas/js/index-igrejas-regiao.js',
    ROOT / 'public/congregacoes/js/index.js',
    ROOT / 'public/congregados/js/index.js',
    ROOT / 'public/membros/js/index.js',
    ROOT / 'public/membros/js/indexRecadastramento.js',
    ROOT / 'public/visitantes/js/index.js',
    ROOT / 'public/gceu/js/index.js',
    ROOT / 'public/gceu/js/cartaPastoral.js',
    ROOT / 'public/perfil/clerigos/dependentes/js/index.js',
    ROOT / 'public/perfil/clerigos/prebendas/js/index.js',
    ROOT / 'public/theme/assets/js/pages/movimentocaixa.js',
]
PT_BR = ROOT / 'lang/pt_BR.json'

catalog = json.loads(PT_BR.read_text(encoding='utf-8'))
changed_files = 0
changed_strings = 0

visible_context = re.compile(
    r"(?P<prefix>\b(?:title|confirmButtonText|cancelButtonText|sProcessing|sInfo|sZeroRecords|sPrevious|sNext)\s*:\s*)"
    r"(?P<quote>['\"])(?P<text>.*?[A-Za-zÀ-ÿ].*?)(?P=quote)",
    re.S,
)
toastr_context = re.compile(
    r"(?P<prefix>\btoastr\.(?:error|success|warning|info)\(\s*)"
    r"(?P<quote>['\"])(?P<text>.*?[A-Za-zÀ-ÿ].*?)(?P=quote)",
    re.S,
)

skip = re.compile(r"(__\(|\$|\{\{|\}\}|function|return|data:|name:|url:|type:|class=|<span|<div class=\"spinner)")

def js_expr(text):
    escaped = text.replace('\\', '\\\\').replace("'", "\\'")
    return f"__('{escaped}')"


def replace_match(match):
    global changed_strings
    text = re.sub(r"\s+", " ", match.group('text')).strip()
    raw_text = match.group('text')
    if not text or skip.search(raw_text):
        return match.group(0)
    if match.group(0).find('__(') >= 0:
        return match.group(0)
    catalog.setdefault(text, text)
    changed_strings += 1
    return match.group('prefix') + js_expr(text)

for path in FILES:
    if not path.exists():
        continue
    original = path.read_text(encoding='utf-8')
    content = visible_context.sub(replace_match, original)
    content = toastr_context.sub(replace_match, content)

    # Template literals with static Portuguese fragments mixed with dynamic data.
    content = content.replace('(transferido(a) para ${row.igreja_atual})', "${__('(transferido(a) para')} ${row.igreja_atual})")
    content = content.replace('(Em transferência para ${row.notificacao_transferencia_ativa.igreja_destino.nome})', "(${__('Em transferência para')} ${row.notificacao_transferencia_ativa.igreja_destino.nome})")
    catalog.setdefault('(transferido(a) para', '(transferido(a) para')
    catalog.setdefault('Em transferência para', 'Em transferência para')

    if content != original:
        path.write_text(content, encoding='utf-8')
        changed_files += 1

PT_BR.write_text(json.dumps(dict(sorted(catalog.items(), key=lambda item: item[0].lower())), ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
print(f'Arquivos JS alterados: {changed_files}')
print(f'Textos JS convertidos: {changed_strings}')
