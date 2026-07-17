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
    <h5 class="modal-title">Dados: {{ $gceu->nome }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <table class="table table-bordered table-striped table-hover mb-4 dataTable no-footer">
                    <thead>
                        <tr><th>{{ __('Nome:') }}</th><th>{{ __('Anfitriao:') }}</th><th>{{ __('Contato:') }}</th> <th>{{ __('E-mail:') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>{{ $gceu->nome }}</td><td>{{ $gceu->anfitriao }}</td> <td>{{ $gceu->contato }}</td> <td>{{ $gceu->email }}</td></tr>
                    </tbody>
                    <thead>
                        <tr><th>{{ __('CEP:') }}</th><th>{{ __('Endereco:') }}</th><th>{{ __('Número:') }}</th> <th>{{ __('Bairro:') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>{{ $gceu->cep }}</td><td>{{ $gceu->endereco }}</td> <td>{{ $gceu->numero }}</td>  <td>{{ $gceu->bairro }}</td></tr>
                    </tbody>
                    <thead>
                        <tr><th>{{ __('Cidade:') }}</th><th>{{ __('UF:') }}</th><th>{{ __('Igreja:') }}</th> <th>{{ __('Maps:') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>{{ $gceu->cidade }}</td><td>{{ $gceu->uf }}</td> <td>{{ $gceu->igreja }}</td>  <td ><a href="https://www.google.com/maps/?q='{{ $gceu->endereco }}, {{ $gceu->numero }}, {{ $gceu->bairro }}, {{ $gceu->cidade }}, {{ $gceu->uf }}'"  title="{{ __('Acessar no Google Maps') }}" target="_blank" rel="noopener noreferrer"><i class="fas fa-map-marker-alt" style="margin-left: 25px;"></i></a></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
