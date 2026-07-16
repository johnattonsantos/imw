<div class="modal fade" id="modalInstituicoes" tabindex="-1" role="dialog" aria-labelledby="modalInstituicoesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" class="form-control" id="searchInstituicao" placeholder="{{ __('Pesquisar instituição') }}">
                <button type="button" class="btn btn-primary btn-lg" id="searchButton">{{ __('Pesquisar') }}</button>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Imagem de carregamento -->
                <div id="loading" style="display: none; align-items: center; justify-content: center; display: flex; height: 100px;">
                    <img src="{{ asset('theme/assets/img/loading.gif') }}" width="100px" alt="{{ __('Carregando...') }}" />
                </div>
                <!-- As instituições serão carregadas aqui -->
                <div id="instituicoesList"></div>
            </div>
            <div class="modal-footer">
                <!-- Os links de paginação serão inseridos aqui -->
            </div>
        </div>
    </div>
</div>