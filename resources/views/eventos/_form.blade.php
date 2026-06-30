@php
    $statusAtual = old('status', $evento->status ?: 'planejado');
    $dataInicio = old('data_inicio', optional($evento->data_inicio)->format('d/m/Y') ?: now()->format('d/m/Y'));
    $dataFim = old('data_fim', optional($evento->data_fim)->format('d/m/Y'));
    $horaInicio = old('hora_inicio', $evento->hora_inicio ? substr((string) $evento->hora_inicio, 0, 5) : '');
    $horaFim = old('hora_fim', $evento->hora_fim ? substr((string) $evento->hora_fim, 0, 5) : '');
    $equipeOld = old('equipe');

    if (is_array($equipeOld)) {
        $equipeRows = collect($equipeOld);
    } elseif ($evento->exists) {
        $equipeRows = $evento->equipe->map(fn ($membro) => [
            'nome' => $membro->nome,
            'evento_funcao_id' => $membro->evento_funcao_id,
            'funcao' => $membro->funcao,
            'contato' => $membro->contato,
            'lider' => $membro->lider ? 1 : 0,
        ]);
    } else {
        $equipeRows = collect([['nome' => '', 'evento_funcao_id' => '', 'contato' => '', 'lider' => 1]]);
    }

    if ($equipeRows->isEmpty()) {
        $equipeRows = collect([['nome' => '', 'evento_funcao_id' => '', 'contato' => '', 'lider' => 1]]);
    }
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="instituicao_id">* Igreja / Congregação</label>
            <select name="instituicao_id" id="instituicao_id" class="form-control @error('instituicao_id') is-invalid @enderror" required>
                <option value="">Selecione</option>
                @foreach ($instituicoesEvento->groupBy('grupo') as $grupo => $instituicoesGrupo)
                    <optgroup label="{{ $grupo }}">
                        @foreach ($instituicoesGrupo as $instituicaoEvento)
                            <option value="{{ $instituicaoEvento->id }}" {{ (string) old('instituicao_id', $evento->instituicao_id) === (string) $instituicaoEvento->id ? 'selected' : '' }}>
                                {{ $instituicaoEvento->label }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="evento_proposito_id">* Propósito</label>
            <select name="evento_proposito_id" id="evento_proposito_id" class="form-control @error('evento_proposito_id') is-invalid @enderror" required>
                <option value="">Selecione</option>
                @foreach ($propositos as $proposito)
                    <option value="{{ $proposito->id }}" {{ (string) old('evento_proposito_id', $evento->evento_proposito_id) === (string) $proposito->id ? 'selected' : '' }}>
                        {{ $proposito->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="titulo">* Nome do Evento</label>
            <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $evento->titulo) }}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="status">* Status</label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ $statusAtual === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="data_inicio">* Data Inicial</label>
            <input type="text" name="data_inicio" id="data_inicio" class="form-control data-ptbr @error('data_inicio') is-invalid @enderror" value="{{ $dataInicio }}" placeholder="dd/mm/aaaa" autocomplete="off" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="hora_inicio">Hora Inicial</label>
            <input type="text" name="hora_inicio" id="hora_inicio" class="form-control hora-ptbr @error('hora_inicio') is-invalid @enderror" value="{{ $horaInicio }}" placeholder="HH:mm" autocomplete="off">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="data_fim">Data Final</label>
            <input type="text" name="data_fim" id="data_fim" class="form-control data-ptbr @error('data_fim') is-invalid @enderror" value="{{ $dataFim }}" placeholder="dd/mm/aaaa" autocomplete="off">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="hora_fim">Hora Final</label>
            <input type="text" name="hora_fim" id="hora_fim" class="form-control hora-ptbr @error('hora_fim') is-invalid @enderror" value="{{ $horaFim }}" placeholder="HH:mm" autocomplete="off">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="local">Local</label>
    <input type="text" name="local" id="local" class="form-control @error('local') is-invalid @enderror" value="{{ old('local', $evento->local) }}">
</div>

<div class="form-group">
    <label for="descricao">Descrição / Agenda</label>
    <textarea name="descricao" id="descricao" rows="4" class="form-control @error('descricao') is-invalid @enderror" placeholder="Descreva a agenda, programação ou roteiro do evento">{{ old('descricao', $evento->descricao) }}</textarea>
</div>

<div class="form-group">
    <label for="observacoes">Observações</label>
    <textarea name="observacoes" id="observacoes" rows="3" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $evento->observacoes) }}</textarea>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Equipe de Coordenação</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-equipe-row">Adicionar Pessoa</button>
</div>
<small class="text-muted d-block mb-3">Marque apenas uma pessoa como líder. Se mais de uma for marcada, o sistema manterá a primeira.</small>

<div id="equipe-container">
    @foreach ($equipeRows as $index => $membro)
        <div class="row equipe-row align-items-end mb-2">
            <div class="col-md-4">
                <label>Nome</label>
                <input type="text" name="equipe[{{ $index }}][nome]" class="form-control" value="{{ data_get($membro, 'nome') }}">
            </div>
            <div class="col-md-3">
                <label>Função</label>
                <select name="equipe[{{ $index }}][evento_funcao_id]" class="form-control">
                    <option value="">Selecione</option>
                    @foreach ($funcoesEventos as $funcaoEvento)
                        <option value="{{ $funcaoEvento->id }}" {{ (string) data_get($membro, 'evento_funcao_id') === (string) $funcaoEvento->id ? 'selected' : '' }}>
                            {{ $funcaoEvento->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Contato</label>
                <input type="text" name="equipe[{{ $index }}][contato]" class="form-control contato-celular" value="{{ data_get($membro, 'contato') }}" placeholder="(00) 00000-0000">
            </div>
            <div class="col-md-1">
                <label>Líder</label>
                <input type="hidden" name="equipe[{{ $index }}][lider]" value="0">
                <div class="form-check">
                    <input type="checkbox" name="equipe[{{ $index }}][lider]" value="1" class="form-check-input equipe-lider" {{ data_get($membro, 'lider') ? 'checked' : '' }}>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-equipe-row">Remover</button>
            </div>
        </div>
    @endforeach
</div>

<template id="equipe-row-template">
    <div class="row equipe-row align-items-end mb-2">
        <div class="col-md-4">
            <label>Nome</label>
            <input type="text" name="__name__[nome]" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Função</label>
            <select name="__name__[evento_funcao_id]" class="form-control">
                <option value="">Selecione</option>
                @foreach ($funcoesEventos as $funcaoEvento)
                    <option value="{{ $funcaoEvento->id }}">{{ $funcaoEvento->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Contato</label>
            <input type="text" name="__name__[contato]" class="form-control contato-celular" placeholder="(00) 00000-0000">
        </div>
        <div class="col-md-1">
            <label>Líder</label>
            <input type="hidden" name="__name__[lider]" value="0">
            <div class="form-check">
                <input type="checkbox" name="__name__[lider]" value="1" class="form-check-input equipe-lider">
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-equipe-row">Remover</button>
        </div>
    </div>
</template>
