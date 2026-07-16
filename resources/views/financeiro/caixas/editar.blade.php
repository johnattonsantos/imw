@extends('template.layout')
@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Financeiro', 'url' => '/', 'active' => false],
        ['text' => 'Caixas', 'url' => '/financeiro/caixas', 'active' => false],
        ['text' => 'Editar', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection
@include('extras.alerts')
@section('content')
    <div class="container-fluid">

    </div>
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('Editar Caixa') }}</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <!-- Conteúdo -->
                <form autocomplete="off" class="form-horizontal" method="POST" id="formEditarCaixa" action="{{ route('financeiro.caixas.update', $id) }}">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-lg-4">
                                    <label for="descricao">{{ __('* Descrição') }}</label>
                                    <input class="form-control @error('descricao') is-invalid @enderror" id="descricao"
                                        name="descricao" type="text" value="{{ $caixa->descricao }}">
                                    @error('descricao')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label for="descricao">{{ __('Tipo') }}</label>
                                    <select class="form-control @error('tipo') is-invalid @enderror" name="tipo">
                                        <option value="" {{ old('tipo', $caixa->tipo) == '' ? 'selected' : '' }}>{{ __('Selecione um tipo') }}</option>
                                        <option value="B" {{ old('tipo', $caixa->tipo) == 'B' ? 'selected' : '' }}>{{ __('Banco') }}</option>
                                        <option value="C" {{ old('tipo', $caixa->tipo) == 'C' ? 'selected' : '' }}>{{ __('Congregação') }}</option>
                                        <option value="S" {{ old('tipo', $caixa->tipo) == 'S' ? 'selected' : '' }}>{{ __('Secundário') }}</option>
                                    </select>
                                
                                    @error('tipo')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="Atualizar" class="btn btn-primary btn-lg mt-3">
                </form>
            </div>
        </div>
    </div>
@endsection
