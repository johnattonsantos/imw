<div class="tab-pane fade" id="border-top-contato" role="tabpanel" aria-labelledby="border-top-contatos">
    <blockquote class="blockquote">
      <div class="row mb-4">
      <div class="col-xl-3">
          <label for="sexo">{{ __('E-mail') }}</label>
          <input type="email" class="form-control @error('email_preferencial') is-invalid @enderror" id="email_preferencial" name="email_preferencial" value="{{ old('email_preferencial') }}">
          @error('email_preferencial')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-3">
          <label for="sexo">{{ __('Telefone') }}</label>
          <input type="text" class="form-control @error('telefone_preferencial') is-invalid @enderror" id="telefone_preferencial" placeholder="{{ __('ex: +55 (00) 0000-0000') }}" name="telefone_preferencial" value="{{ old('telefone_preferencial') }}">
          @error('telefone_preferencial')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
      </div>



      <div class="row mb-4">
        <div class="col-xl-2">
          <label for="cep">{{ __('CEP') }}</label>
          <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep"  value="{{ old('cep') }}">
          @error('cep')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="endereco">{{ __('Endereço') }}</label>
          <input type="text" class="form-control" id="endereco" name="endereco"  value="{{ old('endereco') }}">
          @error('endereco')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-2">
          <label for="numero">{{ __('Número') }}</label>
          <input type="number" class="form-control" id="numero" name="numero"  value="{{ old('numero') }}">
          @error('numero')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="complemento">{{ __('Complemento') }}</label>
          <input type="text" class="form-control" id="complemento" name="complemento"  value="{{ old('complemento') }}">
          @error('complemento')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-xl-4">
          <label for="bairro">{{ __('Bairro') }}</label>
          <input type="text" class="form-control" id="bairro" name="bairro"  value="{{ old('bairro') }}">
          @error('bairro')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="cidade">{{ __('Cidade') }}</label>
          <input type="text" class="form-control" id="cidade" name="cidade"  value="{{ old('cidade') }}">
          @error('cidade')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="estado">{{ __('Estado') }}</label>
          <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado">
            <option value="">{{ __('Selecione') }}</option>
            @php
              //Colocar no banco de dados , esta estranho assim
              $ufs = [
                'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
              ];
            @endphp
             @foreach ($ufs as $key => $value)
             <option value="{{ $key }}" {{ (old('estado') == $key) ? 'selected' : '' }}>{{ $value }}</option>
           @endforeach
         </select>
          @error('estado')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>

      </div>
      <div class="row mb-4">
        <div class="col-xl-12">
          <label for="observacoes">{{ __('Observações') }}</label>
          <input type="text" class="form-control" id="observacoes" name="observacoes"  value="{{ old('observacoes') }}">
          @error('observacoes')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
      </div>
    </blockquote>
</div>
