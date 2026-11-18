# Gestao de Acessos

Menu: Gestao de Acessos. URL: `/regiao/relatorio/gestao-acessos`.
Apenas o perfil Administrador Regiao selecionado na propria regiao, com a regra
`regiao-gestao-acessos`, pode acessar. A identidade e a regiao sao resolvidas
no servidor. Parametros da URL nao permitem selecionar outra regiao.

## Publicacao

Alternativa para execucao manual no MySQL:
`docs/sql/gestao_acessos_producao_18-11-2026.sql`.
O arquivo cria as estruturas, concede a permissao ao Administrador Regiao e
registra as duas migracoes no controle do Laravel. Preserva dados e horarios de
monitoramento existentes. Fazer backup e interromper a execucao em qualquer erro;
DDL do MySQL nao permite rollback atomico do script inteiro.

Para aplicar pelo Laravel em vez do arquivo SQL:

```sh
php artisan migrate --path=database/migrations/2026_11_18_120000_create_gestao_acessos.php --force
php artisan migrate --path=database/migrations/2026_11_18_130000_create_gestao_acessos_sessoes.php --force
php artisan route:clear
php artisan view:clear
```

Ha alteracao de banco. A primeira migracao cria a coleta diaria e sua configuracao,
alem da regra e dos vinculos de permissao. A segunda cria
`gestao_acessos_sessoes`, `gestao_acessos_sessao_perfis` e acrescenta
`inicio_sessoes` na configuracao. Se a primeira ja foi aplicada, executar apenas
a segunda. Publicar em manutencao e aplicar as migracoes antes de liberar o sistema.
Nao ha reconstrucao retroativa de sessoes/perfis. Logins antigos comprovados na
auditoria podem aparecer com perfil e sessao nao registrados.
O rollback nao remove o historico coletado.

## Interpretacao

O relatorio inclui eventos `login` de `audits` na mesma tabela de acessos, sem
um painel separado de auditoria. Primeiro acesso e o horario de login; ultimo
acesso e o logout da MESMA sessao quando identificada. Novos eventos de
login/logout em `audits` recebem tambem um UUID independente para correlacao,
sem alterar registros antigos. Nao se tenta parear antigos logins e logouts por
usuario, IP, dispositivo ou proximidade de horario.

### Logins antigos

- Data/hora vem de `audits.created_at`, somente para evento `login` de usuario
  identificado como `App\Models\User`. Nao inclui logout, tentativas falhas ou
  alteracoes em cadastros como se fossem login.
- Perfil e sessao aparecem como nao registrados, e nao se inventa um logout.
- Instituicao registrada na auditoria deve estar na hierarquia da regiao logada.
- Login sem instituicao so aparece se o usuario tiver vinculos atuais na regiao
  e nenhum vinculo em outra instituicao fora dela. O texto informa que a
  instituicao historica e desconhecida e que a selecao usa o vinculo atual;
  nao comprova que o login antigo ocorreu nessa regiao.
- Usuarios com vinculos em mais de uma regiao ou instituicoes nao classificadas
  nao tem seus logins sem instituicao atribuidos a uma das regioes por suposicao.
- Eventos correlacionados por `gestao_sessao_id` a uma sessao registrada nao sao
  duplicados. JSON antigo invalido e tratado como sem identificador.
- Usa a tabela configurada em `audit.drivers.database.table` na conexao principal
  do aplicativo. Nao importa nem modifica dados antigos.
- A paginacao e unica para as sessoes e os logins antigos e mantem os filtros.

- Uma linha por sessao/perfil/instituicao efetivamente utilizados. Troca de perfil
  nao gera login: os perfis visitados compartilham o login/logout da sessao.
- Uso de perfil e registrado apos GET autenticado bem-sucedido, nao em AJAX,
  JSON, erros ou selecao de perfil. O intervalo de uso observado e atualizado
  aproximadamente a cada cinco minutos.
- Sem logout explicito: "Saida nao registrada". Fechar o navegador ou expirar a
  sessao nao equivale a sair pelo sistema. Nao se afirma que a sessao segue online.
- Sessoes existentes antes da atualizacao, inclusive restauradas por remember-me,
  tem login desconhecido; o horario da primeira pagina nao e inventado como login.
- Nao sao armazenados IP, senha nem token de autenticacao nas tabelas de Gestao
  de Acessos. O UUID e aleatorio e independente do identificador real de sessao.
- Filtros de dia/mes/ano selecionam intervalos de uso de perfil que se sobrepoem
  ao periodo, ate o ultimo uso observado ou logout. Login/logout originais sao
  mostrados completos, inclusive quando estao fora do periodo.
- Busca por nome ou parte do nome aplica-se a sessoes e logins antigos na mesma
  consulta, sem alterar o isolamento regional. Mantem-se ao trocar a pagina;
  campo vazio nao restringe usuarios. Caracteres `%` e `_` sao tratados literalmente.
- Nomes de usuario, perfil e instituicao sao preservados na primeira utilizacao
  de cada perfil da sessao. A regiao e validada no servidor e registrada nesse momento.
- O quadro mostra sessoes e logins registrados, nao usuarios que nunca acessaram.
- Nao ha quadros mensais separados de pastores ou SDs.
- Usuario com multiplos perfis aparece apenas nos perfis efetivamente utilizados.
- Sem registro em mes parcialmente monitorado nao significa ausencia no mes todo.
- O quadro nao substitui um historico de nomeacoes ou de responsaveis anteriores.
- Falhas no registro nao bloqueiam o sistema e geram aviso no log; lacunas durante
  indisponibilidade do monitoramento precisam ser consideradas na interpretacao.

O historico diario anterior continua armazenado, mas nao e convertido em sessoes
nem usado para inventar horarios de login/logout.

Validacao: `vendor/bin/phpunit --filter='GestaoAcessosTest|SessaoAcessoTest'`.
