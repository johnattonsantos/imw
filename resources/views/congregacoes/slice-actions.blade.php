@if(!$congregacao->deleted_at && !$congregacao->dt_extincao)
    {{-- Editar congregação --}}
    <a href="{{ route('congregacao.editar', $congregacao->id) }}"
        title="{{ __('Editar') }}"
        class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
        <x-bx-pencil />
    </a>

    {{-- desativar congregação --}}
    <form action="{{ route('congregacao.desativar', $congregacao->id) }}" method="POST" style="display: inline-block;" id="form_desativar_congregacao_{{ $congregacao->id }}">
        @csrf
        @method('DELETE')
        <button type="button"
                title="{{ __('Apagar') }}"
                class="btn btn-sm btn-danger mr-2 btn-rounded btn-confirm-delete bs-tooltip"
                data-form-id="form_desativar_congregacao_{{ $congregacao->id }}">
           <x-bx-power-off />
        </button>
    </form>
@else
    {{-- restaurar congregação --}}
    <form action="{{ route('congregacao.restaurar', $congregacao->id) }}" method="POST" style="display: inline-block;" id="form_restaurar_congregacao_{{ $congregacao->id }}">
        @csrf
        @method('PUT')
        <button type="button"
                title="{{ __('Restaurar') }}"
                class="btn btn-sm btn-success mr-2 btn-rounded btn-confirm-restore bs-tooltip"
                data-form-id="form_restaurar_congregacao_{{ $congregacao->id }}">
            <x-bx-power-off />
        </button>
    </form>
@endif