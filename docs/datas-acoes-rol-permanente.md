# Datas de acoes no rol permanente

Novas acoes usam a data atual do sistema (America/Sao_Paulo), definida no
servidor, exceto no processo de Membresia Validacao/Recadastramento.
Nas outras acoes os campos sao somente leitura e o navegador nao permite
registrar uma acao retroativa ou futura.

| Tela / acao | Campo da tela | Gravacao no rol |
| --- | --- | --- |
| Receber novo membro / promover congregado | dt_recepcao | Recepcao de hoje |
| Reintegrar membro | dt_recepcao | Novo registro com recepcao de hoje |
| Transferir para outra igreja | dt_notificacao | Notificacao aberta hoje; rol fechado ao aceitar |
| Aceitar transferencia | dt_resposta | Exclusao na origem e recepcao no destino no dia do aceite |
| Rejeitar transferencia | dt_resposta | Resposta de hoje; nao altera o rol |
| Transferencia interna entre congregacoes | dt_transferencia | Fecha o registro anterior e recebe no novo registro hoje |
| Excluir membro | dt_exclusao | Exclusao de hoje, apenas na igreja atual |
| Editar membro | dt_recepcao, dt_exclusao | Preserva datas existentes; novas recepcoes/exclusoes usam hoje |
| Membresia Validacao/Recadastramento | dt_recepcao, dt_exclusao | Permite informar e gravar datas historicas; exclusao nao pode anteceder recepcao |

Editar dados pessoais nao atualiza datas historicas. Reativar um membro com
rol encerrado cria uma nova recepcao, sem apagar a exclusao anterior.
A validacao que cria um registro oficial novo usa as datas historicas informadas.

As alteracoes nao corrigem automaticamente registros antigos. Casos excepcionais
devem ser justificados e aprovados antes de uma correcao manual no banco, com
ciencia dos efeitos nos relatorios ja emitidos.

Nao ha tabela, migration, regra de acesso ou SQL de implantacao novo.
