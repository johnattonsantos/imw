@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Secretaria', 'url' => route('membro.index'), 'active' => false],
        ['text' => 'Carteirinhas', 'url' => route('membro.carteirinhas.index'), 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    @include('extras.alerts')

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Carteirinhas') }}</h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <form id="form-carteirinhas" action="{{ route('membro.carteirinhas.pdf') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="igreja_id" value="all">

                    <div class="row align-items-end mb-3">
                        <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 mb-3">
                            <p class="mb-1">
                                <strong>{{ data_get(session('session_perfil'), 'instituicoes.igrejaLocal.nome') ?: data_get(session('session_perfil'), 'instituicao_nome') }}</strong>
                            </p>
                            <p class="text-muted mb-0">
                                {{ __('Selecione os membros que terão a carteirinha gerada no PDF.') }}
                            </p>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3 text-lg-right">
                            <button type="submit" id="btn-imprimir-carteirinhas" class="btn btn-primary btn-rounded" disabled>
                                <i class="fas fa-print"></i> {{ __('Imprimir Selecionados') }}
                            </button>
                        </div>
                    </div>

                    @if($membros->isEmpty())
                        <div class="alert alert-warning mb-0" role="alert">
                            {{ __('Nenhum membro ativo encontrado para gerar carteirinhas.') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-3">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;" class="text-center">
                                            <input type="checkbox" id="selecionar-todos-carteirinhas" aria-label="{{ __('Selecionar todos') }}">
                                        </th>
                                        <th>{{ __('Membro') }}</th>
                                        <th>{{ __('Igreja') }}</th>
                                        <th>{{ __('Nascimento') }}</th>
                                        <th>{{ __('Função Eclesiástica') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($membros as $membro)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <input type="checkbox" class="membro-carteirinha" name="membros[]" value="{{ $membro->id }}" aria-label="{{ __('Selecionar :name', ['name' => $membro->nome]) }}">
                                            </td>
                                            <td class="align-middle">{{ $membro->nome }}</td>
                                            <td class="align-middle">{{ $membro->igreja ?: '-' }}</td>
                                            <td class="align-middle">{{ $membro->data_nascimento ? formatDate($membro->data_nascimento) : '-' }}</td>
                                            <td class="align-middle">{{ $membro->funcao_eclesiastica ?: __('Não informado') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="text-muted mb-0">
                            <span id="total-selecionados-carteirinhas">0</span>
                            {{ __('membro(s) selecionado(s). O PDF será gerado em A4 paisagem com 4 carteirinhas por página.') }}
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selecionar-todos-carteirinhas');
            const checkboxes = Array.from(document.querySelectorAll('.membro-carteirinha'));
            const totalSelected = document.getElementById('total-selecionados-carteirinhas');
            const printButton = document.getElementById('btn-imprimir-carteirinhas');
            const form = document.getElementById('form-carteirinhas');

            function refreshSelectionState() {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (totalSelected) {
                    totalSelected.textContent = checkedCount;
                }

                if (printButton) {
                    printButton.disabled = checkedCount === 0;
                }

                if (selectAll) {
                    selectAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                    selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });
                    refreshSelectionState();
                });
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', refreshSelectionState);
            });

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!checkboxes.some((checkbox) => checkbox.checked)) {
                        event.preventDefault();
                        alert('{{ __('Selecione ao menos um membro para imprimir as carteirinhas.') }}');
                    }
                });
            }

            refreshSelectionState();
        });
    </script>
@endsection
