    <button class="btn btn-sm btn-info mr-2 btn-rounded btn-visualizar bs-tooltip" title="{{ __('Visualizar dados da pessoa') }}"
        data-membro-id="{{ $visitante->id }}">
        <x-bx-show />
    </button>
@if (!$visitante->deleted_at)
    <a  href="{{ route('congregado.editar', $visitante->id) }}"
        title="{{ __('Tornar Congregado') }}"
        class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
        </svg>
    </a>
    <a  href="{{ route('visitante.editar', $visitante->id) }}"
        title="{{ __('Editar') }}"
        class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
        </svg>
    </a>
    <form action="{{ route('visitante.deletar', $visitante->id) }}" method="POST" style="display: inline-block;" id="form_delete_visitante_{{ $visitante->id }}">
        @csrf
        <button type="button"
                title="{{ __('Apagar') }}"
                class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete bs-tooltip"
                data-form-id="form_delete_visitante_{{ $visitante->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </button>
    </form>
@endif
