<div class="tab-pane fade" id="border-top-familia" role="tabpanel" aria-labelledby="border-top-familiar">
  <blockquote class="blockquote">
    <div class="row mb-4">
      <div class="col-xl-6">
        <label>{{ __('Nome da Mãe') }}</label>
        <input type="text" class="form-control" id="mae_nome" name="mae_nome" value="{{ old('mae') }}">
        @error('mae')
          <span class="help-block text-danger">{{ $message }}</span>
        @enderror
      </div>
      <div class="col-xl-6">
        <label>{{ __('Nome do Pai') }}</label>
        <input type="text" class="form-control" id="pai_nome" name="pai_nome" value="{{ old('pai_nome') }}">
        @error('pai')
          <span class="help-block text-danger">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-6">
        <label>{{ __('Nome do Cônjuge') }}</label>
        <input type="conjuge" class="form-control" id="conjuge_nome" name="conjuge_nome" value="{{ old('conjuge_nome') }}">
          @error('conjuge')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
      </div>
      <div class="col-xl-3">
        <label>{{ __('Data do Casamento') }}</label>
        <input type="date" class="form-control @error('data_casamento') is-invalid @enderror" id="data_casamento" name="data_casamento" value="{{ old('data_casamento') }}">
          @error('data_casamento')
          <span class="help-block text-danger">{{ $message }}</span>
          @enderror
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-12">

        @include('membros.partials.filhos-inputs', ['filhosValor' => old('filhos')])

      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-12">
        <label>{{ __('Histórico Familiar') }}</label>
        <input type="historico_familiar" class="form-control" id="historico_familiar" name="historico_familiar" value="{{ old('historico_familiar') }}">
          @error('hfamiliar')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
      </div>
    </div>
  </blockquote>

</div>
