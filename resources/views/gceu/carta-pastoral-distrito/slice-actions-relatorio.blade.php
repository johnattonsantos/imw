    <button class="btn btn-sm btn-info mr-2 btn-rounded btn-visualizar bs-tooltip" title="{{ __('Visualizar') }}"
        data-cartapastoralid="{{ $cartaPastoral->id }}">
        <x-bx-show />
    </button>
    <a  href="{{ route('distrito.carta-pastoral.pdf', $cartaPastoral->id) }}" target="_blank" class="btn btn-sm btn-warning mr-2 btn-rounded bs-tooltip" title="{{ __('Gerar PDF') }}">
        <i class="fa fa-file-pdf" style="font-size: 14px;"></i>
    </a>
