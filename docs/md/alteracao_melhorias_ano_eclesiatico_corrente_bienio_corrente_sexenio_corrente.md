Esta regra do ano eclesiástico e biênio, já tínhamos falado outras vezes, inclusive pq tb ocorria o mesmo no mapão. 
- Ano eclesiástico corrente: 1/11 do ano anterior até a data atual (no dia 31/10 termina um ano eclesiástico, começa o novo)
- Biênio corrente: 01/11 do último ano par, até o dia atual
- Sexênio corrente: Um pouco mais complicado de fixar ancora no início, pq não tem uma regra como é a do bienio, que só usa o ano par. O mais simples é saber que todo ano de bienio tem MOD (ano,6)=6, ou seja, o resto da divisão do ano de início do sexênio por 6 é sempre = 4. Daí, vc pode usar o seguinte código no PHP:
$anoEclesiastico = (date('m-d') < '11-01')
    ? (int) date('Y')
    : (int) date('Y') + 1;
$anoInicioSexenio = $anoEclesiastico
    - ((($anoEclesiastico % 6) - 4 + 6) % 6);
    
Este código te dá o início do ano do sexenio corrente, já tratando a questão de que a partir de 1/11 já muda o ano eclesiástico e, portanto, muda o sexenio no ano específico (ou seja, 1/11/2026 já estaremos em um novo sexenio)

Daí, é só usar o str_to_date na query pra fixar este ano como início do período, sempre com 1/11 como ponto de partida (1/11/$anoInicioSexenio)

Ahhhh, e "Ano eclesiástico" = "anuenio".

Tem um erro nos relatórios por causa do Carbon, e que afeta muitos relatórios do sistema.

Carbon::createFromFormat('m/Y', $dt_inicial)->startOfMonth()->format('Y-m-d').

Quando usa isso aqui passando mes e ano, ele preenche o dia sozinho pra montar o range... como hoje é dia 31, e eu tento pegar um relatório de fevereiro, ele tenta usar 29/2. Como não tem, ele pula pra março. Então, quando eu tento hoje pegar um balancete de fevereiro, de qualquer ano, ele monta a query assim:
AND fl.data_movimento BETWEEN '2026-03-01' AND '2026-03-31'

E se tentar puxar o balancete no dia 31, de um mês que só tenha 30 dias, ele tb vai pular para o mes seguinte

E não afeta nada na tela, só a query, então o usuário acha que está tudo certo

No máximo, vai estranhar pq, obviamente, os valores e lançamentos que virão estarão todos equivocados

A solução poder ser Adicionar "!" no início da string de formato — isso instrui o Carbon a resetar todos os campos não especificados (dia, hora) para os valores padrão (dia 1, meia-noite) em vez de herdar de "agora":

Carbon::createFromFormat('!m/Y', $dt_inicial)

O problema aparece no BalanceUtils.php, linhas 24-25, 115-116, 160-161, 164, 236-237, 240. Não verifiquei se existem outros lugares, mas qq lugar que tiver faixa de data por mm/yyyy vai acontecer o problema

O caso da linha 164 é pior ainda. Afeta o saldo anterior no mês de março, pq ele tenta pegar o saldo final de fevereiro, e é jogado de volta para o valor de março na tabela
