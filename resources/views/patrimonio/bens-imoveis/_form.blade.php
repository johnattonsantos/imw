@php
    $item = $imovel ?? null;
@endphp

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Código patrimonial</label>
        <input type="text" class="form-control" value="{{ $item->codigo_patrimonial ?? 'Gerado automaticamente' }}" readonly>
    </div>

    <div class="mb-3 col-md-4">
        <label>Natureza do imóvel</label>
        <input type="text" name="natureza_imovel" class="form-control @error('natureza_imovel') is-invalid @enderror" value="{{ old('natureza_imovel', $item->natureza_imovel ?? '') }}" maxlength="120">
        @error('natureza_imovel')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Status de titularidade</label>
        <input type="text" name="status_titularidade" class="form-control @error('status_titularidade') is-invalid @enderror" value="{{ old('status_titularidade', $item->status_titularidade ?? '') }}" maxlength="80" placeholder="Ex.: proprietário, posse contratual">
        @error('status_titularidade')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label>Nome *</label>
        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $item->nome ?? '') }}" maxlength="180" required>
        @error('nome')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-6">
        <label>Endereço</label>
        <input type="text" name="endereco" class="form-control @error('endereco') is-invalid @enderror" value="{{ old('endereco', $item->endereco ?? '') }}" maxlength="255">
        @error('endereco')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Cidade</label>
        <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" value="{{ old('cidade', $item->cidade ?? '') }}" maxlength="120">
        @error('cidade')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-2">
        <label>Estado (UF)</label>
        <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $item->estado ?? '') }}" maxlength="2">
        @error('estado')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-2">
        <label>CEP</label>
        <input type="text" name="cep" class="form-control @error('cep') is-invalid @enderror" value="{{ old('cep', $item->cep ?? '') }}" maxlength="9">
        @error('cep')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-2">
        <label>Latitude</label>
        <input type="number" step="0.0000001" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $item->latitude ?? '') }}">
        @error('latitude')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-2">
        <label>Longitude</label>
        <input type="number" step="0.0000001" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $item->longitude ?? '') }}">
        @error('longitude')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Área total (m²)</label>
        <input type="number" step="0.01" min="0" name="area_total" class="form-control @error('area_total') is-invalid @enderror" value="{{ old('area_total', $item->area_total ?? '') }}">
        @error('area_total')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Área construída (m²)</label>
        <input type="number" step="0.01" min="0" name="area_construida" class="form-control @error('area_construida') is-invalid @enderror" value="{{ old('area_construida', $item->area_construida ?? '') }}">
        @error('area_construida')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>IPTU/ITR</label>
        <input type="text" name="iptu_itr" class="form-control @error('iptu_itr') is-invalid @enderror" value="{{ old('iptu_itr', $item->iptu_itr ?? '') }}" maxlength="120">
        @error('iptu_itr')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Inscrição municipal/rural</label>
        <input type="text" name="inscricao_municipal_rural" class="form-control @error('inscricao_municipal_rural') is-invalid @enderror" value="{{ old('inscricao_municipal_rural', $item->inscricao_municipal_rural ?? '') }}" maxlength="180">
        @error('inscricao_municipal_rural')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Valor histórico</label>
        <input type="number" step="0.01" min="0" name="valor_historico" class="form-control @error('valor_historico') is-invalid @enderror" value="{{ old('valor_historico', $item->valor_historico ?? '') }}">
        @error('valor_historico')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Valor venal</label>
        <input type="number" step="0.01" min="0" name="valor_venal" class="form-control @error('valor_venal') is-invalid @enderror" value="{{ old('valor_venal', $item->valor_venal ?? '') }}">
        @error('valor_venal')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Valor de mercado</label>
        <input type="number" step="0.01" min="0" name="valor_mercado" class="form-control @error('valor_mercado') is-invalid @enderror" value="{{ old('valor_mercado', $item->valor_mercado ?? '') }}">
        @error('valor_mercado')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Situação tributária</label>
        <input type="text" name="situacao_tributaria" class="form-control @error('situacao_tributaria') is-invalid @enderror" value="{{ old('situacao_tributaria', $item->situacao_tributaria ?? '') }}" maxlength="120">
        @error('situacao_tributaria')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Número da matrícula</label>
        <input type="text" name="numero_matricula" class="form-control @error('numero_matricula') is-invalid @enderror" value="{{ old('numero_matricula', $item->numero_matricula ?? '') }}" maxlength="120">
        @error('numero_matricula')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Cartório</label>
        <input type="text" name="cartorio" class="form-control @error('cartorio') is-invalid @enderror" value="{{ old('cartorio', $item->cartorio ?? '') }}" maxlength="180">
        @error('cartorio')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Tipo de título</label>
        <input type="text" name="tipo_titulo" class="form-control @error('tipo_titulo') is-invalid @enderror" value="{{ old('tipo_titulo', $item->tipo_titulo ?? '') }}" maxlength="120">
        @error('tipo_titulo')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Data de aquisição ou posse</label>
        <input type="date" name="data_aquisicao_posse" class="form-control @error('data_aquisicao_posse') is-invalid @enderror" value="{{ old('data_aquisicao_posse', optional($item->data_aquisicao_posse ?? null)->format('Y-m-d')) }}">
        @error('data_aquisicao_posse')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label>Possui escritura registrada?</label>
        @php $escritura = old('possui_escritura_registrada', isset($item) ? (int) $item->possui_escritura_registrada : 0); @endphp
        <select name="possui_escritura_registrada" class="form-control @error('possui_escritura_registrada') is-invalid @enderror">
            <option value="1" {{ (string) $escritura === '1' ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ (string) $escritura === '0' ? 'selected' : '' }}>Não</option>
        </select>
        @error('possui_escritura_registrada')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Validade AVCB</label>
        <input type="date" name="avcb_validade" class="form-control @error('avcb_validade') is-invalid @enderror" value="{{ old('avcb_validade', optional($item->avcb_validade ?? null)->format('Y-m-d')) }}">
        @error('avcb_validade')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-4">
        <label>Regularização pendente</label>
        <input type="text" class="form-control" value="{{ isset($item) ? ($item->regularizacao_pendente ? 'Sim' : 'Não') : 'Será calculada automaticamente' }}" readonly>
    </div>
</div>

<div class="mb-3">
    <label>Observações jurídicas</label>
    <textarea name="observacoes_juridicas" class="form-control @error('observacoes_juridicas') is-invalid @enderror" rows="4">{{ old('observacoes_juridicas', $item->observacoes_juridicas ?? '') }}</textarea>
    @error('observacoes_juridicas')<small class="text-danger">{{ $message }}</small>@enderror
</div>
