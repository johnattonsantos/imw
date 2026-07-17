@extends('template.layout')
@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Financeiro', 'url' => '/', 'active' => false],
    ['text' => 'Plano Conta', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection
@section('content')

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('Filtros para pesquisa') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <!-- Conteúdo -->
                <!--Card Pesquisa-->
                <div class="card mb-3">
                    <div class="bg-holder d-none d-lg-block bg-card gb-title">
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row mb-4">
                                <div class="col-4">
                                    <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                        placeholder="{{ __('Pesquisar...') }}">
                                </div>
                                <div class="col-auto" style="margin-left: -19px;">
                                    <button type="submit" class="btn btn-primary btn-rounded">{{ __('Pesquisar') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">

                    <div class="col-12">
                        <table class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                            <thead class="thead-light">
                                <tr>
                
                                    <th style="width: 60px;">{{ __('Código') }}</th>
                                    <th>{{ __('Nome') }}</th>
                                    <th width="150"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planocontas as $conta)
                                <tr>
                                    <td>{{$conta->numeracao}}</td>
                                    <td>{{$conta->nome}}</td>
                                    <td class="table-action"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                
                        {{ $planocontas->links('vendor.pagination.index') }}
                    </div>
                </div>
            <!-- Fim conteúdo -->
        </div>
    </div>
</div>
@endsection