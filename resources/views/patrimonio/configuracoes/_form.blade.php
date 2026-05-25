@php
    $item = $configuracao ?? null;
@endphp

<div class="alert alert-info mb-3" role="alert">
    Esta configuração é global e ficará disponível para todas as igrejas/unidades.
</div>

<div class="mb-3">
    <label>Nome *</label>
    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $item->nome ?? '') }}" maxlength="180" required>
    @error('nome')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="mb-3">
    <label>Descrição</label>
    <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="4">{{ old('descricao', $item->descricao ?? '') }}</textarea>
    @error('descricao')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Ativo</label>
        @php $ativo = old('ativo', isset($item) ? (int) $item->ativo : 1); @endphp
        <select name="ativo" class="form-control @error('ativo') is-invalid @enderror">
            <option value="1" {{ (string) $ativo === '1' ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ (string) $ativo === '0' ? 'selected' : '' }}>Não</option>
        </select>
        @error('ativo')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Ordem</label>
        <input type="number" name="ordem" min="0" max="9999" class="form-control @error('ordem') is-invalid @enderror" value="{{ old('ordem', $item->ordem ?? 0) }}">
        @error('ordem')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>
