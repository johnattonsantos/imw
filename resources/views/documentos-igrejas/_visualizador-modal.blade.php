@once
<div class="modal fade" id="documentoVisualizadorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentoVisualizadorTitulo">{{ __('Visualização do documento') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 75vh; background: #f8f9fa;">
                <iframe id="documentoVisualizadorFrame" src="about:blank" title="{{ __('Visualização do documento') }}" style="width: 100%; height: 100%; border: 0;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-rounded" data-dismiss="modal">{{ __('Fechar') }}</button>
            </div>
        </div>
    </div>
</div>
@endonce
