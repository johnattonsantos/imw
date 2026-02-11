# Documentacao de Usabilidade dos Modulos - Sistema IMW

## Indice clicavel por modulo
- [Dashboard](#31-dashboard)
- [Comunicacao](#32-comunicacao)
- [Categoria Comunicacao](#33-categoria-comunicacao)
- [Tipos de Arquivo](#34-tipos-de-arquivo)
- [Secretaria](#35-secretaria)
- [Instituicoes](#36-instituicoes)
- [Igrejas](#37-igrejas)
- [Clerigos](#38-clerigos)
- [Financeiro](#39-financeiro)
- [Relatorios Distritais](#310-relatorios-distritais)
- [Relatorios Regionais](#311-relatorios-regionais)
- [Estatisticas](#312-estatisticas)
- [SRA / Contabilidade](#313-sra--contabilidade)
- [Congregacoes](#314-congregacoes)
- [GCEU](#315-gceu)
- [Seguranca](#316-seguranca)
- [Perfil](#317-perfil)
- [Trocar Instituicao](#318-trocar-instituicao)

## Versoes por publico
- [Guia Operacional](./usabilidade_modulos_operacional.md)
- [Guia de Gestao](./usabilidade_modulos_gestao.md)
- [Guia Tecnico (TI)](./usabilidade_modulos_ti.md)

## 1. Objetivo do documento
Este material orienta o uso funcional dos modulos disponiveis no sistema, com foco em:
- fluxo operacional do dia a dia;
- boas praticas de preenchimento;
- navegacao recomendada;
- erros comuns e como evitar.

## 2. Regras gerais de usabilidade
- O menu exibido depende do perfil e das regras de permissao (`seguranca:*`).
- Sempre confirme a instituicao ativa antes de operar em cadastros e financeiro.
- Em telas com filtros, aplique primeiro periodo/unidade e depois detalhes (nome, status, categoria).
- Em formularios com editor rico, valide o conteudo visual antes de salvar.
- Em exclusoes, revise se o registro possui vinculos (alguns modulos bloqueiam exclusao com dependencia).

## 3. Modulos e uso recomendado

### 3.1 Dashboard
**Objetivo:** visao inicial de acesso e atalhos operacionais.

**Como usar melhor:**
- confirme perfil/instituicao no topo lateral;
- use como ponto de entrada para rotinas do dia.

### 3.2 Comunicacao
**Objetivo:** cadastrar, editar, consultar e exportar comunicados institucionais.

**Telas principais:** lista, cadastro/edicao (modal AJAX), detalhes, exportacao XLSX/PDF.

**Fluxo recomendado:**
1. Acesse `Comunicacao`.
2. Filtre por texto se necessario.
3. Clique em `Novo`.
4. Selecione **Categoria**.
5. Preencha `Titulo` e `Comentario`.
6. Anexe arquivo (formatos permitidos: PDF, imagem, Word, Excel, ZIP, RAR).
7. Salve e valide na lista.

**Boas praticas:**
- padronize titulos curtos e claros;
- use categorias para facilitar busca e exportacao;
- valide o icone do arquivo antes de abrir (tipo do documento).

**Erros comuns:**
- tentar salvar sem categoria;
- colar HTML indevido no comentario;
- anexar formato nao permitido.

### 3.3 Categoria Comunicacao
**Objetivo:** manter tabela de categorias usadas no modulo Comunicacao.

**Telas principais:** lista, criar (modal), editar (modal), excluir.

**Fluxo recomendado:**
1. Cadastre categorias antes de criar comunicados.
2. Use nomes unicos e padronizados.

**Regra importante:**
- nao e possivel excluir categoria vinculada a comunicacoes existentes.

### 3.4 Tipos de Arquivo
**Objetivo:** manter referencia de tipos aceitos/operacionais para anexos.

**Telas principais:** lista, criar, editar, excluir (AJAX).

**Boas praticas:**
- manter nomenclatura objetiva;
- revisar periodicamente para evitar redundancia.

### 3.5 Secretaria
Submodulos: **Membros**, **Congregados**, **Visitantes**, e relatorios de secretaria.

#### Membros
**Objetivo:** gestao de cadastro, disciplina, transferencias e historico.

**Uso recomendado:**
- atualizar dados antes de executar processos (transferencia, exclusao, reintegracao);
- revisar validacoes de igreja/instituicao ao editar.

#### Congregados e Visitantes
**Objetivo:** controle de pessoas em acompanhamento.

**Uso recomendado:**
- padronizar identificacao e contatos;
- converter fluxos para membro somente apos validacao interna.

#### Relatorios de Secretaria
- Membresia
- Aniversariantes
- Membros por Ministerio
- Membros Disciplinados
- Funcao Eclesiastica

**Boas praticas:**
- sempre filtrar antes de exportar;
- revisar volume de dados para evitar relatorio inconsistente.

### 3.6 Instituicoes
**Objetivo:** visao e administracao de unidades institucionais por contexto de acesso.

**Uso recomendado:**
- alinhar com `Trocar Instituicao` antes de qualquer alteracao cadastral.

### 3.7 Igrejas
**Objetivo:** consulta operacional de igrejas e relatorios por igreja.

**Telas comuns:** listagem, balancete, movimento diario, livro razao.

**Boas praticas:**
- confirmar igreja alvo antes de gerar demonstrativos;
- comparar datas e periodos com financeiro.

### 3.8 Clerigos
Submodulos: cadastro/listagem e prebendas.

**Objetivo:** manutencao ministerial e informacoes funcionais.

**Boas praticas:**
- manter status/categoria atualizados para refletir relatorios regionais;
- revisar dependencias com impostos e prebendas.

### 3.9 Financeiro
Submodulos principais:
- Movimento de Caixa
- Consolidacao de Caixa
- Cota Orcamentaria
- Plano de Conta
- Caixas
- Fornecedores
- Relatorios (Movimento Diario, Livro Caixa, Balancete, Livro Grade, Livro Razao, Movimento Bancario)

**Fluxo recomendado:**
1. Configurar plano de contas/caixas.
2. Lancar entradas/saidas/transferencias.
3. Consolidar.
4. Validar em relatorios.

**Boas praticas:**
- evitar lancamentos sem classificacao;
- anexar comprovantes quando aplicavel;
- fechar periodo com consolidacao validada.

### 3.10 Relatorios Distritais
Grupos: Financeiro, Membresia, Igrejas, GCEU.

**Objetivo:** acompanhamento consolidado por distrito.

**Uso recomendado:**
- usar filtros de periodo e unidade antes de gerar PDF;
- conferir consistencia com dados de origem (financeiro/membresia).

### 3.11 Relatorios Regionais
Grupos: Financeiro, Membresia, Clerigos, Igrejas, GCEU.

**Objetivo:** consolidacao gerencial por regiao.

**Boas praticas:**
- executar em horario de menor carga quando houver alto volume;
- padronizar periodo de comparacao entre relatorios.

### 3.12 Estatisticas
Grupos: Membresia, Totalizacao, Top 10 distritos/igrejas, Clerigos, Ticket medio.

**Objetivo:** analise executiva e historica.

**Uso recomendado:**
- manter filtros consistentes entre consultas;
- usar exportacoes para apresentacoes e comites.

### 3.13 SRA / Contabilidade
Submodulos principais: IRRF e Balancete.

**Objetivo:** apoio contábil com visao institucional.

**Boas praticas:**
- validar dados financeiros consolidados antes da emissao;
- revisar competencia e periodo contabil.

### 3.14 Congregacoes
**Objetivo:** cadastro, manutencao e controle de status de congregacoes.

**Uso recomendado:**
- usar desativacao/restauracao em vez de exclusao definitiva sempre que possivel.

### 3.15 GCEU
Submodulos:
- Cadastro
- Membros
- Carta Pastoral
- Diario
- Relatorios (listas, funcoes, aniversariantes)

**Boas praticas:**
- manter rotina de atualizacao diaria no diario;
- validar publicacao de carta pastoral antes de divulgar.

### 3.16 Seguranca
Submodulo: **Auditorias**.

**Objetivo:** rastreabilidade de eventos do sistema.

**Uso recomendado:**
- filtrar por usuario, evento e periodo;
- usar exportacao para analise de incidente;
- revisar IP e metadados quando necessario.

### 3.17 Perfil
Submodulos:
- Dados Pessoais
- Carteira Digital
- Dependentes
- Prebendas
- Imposto de Renda
- Informe de Rendimentos

**Boas praticas:**
- manter dados pessoais atualizados;
- validar dados fiscais antes de emissao de informes.

### 3.18 Trocar Instituicao
**Objetivo:** mudar contexto operacional sem novo login.

**Regra de uso:**
- sempre trocar antes de cadastrar/editar registros para evitar operacao na instituicao errada.

## 4. Checklist rapido de uso correto
- Perfil/instituicao corretos?
- Permissao do modulo ativa?
- Filtros aplicados antes de exportar?
- Campos obrigatorios preenchidos?
- Dependencias verificadas antes de excluir?

## 5. Problemas frequentes e acao recomendada
- **Modulo nao aparece no menu:** revisar regra de perfil.
- **Erro ao excluir registro:** verificar vinculos ativos.
- **Dados divergentes em relatorio:** validar periodo/filtros e origem dos lancamentos.
- **Arquivo nao abre:** conferir tipo de arquivo e permissao de acesso.

## 6. Manutencao deste documento
Este e um documento vivo. Atualizar sempre que:
- novo modulo for criado;
- fluxo de negocio mudar;
- permissao/regra de acesso for alterada;
- campos obrigatorios forem modificados.
