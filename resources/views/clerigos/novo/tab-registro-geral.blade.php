<div class="tab-pane fade" id="border-top-registro-geral" role="tabpanel" aria-labelledby="border-top-familiar">
    <blockquote class="blockquote">
        <div class="row">
            <div class="col-12 mt-3 col-md-4">
                <label for="identidade">{{ __('Identidade*') }}</label>
                <input type="text" class="form-control @error('identidade') is-invalid @enderror" id="identidade"
                    name="identidade" value="{{ old('identidade') }}" maxlength="13" placeholder="***.***.***-*">
                @error('identidade')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="data_emissao">{{ __('Data de Emissão*') }}</label>
                <input type="date" class="form-control @error('data_emissao') is-invalid @enderror" id="data_emissao"
                    name="data_emissao" value="{{ old('data_emissao') }}">
                @error('data_emissao')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="orgao_emissor">{{ __('Órgão Emissor*') }}</label>
                <input type="text" class="form-control @error('orgao_emissor') is-invalid @enderror"
                    id="orgao_emissor" name="orgao_emissor" value="{{ old('orgao_emissor') }}" maxlength="50">
                @error('orgao_emissor')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="identidade_uf">{{ __('Estado*') }}</label>
                <select class="form-control @error('identidade_uf') is-invalid @enderror" id="identidade_uf"
                    name="identidade_uf">
                    <option value="">{{ __('Selecione') }}</option>
                    @foreach ($ufs as $key => $uf)
                        <option value="{{ $key }}"
                            {{ old('identidade_uf') == $key ? 'selected' : '' }}>
                            {{ $uf }}
                        </option>
                    @endforeach
                </select>
                @error('identidade_uf')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
            <input type="hidden" name="regiao_id" value="23">
        </div>
    </blockquote>
</div>
