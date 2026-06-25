@php $item = $riscoJuridico ?? null; @endphp

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
        <label>Possui ônus *</label>
        @php $possuiOnus = (string) old('possui_onus', (int) ($item->possui_onus ?? 0)); @endphp
        <select name="possui_onus" class="form-control @error('possui_onus') is-invalid @enderror" required>
            <option value="1" {{ $possuiOnus === '1' ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ $possuiOnus === '0' ? 'selected' : '' }}>Não</option>
        </select>
        @error('possui_onus')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Tipo de ônus</label>
        <input type="text" name="tipo_onus" class="form-control @error('tipo_onus') is-invalid @enderror"
            value="{{ old('tipo_onus', $item->tipo_onus ?? '') }}" maxlength="120">
        @error('tipo_onus')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-3">
        <label>Nível de risco *</label>
        @php $nivel = old('nivel_risco', $item->nivel_risco ?? 'baixo'); @endphp
        <select name="nivel_risco" class="form-control @error('nivel_risco') is-invalid @enderror" required>
            <option value="baixo" {{ $nivel === 'baixo' ? 'selected' : '' }}>Baixo</option>
            <option value="medio" {{ $nivel === 'medio' ? 'selected' : '' }}>Médio</option>
            <option value="alto" {{ $nivel === 'alto' ? 'selected' : '' }}>Alto</option>
            <option value="critico" {{ $nivel === 'critico' ? 'selected' : '' }}>Crítico</option>
        </select>
        @error('nivel_risco')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Data de identificação *</label>
        <input type="date" name="data_identificacao" class="form-control @error('data_identificacao') is-invalid @enderror"
            value="{{ old('data_identificacao', optional($item->data_identificacao ?? null)->format('Y-m-d')) }}" required>
        @error('data_identificacao')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3 col-md-3">
        <label>Status *</label>
        @php $status = old('status', $item->status ?? 'aberto'); @endphp
        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="aberto" {{ $status === 'aberto' ? 'selected' : '' }}>Aberto</option>
            <option value="em_andamento" {{ $status === 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
            <option value="mitigado" {{ $status === 'mitigado' ? 'selected' : '' }}>Mitigado</option>
            <option value="encerrado" {{ $status === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
        </select>
        @error('status')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="mb-3">
    <label>Descrição *</label>
    <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="4" required>{{ old('descricao', $item->descricao ?? '') }}</textarea>
    @error('descricao')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="mb-3">
    <label>Providência recomendada</label>
    <textarea name="providencia_recomendada" class="form-control @error('providencia_recomendada') is-invalid @enderror" rows="3">{{ old('providencia_recomendada', $item->providencia_recomendada ?? '') }}</textarea>
    @error('providencia_recomendada')<small class="text-danger">{{ $message }}</small>@enderror
</div>
