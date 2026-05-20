@php $item = $baixa ?? null; @endphp

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Bem móvel *</label>
        <select name="bem_movel_id" class="form-control @error('bem_movel_id') is-invalid @enderror" required>
            <option value="">Selecione</option>
            @foreach ($bensMoveis as $bemMovel)
                <option value="{{ $bemMovel->id }}" {{ (string) old('bem_movel_id', $item->bem_movel_id ?? '') === (string) $bemMovel->id ? 'selected' : '' }}>
                    {{ $bemMovel->nome }} ({{ $bemMovel->codigo_patrimonial }})
                </option>
            @endforeach
        </select>
        @error('bem_movel_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Data da baixa *</label>
        <input type="date" name="data_baixa" class="form-control @error('data_baixa') is-invalid @enderror"
            value="{{ old('data_baixa', optional($item->data_baixa ?? null)->format('Y-m-d')) }}" required>
        @error('data_baixa')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Responsável</label>
        <input type="text" name="responsavel" class="form-control @error('responsavel') is-invalid @enderror"
            value="{{ old('responsavel', $item->responsavel ?? '') }}" maxlength="180">
        @error('responsavel')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mb-3">
    <label>Motivo *</label>
    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror"
        value="{{ old('motivo', $item->motivo ?? '') }}" maxlength="180" required>
    @error('motivo')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="mb-3">
    <label>Documento comprobatório</label>
    <input type="file" name="documento_comprobatorio" class="form-control @error('documento_comprobatorio') is-invalid @enderror">
    <small class="text-muted">Tipos permitidos: PDF, JPG, PNG, DOC e DOCX.</small>
    @error('documento_comprobatorio')<small class="text-danger d-block">{{ $message }}</small>@enderror

    @if (isset($baixa) && $baixa->documento_comprobatorio && auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.visualizar') && auth()->user()->hasPerfilRegra('patrimonio.baixa'))
        <div class="mt-2">
            <a href="{{ route('patrimonio.baixas.download', $baixa->id) }}" class="btn btn-sm btn-outline-primary">Baixar documento atual</a>
        </div>
    @endif
</div>

<div class="mb-3">
    <label>Observações</label>
    <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes', $item->observacoes ?? '') }}</textarea>
    @error('observacoes')<small class="text-danger">{{ $message }}</small>@enderror
</div>
