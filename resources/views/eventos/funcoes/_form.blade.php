<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="nome">* Nome</label>
            <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $funcao->nome) }}" maxlength="120" required>
            @error('nome')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Status</label>
            <div class="form-check mt-2">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" id="ativo" value="1" class="form-check-input" {{ old('ativo', $funcao->ativo ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo">Ativo</label>
            </div>
        </div>
    </div>
</div>
