<div class="tab-pane fade" id="border-top-endereco" role="tabpanel" aria-labelledby="border-top-endereco">
    <blockquote class="blockquote">
        <div class="row mb-4">
            <div class="col-xl-3">
                <label for="telefone_preferencial">{{ __('Celular*') }}</label>
                <input type="text" class="form-control @error('telefone_preferencial') is-invalid @enderror"
                    id="telefone_preferencial" name="telefone_preferencial" value="{{ old('telefone_preferencial') }}">
                @error('telefone_preferencial')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-3">
                <label for="telefone_alternativo">{{ __('Telefone') }}</label>
                <input type="text" class="form-control @error('telefone_alternativo') is-invalid @enderror" id="telefone_alternativo"
                    name="telefone_alternativo" value="{{ old('telefone_alternativo') }}" >
                @error('telefone_alternativo')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-3">
                <label for="pais">{{ __('País') }}</label>
                <select class="form-control @error('pais') is-invalid @enderror" id="pais" name="pais">
                    <option value="BR" {{ old('pais') == 'BR' ? 'selected' : '' }}>{{ __('Brasil') }}</option>
                </select>
                @error('pais')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-2">
                <label for="cep">{{ __('* CEP') }}</label>
                <input type="text" id="cep" class="form-control @error('cep') is-invalid @enderror"
                    name="cep" value="{{ old('cep') }}" >
                @error('cep')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-4">
                <label for="endereco">{{ __('* Logradouro (Rua/Av/Beco)') }}</label>
                <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco"
                    name="endereco" value="{{ old('endereco') }}" maxlength="100">
                @error('endereco')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-2">
                <label for="numero">{{ __('Número') }}</label>
                <input type="number" class="form-control @error('numero') is-invalid @enderror" id="numero"
                    name="numero" value="{{ old('numero') }}" maxlength="20">
                @error('numero')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-4">
                <label for="complemento">{{ __('Complemento') }}</label>
                <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento"
                    name="complemento" value="{{ old('complemento') }}" maxlength="30">
                @error('complemento')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-4">
                <label for="bairro">{{ __('* Bairro') }}</label>
                <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro"
                    name="bairro" value="{{ old('bairro') }}" maxlength="100">
                @error('bairro')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-4">
                <label for="cidade">{{ __('* Cidade') }}</label>
                <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade"
                    name="cidade" value="{{ old('cidade') }}" maxlength="100">
                @error('cidade')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-xl-4">
                <label for="uf">{{ __('UF') }}</label>
                <select class="form-control @error('uf') is-invalid @enderror" id="uf" name="uf">
                    <option value="">{{ __('Selecione') }}</option>
                    @foreach ($ufs as $key => $value)
                        <option value="{{ $key }}" {{ old('uf') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
                @error('uf')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 mt-3 col-md-4">
                <label for="residencia_propria">{{ __('Residência Própria') }}</label>
                <select class="form-control @error('residencia_propria') is-invalid @enderror" id="residencia_propria"
                    name="residencia_propria">
                    <option value="0" {{ old('residencia_propria') == '0' ? 'selected' : '' }}>{{ __('Não') }}</option>
                    <option value="1" {{ old('residencia_propria') == '1' ? 'selected' : '' }}>{{ __('Sim') }}</option>
                </select>
                @error('residencia_propria')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="residencia_propria_fgts">{{ __('Utilizou FGTS?') }}</label>
                <select class="form-control @error('residencia_propria_fgts') is-invalid @enderror"
                    id="residencia_propria_fgts" name="residencia_propria_fgts">
                    <option value="0" {{ old('residencia_propria_fgts') == '0' ? 'selected' : '' }}>{{ __('Não') }}</option>
                    <option value="1" {{ old('residencia_propria_fgts') == '1' ? 'selected' : '' }}>{{ __('Sim') }}</option>
                </select>
                @error('residencia_propria_fgts')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </blockquote>
</div>
