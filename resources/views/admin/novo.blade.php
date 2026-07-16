@extends('template.layout')
@section('extras-css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
        ['text' => 'Segurança', 'url' => '/', 'active' => false],
        ['text' => 'Todos Usuários', 'url' => '/admin/', 'active' => false],
        ['text' => 'Novo', 'url' => '/usuarios/novo', 'active' => true],
    ]">
</x-breadcrumb>
@endsection
@include('extras.alerts')
@section('content')
{{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif --}}

<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Novo Usuário') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form autocomplete="off" class="form-horizontal" method="POST" id="formCreatePaciente" action="{{ route('admin.store') }}">
                @csrf
                <div class="row mb-1">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>{{ __('* Nome completo') }}</label>
                            <div class="controls">
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required minlength="4" autocomplete="off" value="{{ old('name') }}">
                                @error('name')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>{{ __('* E-mail') }}</label>
                            <div class="controls">
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" required autocomplete="off" value="{{ old('email') }}" />
                                @error('email')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4" >
                    <div class="col-lg-5" id="col-cpf" >
                        <div class="form-group">
                            <label>{{ __('* CPF') }}</label>
                            <div class="input-group">
                                <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror" autocomplete="off" value="{{ old('cpf') }}"/>
                            </div>
                            @error('cpf')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-5" id="col-telefone">
                        <div class="form-group">
                            <label>{{ __('* Telefone') }}</label>
                            <div class="input-group">
                                <input type="text" name="telefone" id="telefone" class="form-control @error('telefone') is-invalid @enderror" autocomplete="off" value="{{ old('telefone') }}" />
                            </div>
                            @error('telefone')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>{{ __('* Senha') }}</label>
                            <div class="controls">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" />
                                @error('password')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>{{ __('* Confirmar Senha') }}</label>
                            <div class="controls">
                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password" />
                                @error('password_confirmation')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 d-flex align-items-center">
                        <div class="password-strength-meter-container text-center">
                            <div id="password-strength-meter" class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0;"></div>
                            </div>
                            <div id="password-strength-text"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered" id="dynamicAddRemove">
                            <thead>
                                <tr>
                                    <th>{{ __('* Perfil') }}</th>
                                    <th>{{ __('* Instituição') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control @error('perfil_id.*') is-invalid @enderror" name="perfil_id[]">
                                                <option value="">{{ __('Selecione um perfil') }}</option>
                                                @foreach ($perfis as $perfil)
                                                <option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        @error('perfil_id.*')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror

                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control instituicao-nome @error('instituicao_id.*') is-invalid @enderror" name="instituicao_nome[]" readonly>
                                            <input type="hidden" name="instituicao_id[]">
                                            <button type="button" class="btn btn-secondary abrirModalInstituicoes" data-bs-toggle="modal" data-bs-target="#modalInstituicoes">{{ __('Selecionar') }}</button>

                                        </div>
                                        @error('instituicao_id.*')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-rounded btn-add"><i class="fas fa-plus"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="row mt-4" id="check_clerigo_container" {{ old('pessoa_id') ? '' : 'hidden' }}>
                    <div class="col-12">
                        <div class="form-group">
                            <div class="n-chk">
                                <label class="new-control new-checkbox checkbox-outline-success ">
                                    <input id="chk_clerigo_id" data-clerigoId="{{ old('pessoa_id') }}" name="chk_clerigo_id" type="checkbox" class="new-control-input" {{ old('pessoa_id') ? 'checked' : '' }}>                                        <span class="new-control-indicator"></span> 
                                    <p>{{ __('Vincular Vincular Clérigo(a)') }} <span id="clerigo_nome" class="font-weight-bold font-italic">
                                        {{ old('pessoa_id') ? \App\Models\PessoasPessoa::find(old('pessoa_id'))->nome : '' }}
                                    </span> {{ __('ao cadastro?') }}</p>
                                </label>
                                <input type="hidden" name="pessoa_id" id="pessoa_id" value="{{ old('pessoa_id') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- modal instituicoes --}}
                @include('usuarios.modal-instituicoes')

                <br><br>
                <button type="submit" class="btn btn-primary btn-rounded" id="cadastrar">{{ __('Salvar') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection


@section('extras-scripts')

<script>
    $(document).ready(function() {
        let i = 1;
        let currentButton = null;

        function generatePerfilOptions() {
            let options = '<option value="">Selecione um perfil</option>';
            @foreach($perfis as $perfil)
            options += `<option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>`;
            @endforeach
            return options;
        }

        $(".btn-add").click(function() {
            i++;
            let perfilOptions = generatePerfilOptions();

            let markup = `
    <tr>
        <td>
            <div class="form-group">
                <select class="form-control" name="perfil_id[]">
                    ${perfilOptions}
                </select>
            </div>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control instituicao-nome" name="instituicao_nome[]"
                    readonly>
                <input type="hidden" name="instituicao_id[]">
                <button type="button" class="btn btn-secondary abrirModalInstituicoes"
                    data-bs-toggle="modal" data-bs-target="#modalInstituicoes">Selecionar</button>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-rounded btn-remove"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>`;
            $("#dynamicAddRemove").append(markup);
        });

        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            i--;
        });

        $(document).on('click', '.abrirModalInstituicoes', function() {
            currentButton = $(this).closest('tr').find('.abrirModalInstituicoes');
            console.log(`abriu`);
            loadInstituicoes(1); // Carrega a primeira página
            $('#modalInstituicoes').modal('show');
        });

        $('#searchButton').on('click', function() {
            loadInstituicoes(1, $('#searchInstituicao').val());
        });

        function loadInstituicoes(page, search = '') {
            $('#loading').show();
            $('#instituicoesList').hide();

            $.ajax({
                url: '/instituicoes?page=' + page + '&search=' + search,
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#instituicoesList').html('');
                    response.data.forEach(function(instituicao) {
                        $('#instituicoesList').append(
                            `<div class="list-group-item list-group-item-action instituicao-item" data-id="${instituicao.id}" data-nome="${instituicao.nome}" style="cursor: pointer;">
                    <span class="badge badge-primary">${instituicao.nome}</span> / <span class="badge badge-secondary">${instituicao.instituicao_pai_nome}</span>
                </div>`
                        );
                    });

                    $('#loading').hide();
                    $('#instituicoesList').show();

                    $('#modalInstituicoes .modal-footer .pagination').remove();
                    let paginationLinks = generatePaginationLinks(response);
                    $('#modalInstituicoes .modal-footer').prepend(paginationLinks);
                }
            });
        }

        function generatePaginationLinks(response) {
            let paginationLinks = '<div class="pagination">';
            let currentPage = response.current_page;
            let totalPage = response.last_page;
            let range = 2;

            if (totalPage <= (range * 2) + 3) {
                for (let page = 1; page <= totalPage; page++) {
                    paginationLinks += generatePageLink(page, currentPage);
                }
            } else {
                paginationLinks += generatePageLink(1, currentPage);
                paginationLinks += (currentPage > range + 2) ? '...' : '';

                let start = Math.max(currentPage - range, 2);
                let end = Math.min(currentPage + range, totalPage - 1);

                for (let page = start; page <= end; page++) {
                    paginationLinks += generatePageLink(page, currentPage);
                }

                paginationLinks += (currentPage < totalPage - range - 1) ? '...' : '';
                paginationLinks += generatePageLink(totalPage, currentPage);
            }

            paginationLinks += '</div>';
            return paginationLinks;
        }

        function generatePageLink(page, currentPage) {
            return `<a href="#" class="page-link ${page === currentPage ? 'active' : ''}" data-page="${page}">${page}</a> `;
        }

        $('#modalInstituicoes').on('click', '.page-link', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            loadInstituicoes(page);
        });

        $('#modalInstituicoes').on('click', '.instituicao-item', function() {
            console.log("Item de instituição clicado!");

            var nome = $(this).data('nome');
            var id = $(this).data('id');

            console.log("Nome da instituição:", nome);
            console.log("ID da instituição:", id);

            if (currentButton !== null) {
                // Encontra a linha (tr) onde o botão "Selecionar" foi clicado
                var currentRow = currentButton.closest('tr');

                // Seletor do input de nome corrigido
                var inputNome = currentRow.find('.instituicao-nome');
                console.log("Elemento de input para nome:", inputNome);

                // Seletor do input de ID corrigido
                var inputId = currentRow.find('input[name="instituicao_id[]"]');
                console.log("Elemento de input para ID:", inputId);

                inputNome.val(nome);
                inputId.val(id);

                $('#modalInstituicoes').modal('hide');
            } else {
                console.error("Botão atual não definido.");
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#telefone').mask('(00) 00000-0000');
        $('#cpf').mask('000.000.000-00');
    });
</script>
<script src="{{ asset('usuarios/js/vincula-clerigo.js') }}?t={{ time() }}"></script>
@endsection
