# Guia de Usabilidade - Perfil Tecnico (TI)

## Objetivo
Orientar equipe tecnica na sustentacao funcional do sistema sob perspectiva de acesso, configuracao e suporte.

## Escopo tecnico
- Perfis e regras de seguranca (`seguranca:*`)
- Modulos condicionados por permissao no menu
- Auditoria de eventos
- Contexto de instituicao ativa
- Integracoes de arquivo (local/S3)

## Rotina recomendada de suporte
1. Validar perfil/permissao do usuario.
2. Confirmar instituicao ativa no momento do erro.
3. Reproduzir fluxo no modulo afetado.
4. Consultar auditoria (usuario, evento, periodo, IP).
5. Corrigir regra/configuracao e retestar.

## Pontos criticos de configuracao
### Permissoes
- Acesso aos modulos depende de `hasPerfilRegra(...)` no menu e middlewares `seguranca:*` nas rotas.

### Arquivos
- Modulo Comunicacao usa disco configuravel (`COMUNICACAO_FILESYSTEM_DISK`).
- Para S3, validar credenciais AWS e expiracao de URL temporaria.

### Exportacoes
- Verificar filtros antes da geracao de XLSX/PDF para evitar carga desnecessaria.

## Diagnostico de problemas comuns
- Modulo nao visivel: regra de perfil ausente.
- Exclusao bloqueada: dependencia relacional ativa.
- Dados em HTML na tabela: sanitizacao/normalizacao na camada de exibicao.
- Erro de upload: extensao nao permitida ou tamanho acima do limite.

## Checklist tecnico de liberacao
- Rotas e middlewares revisados.
- Permissoes por perfil validadas.
- Auditoria registrando eventos esperados.
- Upload/download/visualizacao de arquivo ok.
- Exportacoes e filtros sem regressao.
