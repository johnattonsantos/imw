# Prompt: Módulo de Chat para Pastores em Comunicação

## Objetivo
Criar, futuramente, um módulo dentro de **Comunicação** chamado **Chat de Pastores**, permitindo que pastores locais se comuniquem dentro do sistema por mensagens privadas ou por grupos institucionais definidos pela região e pelo distrito.

Este documento descreve as regras funcionais, permissões, validações e fluxo esperado. Não implementar o módulo ainda.

## Local do módulo
- Menu principal: **Comunicação**
- Submenu sugerido: **Chat de Pastores**

## Perfis e permissões
- **Pastor local**: pode acessar o chat, enviar e receber mensagens conforme seu vínculo institucional ativo.
- **Pastor com nomeação ativa**: deve ser considerado elegível para envio e recebimento.
- **Pastor inativo, sem nomeação ativa ou desligado**: não deve aparecer como destinatário e não deve receber novas mensagens.
- **Administrador regional/SRA**: pode ser incluído futuramente como perfil de moderação, parametrização ou auditoria, se a regra de negócio permitir.

## Escopos de envio
O remetente poderá escolher um dos seguintes destinos:

### 1. Pastor específico
Permite enviar mensagem para um pastor específico.

Regras:
- O destinatário deve existir no cadastro de clérigos/pessoas.
- O destinatário deve estar ativo.
- O destinatário deve possuir nomeação ativa.
- O destinatário deve pertencer ao escopo institucional permitido: região, distrito ou igreja/local conforme vínculo do remetente.
- A busca deve permitir localizar por nome, CPF, igreja, distrito ou região, respeitando permissões.

### 2. Todos os pastores do distrito
Permite enviar mensagem para todos os pastores ativos de um distrito.

Regras:
- Para pastor local, o distrito padrão deve ser o distrito da sua igreja/nomeação atual.
- Todos os pastores com nomeação ativa naquele distrito devem receber/ver a mensagem.
- Pastores sem nomeação ativa não entram na lista de destinatários.
- A mensagem deve registrar o distrito de destino.

### 3. Todos os pastores da região
Permite enviar mensagem para todos os pastores ativos da região.

Regras:
- A região padrão deve ser a região da nomeação atual do remetente.
- Todos os pastores com nomeação ativa naquela região devem receber/ver a mensagem.
- Pastores desligados, inativos ou sem nomeação ativa não entram na lista de destinatários.
- A mensagem deve registrar a região de destino.

## Regras de visualização
- Mensagem privada: visível apenas para remetente e destinatário específico.
- Mensagem para distrito: visível para todos os pastores ativos do distrito definido.
- Mensagem para região: visível para todos os pastores ativos da região definida.
- O sistema deve manter histórico das mensagens, participantes, datas de envio e leituras.
- Recomenda-se registrar os participantes resolvidos no momento do envio para preservar o histórico mesmo que o pastor mude de igreja, distrito ou região depois.

## Regras de envio
- O remetente deve estar autenticado.
- O remetente deve ter permissão para acessar **Comunicação > Chat de Pastores**.
- O remetente deve possuir vínculo pastoral válido e ativo.
- A mensagem deve ter conteúdo obrigatório.
- O sistema deve validar o tipo de destino antes de salvar.
- O sistema deve impedir envio para pastor fora do escopo permitido.
- O sistema deve impedir envio para destinatários inativos ou sem nomeação ativa.

## Dados mínimos da conversa
Uma conversa deve conter:
- Tipo da conversa: `PRIVADA`, `DISTRITO` ou `REGIAO`.
- Remetente.
- Destinatário específico, quando privada.
- Distrito de destino, quando distrital.
- Região de destino, quando regional.
- Data/hora de criação.
- Status da conversa: ativa, arquivada ou encerrada.

## Dados mínimos da mensagem
Uma mensagem deve conter:
- Conversa vinculada.
- Remetente.
- Conteúdo da mensagem.
- Data/hora de envio.
- Status de entrega.
- Controle de leitura por participante.

## Auditoria e histórico
Registrar auditoria para:
- Criação de conversa.
- Envio de mensagem.
- Leitura de mensagem.
- Arquivamento/encerramento de conversa.
- Falha de validação por escopo ou destinatário inativo.

## Notificações
Ao enviar uma mensagem:
- Notificar destinatário específico, se conversa privada.
- Notificar todos os pastores elegíveis do distrito, se conversa distrital.
- Notificar todos os pastores elegíveis da região, se conversa regional.

## Regras de segurança
- Não permitir acesso anônimo.
- Não permitir que usuário sem perfil pastoral leia mensagens.
- Não permitir que pastor acesse mensagens de outra região ou distrito fora do seu escopo.
- Não expor CPF ou dados sensíveis no corpo da conversa sem necessidade.
- Garantir que consultas respeitem instituição, região, distrito e igreja do usuário logado.

## Fluxo esperado
1. Usuário acessa **Comunicação > Chat de Pastores**.
2. Sistema valida se o usuário é pastor elegível.
3. Sistema carrega conversas existentes do escopo permitido.
4. Usuário clica em **Nova conversa**.
5. Usuário escolhe o tipo de destino: pastor específico, distrito ou região.
6. Sistema valida o escopo e resolve os destinatários elegíveis.
7. Usuário digita a mensagem.
8. Sistema salva a conversa e a mensagem.
9. Sistema registra participantes, auditoria e notificações.
10. Destinatários visualizam a mensagem conforme regra de escopo.

## Critérios de aceite
- Pastor local consegue enviar mensagem para outro pastor elegível dentro do escopo permitido.
- Pastor local consegue enviar mensagem para todos os pastores do seu distrito, quando habilitado.
- Pastor local consegue enviar mensagem para todos os pastores da sua região, quando habilitado.
- Pastor inativo ou sem nomeação ativa não aparece como destinatário.
- Mensagens distritais são visíveis para todos os pastores elegíveis do distrito.
- Mensagens regionais são visíveis para todos os pastores elegíveis da região.
- O histórico de envio, leitura e participantes é preservado.
- O sistema registra auditoria das principais ações.
