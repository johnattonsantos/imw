<div class="tab-pane fade" id="border-top-carteira-trabalho" role="tabpanel" aria-labelledby="border-top-carteira-trabalho">
    <blockquote class="blockquote">
        <div class="row">
            <div class="col-12 mt-3 col-md-4">
                <label for="ctps">{{ __('Número da CTPS') }}</label>
                <input type="text" class="form-control @error('ctps') is-invalid @enderror" id="ctps" name="ctps" value="{{ old('ctps') }}" maxlength="10" >
                @error('ctps')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 mt-3 col-md-4">
                <label for="ctps_emissao">{{ __('Data de Emissão') }}</label>
                <input type="date" class="form-control @error('ctps_emissao') is-invalid @enderror" id="ctps_emissao" name="ctps_emissao" value="{{ old('ctps_emissao') }}" >
                @error('ctps_emissao')
                    <span class="help-block text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </blockquote>
</div>
