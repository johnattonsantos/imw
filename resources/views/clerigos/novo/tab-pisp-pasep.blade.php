<div class="tab-pane fade" id="border-top-pisp-pasep" role="tabpanel" aria-labelledby="border-top-pisp-pasep">
    <blockquote class="blockquote">
        <div class="row">
            <div class="col-12 mt-3 col-md-4">
                <label for="pispasep">{{ __('Número do PIS/PASEP') }}</label>
                <input type="text" class="form-control @error('pispasep') is-invalid @enderror" id="pispasep"
                    name="pispasep" value="{{ old('pispasep') }}" maxlength="11" >
                @error('pispasep')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="pispasep_emissao">{{ __('Data de Emissão') }}</label>
                <input type="date" class="form-control @error('pispasep_emissao') is-invalid @enderror"
                    id="pispasep_emissao" name="pispasep_emissao" value="{{ old('pispasep_emissao') }}" >
                @error('pispasep_emissao')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </blockquote>
</div>
