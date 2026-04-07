<div class="tab-pane fade" id="border-top-registro-geral" role="tabpanel" aria-labelledby="border-top-familiar">
    <blockquote class="blockquote">
        <div class="row">
            <div class="col-12 mt-3 col-md-4">
                <label for="identidade">Identidade*</label>
                <input type="text" class="form-control @error('identidade') is-invalid @enderror" id="identidade"
                    name="identidade" value="{{ old('identidade', $clerigo->identidade) }}">
                @error('identidade')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="data_emissao">Data de Emissão*</label>
                <input type="date" class="form-control @error('data_emissao') is-invalid @enderror" id="data_emissao"
                    name="data_emissao" value="{{ old('data_emissao', $clerigo->data_emissao) }}">
                @error('data_emissao')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="orgao_emissor">Órgão Emissor*</label>
                <input type="text" class="form-control @error('orgao_emissor') is-invalid @enderror"
                    id="orgao_emissor" name="orgao_emissor" value="{{ old('orgao_emissor', $clerigo->orgao_emissor) }}"
                    maxlength="50">
                @error('orgao_emissor')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="identidade_uf">Estado*</label>
                <select class="form-control @error('identidade_uf') is-invalid @enderror" id="identidade_uf"
                    name="identidade_uf">
                    <option value="">Selecione</option>
                    <option value="{{ old('identidade_uf', $clerigo->identidade_uf) }}" selected>
                        {{ old('identidade_uf', $clerigo->identidade_uf) }}</option>
                    @foreach ($ufs as $sigla => $uf)
                        <option value="{{$sigla}}"
                            {{ old('identidade_uf', $clerigo->identidade_uf) == $sigla ? 'selected' : '' }}>
                            {{ $uf }}
                        </option>
                    @endforeach
                </select>
                @error('identidade_uf')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
            @php
                $regiaoSessaoId = optional(optional(optional(session('session_perfil'))->instituicoes)->regiao)->id
                    ?? optional(session('session_perfil'))->instituicao_id;
            @endphp
            <input type="hidden" name="regiao_id" value="{{ old('regiao_id', $regiaoSessaoId) }}">
        </div>

    </blockquote>
</div>
