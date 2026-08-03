@php
  $filhosInformados = is_array($filhosValor ?? null)
    ? $filhosValor
    : preg_split('/\s*;\s*|\R/u', trim((string) ($filhosValor ?? '')));
  $filhosInformados = array_values(array_filter(
    array_map(fn ($filho) => trim((string) $filho), $filhosInformados ?: []),
    fn ($filho) => $filho !== ''
  ));
  $filhosInformados = $filhosInformados ?: [''];
@endphp

<label>{{ __('Filhos') }}</label>
<div class="filhos-inputs-container">
  @foreach ($filhosInformados as $index => $filho)
    <div class="input-group mb-2 filho-input-row">
      <input
        type="text"
        class="form-control"
        name="filhos[]"
        value="{{ $filho }}"
        maxlength="150"
        placeholder="{{ __('Nome do filho') }}"
        aria-label="{{ __('Nome do filho') }}"
      >
      <div class="input-group-append">
        @if ($index === 0)
          <button type="button" class="btn btn-outline-primary btn-adicionar-filho" title="{{ __('Adicionar outro filho') }}" aria-label="{{ __('Adicionar outro filho') }}">
            <i class="fas fa-plus"></i>
          </button>
        @else
          <button type="button" class="btn btn-outline-danger btn-remover-filho" title="{{ __('Remover filho') }}" aria-label="{{ __('Remover filho') }}">
            <i class="fas fa-trash-alt"></i>
          </button>
        @endif
      </div>
    </div>
  @endforeach
</div>
<small class="form-text text-muted">{{ __('Informe um filho por campo.') }}</small>
@error('filhos')
  <span class="help-block text-danger">{{ $message }}</span>
@enderror
@error('filhos.*')
  <span class="help-block text-danger">{{ $message }}</span>
@enderror

@push('tab-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.filhos-inputs-container').forEach(function (container) {
      container.addEventListener('click', function (event) {
        const addButton = event.target.closest('.btn-adicionar-filho');
        const removeButton = event.target.closest('.btn-remover-filho');

        if (addButton) {
          const row = document.createElement('div');
          row.className = 'input-group mb-2 filho-input-row';
          row.innerHTML =
            '<input type="text" class="form-control" name="filhos[]" maxlength="150" ' +
              'placeholder="{{ __('Nome do filho') }}" aria-label="{{ __('Nome do filho') }}">' +
            '<div class="input-group-append">' +
              '<button type="button" class="btn btn-outline-danger btn-remover-filho" ' +
                'title="{{ __('Remover filho') }}" aria-label="{{ __('Remover filho') }}">' +
                '<i class="fas fa-trash-alt"></i>' +
              '</button>' +
            '</div>';
          container.appendChild(row);
          row.querySelector('input').focus();
        }

        if (removeButton) {
          removeButton.closest('.filho-input-row').remove();
        }
      });
    });
  });
</script>
@endpush
