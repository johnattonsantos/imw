@php
    $item = $documento ?? null;
    $tiposDocumento = $tiposDocumento ?? collect();
    $tipoSelecionado = old('tipo', $item->tipo ?? '');
    $documentavelType = old('documentavel_type');
    $documentavelId = old('documentavel_id');

    if ($item && $item->documentavel_type === \App\Models\Patrimonio\Imovel::class) {
        $documentavelType = $documentavelType ?: 'imovel';
        $documentavelId = $documentavelId ?: $item->documentavel_id;
    }

    if ($item && $item->documentavel_type === \App\Models\Patrimonio\BemMovel::class) {
        $documentavelType = $documentavelType ?: 'bem_movel';
        $documentavelId = $documentavelId ?: $item->documentavel_id;
    }
@endphp

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Nome *</label>
        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
            value="{{ old('nome', $item->nome ?? '') }}" maxlength="180" required>
        @error('nome')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Tipo *</label>
        <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
            <option value="">Selecione</option>
            @foreach ($tiposDocumento as $tipoDocumento)
                <option value="{{ $tipoDocumento->nome }}" {{ (string) $tipoSelecionado === (string) $tipoDocumento->nome ? 'selected' : '' }}>
                    {{ $tipoDocumento->nome }}
                </option>
            @endforeach
            @if ($tipoSelecionado !== '' && ! $tiposDocumento->contains(fn ($tipoDocumento) => (string) $tipoDocumento->nome === (string) $tipoSelecionado))
                <option value="{{ $tipoSelecionado }}" selected>{{ $tipoSelecionado }} (não encontrada nas configurações)</option>
            @endif
        </select>
        @error('tipo')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Status</label>
        @php $status = old('status', $item->status ?? 'vigente'); @endphp
        <select name="status" class="form-control @error('status') is-invalid @enderror">
            <option value="vigente" {{ $status === 'vigente' ? 'selected' : '' }}>Vigente</option>
            <option value="vencido" {{ $status === 'vencido' ? 'selected' : '' }}>Vencido</option>
            <option value="cancelado" {{ $status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>
        @error('status')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Data de emissão</label>
        <input type="date" name="data_emissao" class="form-control @error('data_emissao') is-invalid @enderror"
            value="{{ old('data_emissao', optional($item->data_emissao ?? null)->format('Y-m-d')) }}">
        @error('data_emissao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Data de validade</label>
        <input type="date" name="data_validade" class="form-control @error('data_validade') is-invalid @enderror"
            value="{{ old('data_validade', optional($item->data_validade ?? null)->format('Y-m-d')) }}">
        @error('data_validade')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Pertence a *</label>
        <select name="documentavel_type" class="form-control @error('documentavel_type') is-invalid @enderror" required>
            <option value="">Selecione</option>
            <option value="imovel" {{ $documentavelType === 'imovel' ? 'selected' : '' }}>Imóvel</option>
            <option value="bem_movel" {{ $documentavelType === 'bem_movel' ? 'selected' : '' }}>Bem móvel</option>
        </select>
        @error('documentavel_type')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Registro vinculado *</label>
        <select name="documentavel_id" class="form-control @error('documentavel_id') is-invalid @enderror" required>
            <option value="">Selecione</option>

            @if ($documentavelType === 'bem_movel')
                @foreach ($bensMoveis as $bemMovel)
                    <option value="{{ $bemMovel->id }}" {{ (string) $documentavelId === (string) $bemMovel->id ? 'selected' : '' }}>
                        [Bem] {{ $bemMovel->nome }} ({{ $bemMovel->codigo_patrimonial }})
                    </option>
                @endforeach
            @else
                @foreach ($imoveis as $imovel)
                    <option value="{{ $imovel->id }}" {{ (string) $documentavelId === (string) $imovel->id ? 'selected' : '' }}>
                        [Imóvel] {{ $imovel->nome ?: ('Imóvel #' . $imovel->id) }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('documentavel_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mb-3">
    <label>Arquivo {{ isset($documento) ? '' : '*' }}</label>
    <input type="file" name="arquivo" class="form-control @error('arquivo') is-invalid @enderror" {{ isset($documento) ? '' : 'required' }}>
    <small class="text-muted">Tipos permitidos: PDF, JPG, PNG, DOC, DOCX.</small>
    @error('arquivo')<small class="text-danger d-block">{{ $message }}</small>@enderror

    @if (isset($documento) && $documento->arquivo && auth()->check())
        <div class="mt-2">
            <a href="{{ route('patrimonio.documentos.download', $documento->id) }}" class="btn btn-sm btn-outline-primary">Baixar arquivo atual</a>
        </div>
    @endif
</div>

<div class="mb-3">
    <label>Observações</label>
    <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes', $item->observacoes ?? '') }}</textarea>
    @error('observacoes')<small class="text-danger">{{ $message }}</small>@enderror
</div>
