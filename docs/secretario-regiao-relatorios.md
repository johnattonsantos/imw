# Relatorios regionais do Secretario Regiao

O perfil regional `Secretário(a) Região` deve acessar os relatorios de EBD,
membresia, clerigos, igrejas, patrimonio e GCEU. O bloco Financeiro permanece
restrito, inclusive nas URLs diretas e nas exportacoes PDF.

## Aplicacao

Depois de publicar o codigo, execute a migracao de permissoes:

```sh
php artisan migrate --path=database/migrations/2026_11_13_120000_grant_non_financial_regional_reports_to_secretary.php --force
php artisan route:clear
php artisan view:clear
```

A migracao apenas ajusta vinculos existentes em `perfil_regra`, usando as regras
ativas em `regras`. Nao cria tabelas nem modifica a estrutura do banco. Considera
somente perfis regionais ativos com os nomes de secretario previstos na migracao;
nao altera administradores, tesoureiros ou secretarios locais e distritais.
Tambem remove desse perfil as permissoes de relatorios financeiros listadas na
migracao. Pode ser reaplicada sem duplicar vinculos.

O rollback e intencionalmente vazio: nao e possivel reconstruir com seguranca
quais permissoes o perfil tinha antes da correcao.

Relatorios desabilitados anteriormente nao sao reativados. A secao Estatisticas
fora de Relatorios Regionais nao recebe uma liberacao geral.

## Verificacao

```sh
vendor/bin/phpunit --filter=SecretarioRegiaoRelatoriosTest
```

Com o perfil Secretario Regiao selecionado, conferir os blocos nao financeiros
do menu e o acesso aos relatorios e suas exportacoes. Confirmar que Saldo de
Caixas, Livro Razao, Orcamento, Variacao Financeira, IRRF, Balancete e demais
itens financeiros nao aparecem e retornam 403 no acesso direto.
