    <button class="btn btn-sm btn-info mr-2 btn-rounded btn-visualizar bs-tooltip" title="{{ __('Visualizar') }}"
        data-cartapastoralid="{{ $cartaPastoral->id }}">
        <x-bx-show />
    </button>
    <a  href="{{ route('gceu.carta-pastoral.pdf', $cartaPastoral->id) }}" target="_blank" class="btn btn-sm btn-warning mr-2 btn-rounded bs-tooltip" title="{{ __('Gerar PDF') }}">
        <i class="fa fa-file-pdf" style="font-size: 14px;"></i>
    </a>
@if (!$cartaPastoral->deleted_at)
    <a  href="{{ route('gceu.carta-pastoral.editar', $cartaPastoral->id) }}"
        title="{{ __('Editar') }}"
        class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
        </svg>
    </a>
    <form action="{{ route('gceu.carta-pastoral.deletar', $cartaPastoral->id) }}" method="POST" style="display: inline-block;" id="form_delete_gceu_carta_pastoral_{{ $cartaPastoral->id }}">
        @csrf
        <button type="button"
                title="{{ __('Apagar') }}"
                class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete bs-tooltip"
                data-form-id="form_delete_gceu_carta_pastoral_{{ $cartaPastoral->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </button>
    </form>
@endif
