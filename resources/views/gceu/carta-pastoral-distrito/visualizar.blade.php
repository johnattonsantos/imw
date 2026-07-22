<style>
    .blockui-growl-message {
        display: none;
        text-align: left;
        padding: 15px;
        background-color: #455a64;
        color: #fff;
        border-radius: 3px;
    }

    .blockui-animation-container {
        display: none;
    }

    .multiMessageBlockUi {
        display: none;
        background-color: #455a64;
        color: #fff;
        border-radius: 3px;
        padding: 15px 15px 10px 15px;
    }

    .multiMessageBlockUi i {
        display: block
    }
</style>

<div class="modal-header">
    <h5 class="modal-title">{{ __('Visualização da Carta Pastoral') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div><h6>{{ __('Titulo') }}</h6></div>
                <div style="margin-bottom: 15px;">{{ $cartaPastoral->titulo }}</div>
                <div><h6>{{ __('Carta Pastoral') }}</h6></div>
                <div style="margin-bottom: 15px;">{!! $cartaPastoral->conteudo !!}</div>
                <div><h6>{{ __('Pastor') }}</h6></div>
                <div>{{ $cartaPastoral->pastor }}</div>
            </div>
        </div>
    </div>
</div>
