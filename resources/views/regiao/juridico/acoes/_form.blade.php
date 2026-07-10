<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="instituicao_id">* Instituição</label>
            <select name="instituicao_id" id="instituicao_id" class="form-control @error('instituicao_id') is-invalid @enderror" required>
                <option value="">Selecione</option>
                @foreach ($instituicoes as $instituicao)
                    @php
                        $tipoLabel = match ((int) $instituicao->tipo_instituicao_id) {
                            \App\Models\InstituicoesTipoInstituicao::REGIAO => 'Região',
                            \App\Models\InstituicoesTipoInstituicao::DISTRITO => 'Distrito',
                            \App\Models\InstituicoesTipoInstituicao::IGREJA_LOCAL => 'Igreja',
                            \App\Models\InstituicoesTipoInstituicao::CONGREGACAO => 'Congregação',
                            default => 'Instituição',
                        };
                    @endphp
                    <option value="{{ $instituicao->id }}" {{ (string) old('instituicao_id', $acao->instituicao_id) === (string) $instituicao->id ? 'selected' : '' }}>
                        {{ $tipoLabel }} - {{ $instituicao->nome }}
                    </option>
                @endforeach
            </select>
            @error('instituicao_id')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="numero_processo">Número do Processo</label>
            <input type="text" name="numero_processo" id="numero_processo" class="form-control" value="{{ old('numero_processo', $acao->numero_processo) }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="vara_tribunal">Vara ou Tribunal</label>
            <input type="text" name="vara_tribunal" id="vara_tribunal" class="form-control" value="{{ old('vara_tribunal', $acao->vara_tribunal) }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="autor">* Autor</label>
            <input type="text" name="autor" id="autor" class="form-control @error('autor') is-invalid @enderror" value="{{ old('autor', $acao->autor) }}" required>
            @error('autor')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="reu">* Ré</label>
            <input type="text" name="reu" id="reu" class="form-control @error('reu') is-invalid @enderror" value="{{ old('reu', $acao->reu) }}" required>
            @error('reu')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="advogado_causa_id">Advogado da Causa</label>
            <select name="advogado_causa_id" id="advogado_causa_id" class="form-control">
                <option value="">Selecione</option>
                @foreach ($advogados as $advogado)
                    <option value="{{ $advogado->id }}" {{ (string) old('advogado_causa_id', $acao->advogado_causa_id) === (string) $advogado->id ? 'selected' : '' }}>{{ $advogado->nome }}{{ $advogado->registro_oab ? ' - OAB ' . $advogado->registro_oab : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="advogado_oposicao_id">Advogado da Oposição</label>
            <select name="advogado_oposicao_id" id="advogado_oposicao_id" class="form-control">
                <option value="">Selecione</option>
                @foreach ($advogados as $advogado)
                    <option value="{{ $advogado->id }}" {{ (string) old('advogado_oposicao_id', $acao->advogado_oposicao_id) === (string) $advogado->id ? 'selected' : '' }}>{{ $advogado->nome }}{{ $advogado->registro_oab ? ' - OAB ' . $advogado->registro_oab : '' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="advogado_oposicao_nome">Nome do Advogado da Oposição</label>
            <input type="text" name="advogado_oposicao_nome" id="advogado_oposicao_nome" class="form-control" value="{{ old('advogado_oposicao_nome', $acao->advogado_oposicao_nome) }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="status">* Status</label>
            <select name="status" id="status" class="form-control" required>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $acao->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="resultado">* Resultado</label>
            <select name="resultado" id="resultado" class="form-control" required>
                @foreach ($resultadoOptions as $value => $label)
                    <option value="{{ $value }}" {{ old('resultado', $acao->resultado) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="data_distribuicao">Data Inicial</label>
            <input type="date" name="data_distribuicao" id="data_distribuicao" class="form-control" value="{{ old('data_distribuicao', optional($acao->data_distribuicao)->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="data_sentenca">Data da Sentença</label>
            <input type="date" name="data_sentenca" id="data_sentenca" class="form-control" value="{{ old('data_sentenca', optional($acao->data_sentenca)->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="custo_demanda">Custo da Demanda</label>
            <input type="text" name="custo_demanda" id="custo_demanda" class="form-control" value="{{ old('custo_demanda', $acao->custo_demanda !== null ? number_format((float) $acao->custo_demanda, 2, ',', '.') : '') }}" placeholder="0,00">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="objeto">Objeto da Ação</label>
    <textarea name="objeto" id="objeto" rows="3" class="form-control">{{ old('objeto', $acao->objeto) }}</textarea>
</div>

<div class="form-group">
    <label for="teor_decisao">Teor da Decisão</label>
    <textarea name="teor_decisao" id="teor_decisao" rows="4" class="form-control">{{ old('teor_decisao', $acao->teor_decisao) }}</textarea>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="outros">Outros</label>
            <textarea name="outros" id="outros" rows="3" class="form-control">{{ old('outros', $acao->outros) }}</textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="observacoes">Observações</label>
            <textarea name="observacoes" id="observacoes" rows="3" class="form-control">{{ old('observacoes', $acao->observacoes) }}</textarea>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('regiao.juridico.acoes.index') }}" class="btn btn-light">Voltar</a>
    <button type="submit" class="btn btn-primary">Salvar</button>
</div>
