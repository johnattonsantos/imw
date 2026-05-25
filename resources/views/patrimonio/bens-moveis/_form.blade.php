@php
    $item = $bemMovel ?? null;
    $categorias = $categorias ?? collect();
    $comprobatorios = $comprobatorios ?? collect();
    $categoriaSelecionada = old('categoria', $item->categoria ?? '');
    $comprobatorioSelecionado = old('natureza_comprobatoria', $item->natureza_comprobatoria ?? '');
@endphp

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Código patrimonial</label>
        <input type="text" class="form-control" value="{{ $item->codigo_patrimonial ?? 'Gerado automaticamente' }}" readonly>
    </div>

    <div class="mb-3 col-md-4">
        <label>Placa patrimonial</label>
        <input type="text" class="form-control @error('placa_patrimonial') is-invalid @enderror" name="placa_patrimonial"
            value="{{ old('placa_patrimonial', $item->placa_patrimonial ?? '') }}" maxlength="60">
        @error('placa_patrimonial')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Status *</label>
        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
            @php $status = old('status', $item->status ?? 'ativo'); @endphp
            <option value="ativo" {{ $status === 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ $status === 'inativo' ? 'selected' : '' }}>Inativo</option>
            <option value="baixado" {{ $status === 'baixado' ? 'selected' : '' }}>Baixado</option>
            <option value="em_manutencao" {{ $status === 'em_manutencao' ? 'selected' : '' }}>Em manutenção</option>
            <option value="depreciado" {{ $status === 'depreciado' ? 'selected' : '' }}>Depreciado</option>
        </select>
        @error('status')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Nome *</label>
        <input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome"
            value="{{ old('nome', $item->nome ?? '') }}" maxlength="180" required>
        @error('nome')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Categoria</label>
        <select name="categoria" class="form-control @error('categoria') is-invalid @enderror">
            <option value="">Selecione</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->nome }}" {{ (string) $categoriaSelecionada === (string) $categoria->nome ? 'selected' : '' }}>
                    {{ $categoria->nome }}
                </option>
            @endforeach
            @if ($categoriaSelecionada !== '' && ! $categorias->contains(fn ($categoria) => (string) $categoria->nome === (string) $categoriaSelecionada))
                <option value="{{ $categoriaSelecionada }}" selected>{{ $categoriaSelecionada }} (não encontrada nas configurações)</option>
            @endif
        </select>
        @error('categoria')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Estado de conservação</label>
        <input type="text" class="form-control @error('estado_conservacao') is-invalid @enderror" name="estado_conservacao"
            value="{{ old('estado_conservacao', $item->estado_conservacao ?? '') }}" maxlength="60">
        @error('estado_conservacao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Localização</label>
        <input type="text" class="form-control @error('localizacao') is-invalid @enderror" name="localizacao"
            value="{{ old('localizacao', $item->localizacao ?? '') }}" maxlength="180">
        @error('localizacao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Responsável</label>
        <input type="text" class="form-control @error('responsavel') is-invalid @enderror" name="responsavel"
            value="{{ old('responsavel', $item->responsavel ?? '') }}" maxlength="180">
        @error('responsavel')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Imóvel vinculado</label>
        <select name="imovel_id" class="form-control @error('imovel_id') is-invalid @enderror">
            <option value="">Não vinculado</option>
            @foreach ($imoveis as $imovel)
                <option value="{{ $imovel->id }}" {{ (string) old('imovel_id', $item->imovel_id ?? '') === (string) $imovel->id ? 'selected' : '' }}>
                    {{ $imovel->nome ?: ('Imóvel #' . $imovel->id) }}
                </option>
            @endforeach
        </select>
        @error('imovel_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Data de aquisição</label>
        <input type="date" class="form-control @error('data_aquisicao') is-invalid @enderror" name="data_aquisicao"
            value="{{ old('data_aquisicao', optional($item->data_aquisicao ?? null)->format('Y-m-d')) }}">
        @error('data_aquisicao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Valor de aquisição</label>
        <input type="number" step="0.01" min="0" class="form-control @error('valor_aquisicao') is-invalid @enderror" name="valor_aquisicao"
            value="{{ old('valor_aquisicao', $item->valor_aquisicao ?? '') }}">
        @error('valor_aquisicao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Valor residual</label>
        <input type="number" step="0.01" min="0" class="form-control @error('valor_residual') is-invalid @enderror" name="valor_residual"
            value="{{ old('valor_residual', $item->valor_residual ?? '') }}">
        @error('valor_residual')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Vida útil (anos)</label>
        <input type="number" min="0" class="form-control @error('vida_util') is-invalid @enderror" name="vida_util"
            value="{{ old('vida_util', $item->vida_util ?? '') }}">
        @error('vida_util')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Natureza comprobatória</label>
        <select name="natureza_comprobatoria" class="form-control @error('natureza_comprobatoria') is-invalid @enderror">
            <option value="">Selecione</option>
            @foreach ($comprobatorios as $comprobatorio)
                <option value="{{ $comprobatorio->nome }}" {{ (string) $comprobatorioSelecionado === (string) $comprobatorio->nome ? 'selected' : '' }}>
                    {{ $comprobatorio->nome }}
                </option>
            @endforeach
            @if ($comprobatorioSelecionado !== '' && ! $comprobatorios->contains(fn ($comprobatorio) => (string) $comprobatorio->nome === (string) $comprobatorioSelecionado))
                <option value="{{ $comprobatorioSelecionado }}" selected>{{ $comprobatorioSelecionado }} (não encontrada nas configurações)</option>
            @endif
        </select>
        @error('natureza_comprobatoria')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Número do documento</label>
        <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" name="numero_documento"
            value="{{ old('numero_documento', $item->numero_documento ?? '') }}" maxlength="120">
        @error('numero_documento')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Fornecedor/Doador</label>
        <input type="text" class="form-control @error('fornecedor_doador') is-invalid @enderror" name="fornecedor_doador"
            value="{{ old('fornecedor_doador', $item->fornecedor_doador ?? '') }}" maxlength="180">
        @error('fornecedor_doador')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mb-3">
    <label>Descrição</label>
    <textarea class="form-control @error('descricao') is-invalid @enderror" name="descricao" rows="3">{{ old('descricao', $item->descricao ?? '') }}</textarea>
    @error('descricao')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="mb-3">
    <label>Observações</label>
    <textarea class="form-control @error('observacoes') is-invalid @enderror" name="observacoes" rows="3">{{ old('observacoes', $item->observacoes ?? '') }}</textarea>
    @error('observacoes')<small class="text-danger">{{ $message }}</small>@enderror
</div>
