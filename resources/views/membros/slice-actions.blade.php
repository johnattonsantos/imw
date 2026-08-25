@if ($rolMembro->status == \App\Models\MembresiaMembro::STATUS_ATIVO)
    @if($rolMembro->notificacaoTransferenciaAtiva)
        <form action="{{ route('membro.exclusao_transferencia.cancel', $rolMembro->notificacaoTransferenciaAtiva->id) }}"
              method="POST" style="display: none;"
              id="form_cancel_notificacao_transferencia_{{ $rolMembro->notificacaoTransferenciaAtiva->id }}">
            @csrf
            @method('DELETE')
        </form>
        <button title="{{ __('Cancelar Transferência') }}"
                class="btn btn-sm btn-danger mr-2 btn-rounded btn-cancel-notificacao-transferencia bs-tooltip"
                data-form-id="form_cancel_notificacao_transferencia_{{ $rolMembro->notificacaoTransferenciaAtiva->id }}">
            <x-bx-transfer-alt />
        </button>
    @else
        <a href="{{ route('membro.editar', $rolMembro->membro_id) }}" title="{{ __('Editar') }}"
            class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-edit-2">
                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
            </svg>
        </a>
    @endif
@endif

<button class="btn btn-sm btn-info mr-2 btn-rounded btn-visualizar bs-tooltip"
        title="{{ __('Visualizar dados da pessoa') }}"
        data-membro-id="{{ $rolMembro->membro_id }}">
    <x-bx-show />
</button>
