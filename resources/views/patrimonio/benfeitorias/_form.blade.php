@php $item = $benfeitoria ?? null; @endphp

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Imóvel *</label>
        <select name="imovel_id" class="form-control @error('imovel_id') is-invalid @enderror" required>
            <option value="">Selecione</option>
            @foreach ($imoveis as $imovel)
                <option value="{{ $imovel->id }}" {{ (string) old('imovel_id', $item->imovel_id ?? '') === (string) $imovel->id ? 'selected' : '' }}>
                    {{ $imovel->nome ?: ('Imóvel #' . $imovel->id) }}
                </option>
            @endforeach
        </select>
        @error('imovel_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Data</label>
        <input type="date" name="data" class="form-control @error('data') is-invalid @enderror"
            value="{{ old('data', optional($item->data ?? null)->format('Y-m-d')) }}">
        @error('data')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Valor investido *</label>
        <input type="number" name="valor_investido" class="form-control @error('valor_investido') is-invalid @enderror"
            value="{{ old('valor_investido', $item->valor_investido ?? '') }}" min="0" step="0.01" required>
        @error('valor_investido')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mb-3">
    <label>Descrição *</label>
    <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="4" required>{{ old('descricao', $item->descricao ?? '') }}</textarea>
    @error('descricao')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Responsável</label>
        <input type="text" name="responsavel" class="form-control @error('responsavel') is-invalid @enderror"
            value="{{ old('responsavel', $item->responsavel ?? '') }}" maxlength="180">
        @error('responsavel')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-6">
        <label>Documento/Anexo</label>
        <input type="file" name="documento_anexo" class="form-control @error('documento_anexo') is-invalid @enderror">
        <small class="text-muted">Tipos permitidos: PDF, JPG, PNG, DOC e DOCX.</small>
        @error('documento_anexo')<small class="text-danger d-block">{{ $message }}</small>@enderror

        @if (isset($benfeitoria) && $benfeitoria->documento_anexo && auth()->check() && auth()->user()->hasPerfilRegra('patrimonio.visualizar'))
            <div class="mt-2">
                <a href="{{ route('patrimonio.benfeitorias.download', $benfeitoria->id) }}" class="btn btn-sm btn-outline-primary">Baixar anexo atual</a>
            </div>
        @endif
    </div>
</div>

<div class="mb-3">
    <label>Observações</label>
    <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes', $item->observacoes ?? '') }}</textarea>
    @error('observacoes')<small class="text-danger">{{ $message }}</small>@enderror
</div>
