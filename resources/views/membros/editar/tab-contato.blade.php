@php
  $isRecadastramento = request()->routeIs('recadastramento-membro.editar') || request()->routeIs('recadastramento-membro.update');
  $contatoObrigatorio = old('status', $pessoa->status) === \App\Models\MembresiaMembro::STATUS_ATIVO;
@endphp

<div class="tab-pane fade" id="border-top-contato" role="tabpanel" aria-labelledby="border-top-contatos">
    <blockquote class="blockquote">
      <div class="row mb-4">
      <div class="col-xl-3">
          <label for="email_preferencial">{{ __('E-mail') }}</label>
          <input type="email" class="form-control @error('email_preferencial') is-invalid @enderror" id="email_preferencial" name="email_preferencial" value="{{ old('email_preferencial', $pessoa->contato->email_preferencial) }}" maxlength="100">
          @error('email_preferencial')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-3">
          <label for="telefone_preferencial">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Telefone') }}</label>
          <input type="text" class="form-control @error('telefone_preferencial') is-invalid @enderror" id="telefone_preferencial" name="telefone_preferencial" placeholder="ex: +55 (00) 0000-0000" value="{{ old('telefone_preferencial', $pessoa->contato->telefone_preferencial) }}" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('telefone_preferencial')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>


      </div>

      <div class="row mb-4">
        <div class="col-xl-2">
          <label for="cep">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('CEP') }}</label>
          <input type="text" id="cep" class="form-control @error('cep') is-invalid @enderror" name="cep"  value="{{ old('cep', $pessoa->contato->cep) }}" maxlength="8" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('cep')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="endereco">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Endereço') }}</label>
          <input type="text" class="form-control" id="endereco" name="endereco"  value="{{ old('endereco', $pessoa->contato->endereco) }}" maxlength="100" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('endereco')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-2">
          <label for="numero">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Número') }}</label>
          <input type="number" class="form-control" id="numero" name="numero"  value="{{ old('numero', $pessoa->contato->numero) }}" maxlength="20" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('numero')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="complemento">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Complemento') }}</label>
          <input type="text" class="form-control" id="complemento" name="complemento"  value="{{ old('complemento', $pessoa->contato->complemento) }}" maxlength="100" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('complemento')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-xl-4">
          <label for="bairro">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Bairro') }}</label>
          <input type="text" class="form-control" id="bairro" name="bairro"  value="{{ old('bairro', $pessoa->contato->bairro) }}" maxlength="100" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('bairro')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="cidade">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Cidade') }}</label>
          <input type="text" class="form-control" id="cidade" name="cidade"  value="{{ old('cidade', $pessoa->contato->cidade) }}" maxlength="100" {{ $contatoObrigatorio ? 'required' : '' }}>
          @error('cidade')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="col-xl-4">
          <label for="estado">{{ $contatoObrigatorio ? '* ' : '' }}{{ __('Estado') }}</label>
          <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" {{ $contatoObrigatorio ? 'required' : '' }}>
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
             <option value="{{ $key }}" {{ (old('estado', $pessoa->contato->estado) == $key) ? 'selected' : '' }}>{{ $value }}</option>
           @endforeach
         </select>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-xl-12">
          <label for="observacoes">{{ __('Observações') }}</label>
          <input type="text" class="form-control" id="observacoes" name="observacoes"  value="{{ old('observacoes', $pessoa->contato->observacoes) }}" maxlength="1000">
          @error('observacoes')
            <span class="help-block text-danger">{{ $message }}</span>
          @enderror
        </div>
      </div>
    </blockquote>
</div>
