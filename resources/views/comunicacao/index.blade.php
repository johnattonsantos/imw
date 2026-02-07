@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Comunicacao', 'url' => '/comunicacao', 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('extras-scripts')
<script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
<script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
@endsection

@include('extras.alerts')

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Comunicacao</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <form method="GET" class="mb-3">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                            placeholder="Pesquisar por titulo ou comentario...">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="mb-3 d-flex" style="gap: 8px;">
                <a href="{{ route('comunicacao.create') }}" class="btn btn-primary btn-sm">Novo</a>
                <a href="{{ route('comunicacao.export.xlsx', request()->query()) }}" class="btn btn-success btn-sm">Exportar XLSX</a>
                <a href="{{ route('comunicacao.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">Exportar PDF</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Comentario</th>
                            <th>Arquivo</th>
                            <th>Criado em</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($comunicacoes as $index => $comunicacao)
                            <tr>
                                <td>{{ $comunicacao->titulo }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($comunicacao->comentario), 80) }}</td>
                                <td>
                                    @if ($comunicacao->arquivo)
                                        <a href="{{ route('comunicacao.visualizar', $comunicacao) }}" target="_blank">Visualizar</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($comunicacao->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="table-action">
                                    <a href="{{ route('comunicacao.show', $comunicacao) }}" class="btn btn-sm btn-info btn-rounded">Detalhes</a>
                                    <a href="{{ route('comunicacao.edit', $comunicacao) }}" class="btn btn-sm btn-dark btn-rounded">Editar</a>

                                    <form action="{{ route('comunicacao.destroy', $comunicacao) }}" method="POST"
                                        style="display: inline-block;" id="form_delete_comunicacao_{{ $index }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-rounded btn-confirm-delete"
                                            data-form-id="form_delete_comunicacao_{{ $index }}">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $comunicacoes->links('vendor.pagination.index') }}
        </div>
    </div>
</div>

<script>
    $('.btn-confirm-delete').on('click', function() {
        const formId = $(this).data('form-id');

        swal({
            title: 'Primeira confirmacao',
            text: 'Deseja continuar para a exclusao?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar',
            padding: '2em'
        }).then(function(firstResult) {
            if (!firstResult.value) {
                return;
            }

            swal({
                title: 'Confirmacao final',
                text: 'Esta acao nao podera ser desfeita. Confirma excluir?',
                type: 'error',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#3085d6',
                padding: '2em'
            }).then(function(secondResult) {
                if (secondResult.value) {
                    document.getElementById(formId).submit();
                }
            });
        });
    });
</script>
@endsection
