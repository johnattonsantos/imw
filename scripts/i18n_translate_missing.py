#!/usr/bin/env python3
import json
import re
import time
import urllib.parse
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
BASE = ROOT / 'lang' / 'pt_BR.json'
LOCALES = {
    'pt_PT': 'pt-PT',
    'en': 'en',
    'fr': 'fr',
    'it': 'it',
    'es': 'es',
    'ja': 'ja',
}
DELIMITER = '\n<<<I18N_SPLIT>>>\n'
CHUNK_SIZE = 25
SLEEP_SECONDS = 0.15
PLACEHOLDER_RE = re.compile(r':[A-Za-z_][A-Za-z0-9_]*')


def load_json(path):
    if path.exists():
        with path.open(encoding='utf-8') as fh:
            data = json.load(fh)
            if isinstance(data, dict):
                return data
    return {}


def dump_json(path, data):
    with path.open('w', encoding='utf-8') as fh:
        json.dump(dict(sorted(data.items(), key=lambda item: item[0].lower())), fh, ensure_ascii=False, indent=2)
        fh.write('\n')


def protect(text):
    placeholders = []

    def repl(match):
        placeholders.append(match.group(0))
        return f'__PH{len(placeholders)-1}__'

    return PLACEHOLDER_RE.sub(repl, text), placeholders


def restore(text, placeholders):
    for index, placeholder in enumerate(placeholders):
        text = text.replace(f'__PH{index}__', placeholder)
        text = text.replace(f'__PH {index}__', placeholder)
    return text.strip()


def translate_batch(items, target):
    protected_items = []
    placeholder_sets = []
    for item in items:
        protected, placeholders = protect(item)
        protected_items.append(protected)
        placeholder_sets.append(placeholders)

    query = DELIMITER.join(protected_items)
    params = urllib.parse.urlencode({
        'client': 'gtx',
        'sl': 'pt',
        'tl': target,
        'dt': 't',
        'q': query,
    })
    url = 'https://translate.googleapis.com/translate_a/single?' + params

    with urllib.request.urlopen(url, timeout=30) as response:
        payload = json.loads(response.read().decode('utf-8'))

    translated = ''.join(segment[0] for segment in payload[0])
    parts = [part.strip() for part in translated.split('<<<I18N_SPLIT>>>')]

    if len(parts) != len(items):
        return [translate_batch([item], target)[0] for item in items]

    return [restore(part, placeholders) for part, placeholders in zip(parts, placeholder_sets)]


def chunks(items, size):
    for i in range(0, len(items), size):
        yield items[i:i + size]


def main():
    base = load_json(BASE)

    for locale, target in LOCALES.items():
        path = ROOT / 'lang' / f'{locale}.json'
        catalog = load_json(path)
        missing = [key for key in base.keys() if key not in catalog or not str(catalog[key]).strip()]
        print(f'{locale}: traduzindo {len(missing)} pendencias')

        for batch in chunks(missing, CHUNK_SIZE):
            try:
                translations = translate_batch(batch, target)
            except Exception as exc:
                print(f'Falha no lote {locale}: {exc}')
                translations = batch

            for key, translated in zip(batch, translations):
                catalog[key] = translated or key

            dump_json(path, catalog)
            time.sleep(SLEEP_SECONDS)

    print('Concluido')


if __name__ == '__main__':
    main()
