@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[['text' => 'Região', 'url' => '#', 'active' => false], ['text' => 'Gestão de Acessos', 'url' => '#', 'active' => true]]" />
@endsection
@section('content')
@include('extras.alerts')
<div class="col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>Gestão de Acessos - {{ $regiao->nome }}</h4></div>
        <div class="widget-content widget-content-area">
            <form method="GET" action="{{ route('regiao.relatorio.gestao-acessos') }}" class="row align-items-end">
                <div class="col-md-2 form-group"><label for="ano">Ano</label><input type="number" class="form-control" name="ano" id="ano" value="{{ $ano }}" min="2000" max="{{ now()->year + 1 }}" required></div>
                <div class="col-md-2 form-group"><label for="mes">Mês</label><select class="form-control" name="mes" id="mes">
                    <option value="0" {{ !$mes ? 'selected' : '' }}>Ano inteiro</option>
                    @foreach(range(1, 12) as $m)<option value="{{ $m }}" {{ $mes === $m ? 'selected' : '' }}>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>@endforeach
                </select></div>
                <div class="col-md-2 form-group"><label for="dia">Dia (opcional)</label><select class="form-control" name="dia" id="dia">
                    <option value="0">Todos os dias</option>
                    @foreach(range(1, 31) as $d)<option value="{{ $d }}" {{ $dia === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach
                </select></div>
                <div class="col-md-4 form-group">
                    <label for="usuario">Usuário (nome ou parte do nome)</label>
                    <input class="form-control" type="search" name="usuario" id="usuario" value="{{ $usuario }}" maxlength="150" placeholder="Digite o nome do usuário">
                    @error('usuario')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-2 form-group"><button class="btn btn-primary">Buscar</button></div>
            </form>
            <p><strong>Início do monitoramento:</strong> {{ $monitoramento ? \Carbon\Carbon::parse($monitoramento)->format('d/m/Y H:i') : 'Ainda não iniciado' }} (horário de Brasília).</p>
            <p class="text-muted">Uma linha por sessão, perfil e instituição efetivamente utilizados. Primeiro acesso é o horário de login; último acesso é o logout da mesma sessão. Trocar de perfil não representa novo login: os perfis usados compartilham os horários da sessão. Fechar o navegador ou expirar a sessão não comprova um logout.</p>
            <p class="text-muted">Os filtros consideram o intervalo de utilização do perfil, até o último uso observado ou o logout, não apenas o dia do login. O uso observado é atualizado aproximadamente a cada cinco minutos. Por isso uma sessão iniciada em outro dia pode aparecer. Os nomes são preservados como registrados na sessão.</p>
            <p class="text-muted">Logins antigos da auditoria também aparecem nesta tabela, com o horário comprovado e “Perfil não registrado”. Não é deduzida uma saída correspondente. Se a instituição não foi registrada, o login só aparece para usuários atualmente vinculados exclusivamente à região; isso não comprova qual instituição foi acessada naquele momento. Logins sem instituição de usuários vinculados a outras regiões ficam de fora.</p>
            @if(!$monitoramento || $inicio->lt(\Carbon\Carbon::parse($monitoramento)))
                <div class="alert alert-warning">O período selecionado não tem cobertura completa por perfil. Ausência de registro anterior ao início do monitoramento não significa que a pessoa não acessou.</div>
            @endif

            <h5>Acessos por perfil: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</h5>
            <div class="table-responsive mb-4"><table class="table table-bordered table-striped">
                <thead><tr><th>Instituição</th><th>Usuário</th><th>Perfil</th><th>Sessão</th><th>Primeiro acesso (login)</th><th>Último acesso (logout)</th><th>Situação</th></tr></thead>
                <tbody>@forelse($quadros['perfis'] as $linha)
                    <tr><td>{{ $linha->instituicao }}</td><td>{{ $linha->usuario }}</td><td>{{ $linha->perfil }}</td><td>{{ $linha->sessao_id ? substr($linha->sessao_id, 0, 8) : 'Não identificada' }}</td>
                        <td>{{ $linha->primeiro ? \Carbon\Carbon::parse($linha->primeiro)->format('d/m/Y H:i:s') : 'Login não registrado (sessão anterior)' }}</td>
                        <td>{{ $linha->ultimo ? \Carbon\Carbon::parse($linha->ultimo)->format('d/m/Y H:i:s') : 'Saída não registrada' }}</td>
                        <td>{{ $linha->origem === 'login_antigo' ? 'Login de auditoria (sem perfil/sessão identificados)' : ($linha->ultimo ? 'Encerrada por logout' : 'Sem logout registrado') }}</td>
                    </tr>
                @empty<tr><td colspan="7">Nenhum acesso identificável para a região no período.</td></tr>@endforelse</tbody>
            </table></div>
            {{ $quadros['perfis']->links() }}

        </div>
    </div>
</div>
@endsection
