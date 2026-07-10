<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nome">* Nome</label>
            <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $advogado->nome) }}" required>
            @error('nome')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="tipo">* Tipo</label>
            <select name="tipo" id="tipo" class="form-control" required>
                @foreach ($tipoOptions as $value => $label)
                    <option value="{{ $value }}" {{ old('tipo', $advogado->tipo) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="registro_oab">Registro da OAB</label>
            <input type="text" name="registro_oab" id="registro_oab" class="form-control" value="{{ old('registro_oab', $advogado->registro_oab) }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $advogado->telefone) }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $advogado->email) }}">
            @error('email')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label for="contatos">Contatos</label>
            <input type="text" name="contatos" id="contatos" class="form-control" value="{{ old('contatos', $advogado->contatos) }}">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="endereco_escritorio">Endereço do Escritório</label>
    <input type="text" name="endereco_escritorio" id="endereco_escritorio" class="form-control" value="{{ old('endereco_escritorio', $advogado->endereco_escritorio) }}">
</div>

<div class="form-group">
    <label for="observacoes">Observações</label>
    <textarea name="observacoes" id="observacoes" rows="4" class="form-control">{{ old('observacoes', $advogado->observacoes) }}</textarea>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('regiao.juridico.advogados.index') }}" class="btn btn-light">Voltar</a>
    <button type="submit" class="btn btn-primary">Salvar</button>
</div>
