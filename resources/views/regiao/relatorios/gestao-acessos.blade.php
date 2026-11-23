@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[['text' => 'Região', 'url' => '#', 'active' => false], ['text' => 'Histórico de Acessos', 'url' => '#', 'active' => true]]" />
@endsection
@section('extras-css')
<link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/table/datatable/dt-global_style.css') }}" rel="stylesheet" type="text/css" />
<style>
    .historico-acessos .widget-content-area {
        padding: 24px;
    }
    .historico-filtros {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 20px 24px;
        margin-bottom: 24px;
    }
    .historico-filtros .form-group {
        min-width: 0;
        margin-bottom: 0;
    }
    .historico-filtros label {
        display: block;
        margin-bottom: 8px;
    }
    .historico-filtros .form-control {
        width: 100%;
        min-width: 0;
        height: 42px;
    }
    .historico-filtros .historico-buscar {
        display: flex;
        align-items: flex-end;
    }
    .historico-filtros .btn {
        min-height: 42px;
    }
    @media (min-width: 768px) {
        .historico-filtros {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .historico-filtros .historico-instituicao {
            grid-column: span 2;
        }
    }
    @media (min-width: 992px) {
        .historico-filtros {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .historico-filtros .historico-usuario {
            grid-column: span 3;
        }
    }
    @media (max-width: 767px) {
        .historico-acessos .widget-content-area {
            padding: 16px;
        }
        .historico-filtros .btn {
            width: 100%;
        }
    }
</style>
@endsection
@section('content')
@include('extras.alerts')
<div class="col-12 layout-spacing historico-acessos">
    <div class="statbox widget box box-shadow">
        <div class="widget-header"><h4>Histórico de Acessos - {{ $regiao->nome }}</h4></div>
        <div class="widget-content widget-content-area">
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach($errors->all() as $erro)<div>{{ $erro }}</div>@endforeach
                </div>
            @endif
            <form method="GET" action="{{ route('regiao.relatorio.gestao-acessos') }}" class="historico-filtros">
                <input type="hidden" name="buscar" value="1">
                <div class="form-group">
                    <label for="data_inicial">Data inicial</label>
                    <input type="date" class="form-control" name="data_inicial" id="data_inicial" value="{{ old('data_inicial', $filtros['data_inicial']) }}" required>
                </div>
                <div class="form-group">
                    <label for="data_final">Data final</label>
                    <input type="date" class="form-control" name="data_final" id="data_final" value="{{ old('data_final', $filtros['data_final']) }}" aria-describedby="limite-periodo" required>
                    <small class="text-muted" id="limite-periodo">Período máximo: 31 dias.</small>
                </div>
                <div class="form-group historico-instituicao">
                    <label for="instituicao_id">Instituição</label>
                    <select class="form-control" name="instituicao_id" id="instituicao_id">
                        <option value="">Todas</option>
                        @foreach($instituicoes as $instituicao)
                            <option value="{{ $instituicao->id }}" {{ (string) old('instituicao_id', $filtros['instituicao_id']) === (string) $instituicao->id ? 'selected' : '' }}>{{ $instituicao->tipo }} - {{ $instituicao->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group historico-usuario">
                    <label for="usuario">Usuário (nome ou parte do nome)</label>
                    <input class="form-control" type="search" name="usuario" id="usuario" value="{{ old('usuario', $filtros['usuario']) }}" maxlength="150" placeholder="Digite o nome do usuário">
                </div>
                <div class="form-group historico-buscar"><button type="submit" class="btn btn-primary">Buscar</button></div>
            </form>
            @if($buscar)
                <div id="historico-erro" class="alert alert-danger d-none" role="alert"></div>
                <div class="table-responsive mb-4">
                    <table id="historico-acessos-table" class="table table-bordered table-striped table-hover mb-4" style="width: 100%">
                        <thead><tr><th>INSTITUIÇÃO</th><th>USUÁRIO</th><th>PERFIL</th><th>INÍCIO DA SESSÃO</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('extras-scripts')
<script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
<script>
$(function () {
    const inicio = document.getElementById('data_inicial');
    const fim = document.getElementById('data_final');
    function limitarPeriodo() {
        fim.min = inicio.value;
        if (!inicio.value) {
            fim.removeAttribute('max');
            return;
        }
        const limite = new Date(inicio.value + 'T00:00:00Z');
        limite.setUTCDate(limite.getUTCDate() + 30);
        fim.max = limite.toISOString().slice(0, 10);
    }
    inicio.addEventListener('change', limitarPeriodo);
    limitarPeriodo();
    @if($buscar)
    const filtros = @json($filtros);
    $('#historico-acessos-table').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 600,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[3, 'desc']],
        orderMulti: false,
        pagingType: 'full_numbers',
        language: {url: @json(asset('custom/i18n/datatables-pt-BR.json'))},
        ajax: function (data, callback) {
            const erro = $('#historico-erro').addClass('d-none').text('');
            $.ajax({
                url: @json(route('regiao.relatorio.gestao-acessos')),
                data: Object.assign({}, data, filtros, {buscar: 1}),
                dataType: 'json',
                success: callback,
                error: function (xhr) {
                    const errors = xhr.responseJSON && xhr.responseJSON.errors;
                    const mensagem = errors ? Object.values(errors).flat().join(' ') : 'Não foi possível consultar o histórico. Tente novamente.';
                    erro.removeClass('d-none').text(mensagem);
                    callback({draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: []});
                }
            });
        },
        columns: [
            {data: 'instituicao', render: $.fn.dataTable.render.text()},
            {data: 'usuario', render: $.fn.dataTable.render.text()},
            {data: 'perfil', render: $.fn.dataTable.render.text()},
            {data: 'primeiro', render: $.fn.dataTable.render.text()}
        ]
    });
    @endif
});
</script>
@endsection
