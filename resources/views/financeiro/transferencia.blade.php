@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Movimento de Caixa', 'url' => '/financeiro/movimento-caixa', 'active' => false],
        ['text' => 'Transferência', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/pt-BR.js"></script>
    <style>
        .select2-selection__rendered {
            line-height: 50px !important;
            padding-left: 25px !important;
            font-family: 'Nunito', sans-serif;
        }

        .select2-container .select2-selection--single {
            height: 50px !important;
            font-weight: 600;
            font-family: 'Nunito', sans-serif;
        }

        .select2-selection__arrow {
            height: 50px !important;
            font-family: 'Nunito', sans-serif;

        }
    </style>
@endsection

@section('content')
    @include('extras.alerts')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Transferência') }}</h4>
                    </div>
                </div>
            </div>

            <form action="{{ route('financeiro.transferencia.store') }}" method="POST" class="widget-content widget-content-area">
                @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="caixa_origem_id">{{ __('* Caixa Origem') }}</label>
                            <select class="form-control @error('caixa_origem_id') is-invalid @enderror" id="caixa_origem_id" name="caixa_origem_id" required>
                                <option value="" hidden disabled>{{ __('Selecione') }}</option>
                                @foreach ($caixas as $caixa)
                                    @if ($caixa->id == old('caixa_origem_id'))
                                        <option value="{{ $caixa->id }}" selected>{{ $caixa->descricao }}</option>
                                    @else
                                        <option value="{{ $caixa->id }}">{{ $caixa->descricao }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('caixa_origem_id')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="caixa_destino_id">{{ __('* Caixa Destino') }}</label>
                            <select class="form-control @error('caixa_destino_id') is-invalid @enderror" id="caixa_destino_id" name="caixa_destino_id" required>
                                <option value="" hidden disabled>{{ __('Selecione') }}</option>
                                @foreach ($caixas as $caixa)
                                    @if ($caixa->id == old('caixa_destino_id'))
                                        <option value="{{ $caixa->id }}" selected>{{ $caixa->descricao }}</option>
                                    @else
                                        <option value="{{ $caixa->id }}">{{ $caixa->descricao }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('caixa_destino_id')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="plano_conta_id">{{ __('* Plano de contas') }}</label>
                        <select class="form-control @error('plano_conta_id') is-invalid @enderror" id="plano_conta_id"
                            name="plano_conta_id" required>
                            <option value="" hidden selected disabled>{{ __('Selecione') }}</option>
                            @foreach ($planoContas as $pc)
                                <option {{ !$pc->selecionavel ? 'disabled' : '' }} value="{{ $pc->id }}">
                                    {{ $pc->numeracao }} - {{ $pc->nome }}</option>
                            @endforeach
                        </select>
                        @error('pai')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="valor">{{ __('* Valor') }}</label>
                        <input type="text" class="form-control @error('valor') is-invalid @enderror" id="valor"
                            name="valor" required autofocus>
                        @error('valor')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="data_movimento">{{ __('* Data do Movimento') }}</label>
                        <input type="date" class="form-control @error('data_movimento') is-invalid @enderror"
                            id="data_movimento" name="data_movimento" value="{{ old('data_movimento', date('Y-m-d')) }}"
                            required>
                        @error('data_movimento')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="row mb-4">
                    <div class="col-12">
                        <label for="descricao">{{ __('* Descrição') }}</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                            value="{{ old('descricao') }}" rows="3" required></textarea>
                        @error('descricao')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4 justify-content-center">
                    <button type="submit" title="{{ __('Salvar nova movimentação de entrada') }}" class="btn btn-success btn-lg ml-4">
                        <x-bx-save /> {{ __('Salvar') }}
                    </button>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
@section('extras-scripts')
    <script>
        // máscara de valor
        $('#valor').mask('0.000.000.000,00', {
            reverse: true
        });
    </script>
@endsection
