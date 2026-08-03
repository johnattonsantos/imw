@php
    $selectedMembro = $selectedMembro ?? null;
    $fieldName = $fieldName ?? 'membro_id';
    $showVisitanteButton = $showVisitanteButton ?? true;
    $buscarButtonClass = $buscarButtonClass ?? 'btn btn-outline-secondary';
    $visitanteButtonClass = $visitanteButtonClass ?? 'btn btn-sm btn-outline-primary';
@endphp

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Vínculo com Membresia</span>
        @if ($showVisitanteButton)
            <button type="button" class="{{ $visitanteButtonClass }}" data-toggle="modal" data-target="#visitanteRapidoModal">
                Cadastrar como Visitante
            </button>
        @endif
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Buscar por nome, CPF, telefone ou e-mail</label>
            <div class="input-group">
                <input type="text" id="membroBuscaInput" class="form-control" placeholder="Digite para buscar...">
                <div class="input-group-append">
                    <button class="{{ $buscarButtonClass }}" type="button" id="membroBuscaBtn">Buscar</button>
                </div>
            </div>
        </div>

        <div class="mb-3 border rounded" style="max-height: 280px; overflow-y: auto;">
            <ul class="list-group list-group-flush" id="membroBuscaResultados">
                <li class="list-group-item text-muted">Digite e clique em buscar para ver os resultados.</li>
            </ul>
        </div>

        <input type="hidden" name="{{ $fieldName }}" id="membro_id" value="{{ old($fieldName, $selectedMembro?->id) }}">

        <div id="membroSelecionado" class="alert alert-info mb-0 {{ old($fieldName, $selectedMembro?->id) ? '' : 'd-none' }}">
            <strong>Selecionado:</strong>
            <span id="membroSelecionadoNome">{{ old($fieldName, $selectedMembro?->nome) }}</span>
        </div>
    </div>
</div>
