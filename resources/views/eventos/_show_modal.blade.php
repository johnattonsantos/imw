<div class="modal-header">
    <h5 class="modal-title">Detalhes do Evento</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @include('eventos._show_content')
</div>
<div class="modal-footer">
    @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-editar'))
        <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-dark">Editar</a>
    @endif
    <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
</div>
