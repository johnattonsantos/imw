<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="nome">{{ __('* Local do Evento') }}</label>
            <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $local->nome) }}" maxlength="180" required>
            @error('nome')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Status') }}</label>
            <div class="form-check mt-2">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" id="ativo" value="1" class="form-check-input" {{ old('ativo', $local->ativo ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo">{{ __('Ativo') }}</label>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="endereco">{{ __('Endereço') }}</label>
    <input type="text" name="endereco" id="endereco" class="form-control @error('endereco') is-invalid @enderror" value="{{ old('endereco', $local->endereco) }}" maxlength="180">
    @error('endereco')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="observacoes">{{ __('Observações') }}</label>
    <textarea name="observacoes" id="observacoes" rows="3" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $local->observacoes) }}</textarea>
    @error('observacoes')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
