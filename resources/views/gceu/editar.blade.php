@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'GCEU', 'url' => '/gceu/lista', 'active' => false],
    ['text' => 'Editar', 'url' => '#', 'active' => true]
]"></x-breadcrumb>
@endsection

@include('extras.alerts')

@section('content')
<div id="tableHover" class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>Editar GCEU</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            @if($gceu)
            <form class="form-vertical" action="{{ route('gceu.update',$gceu->id) }}" id="form_create_gceu" method="post">
                @csrf
                <div class="row">
                    <div class="form-group mb-4 col-8">
                        <label class="control-label" for="nome">* Nome do GCEU</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" id="nome" required placeholder="Nome do GCEU" minlength="4" value="{{ $gceu->nome }}" maxlength="150">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">* Congregação</label>
                        <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" required>
                            <option value="sede" {{ $gceu->congregacao_id == '' ? 'selected' : '' }}>SEDE</option>
                            @foreach ($congregacoes as $congregacao)
                                <option value="{{ $congregacao->id }}" {{ $gceu->congregacao_id == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
                            @endforeach
                        </select>
                        @error('congregacao_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- <div class="form-group mb-4 col-6">
                        <label class="control-label" for="anfitriao">* Anfitrião</label>
                        <input type="text" name="anfitriao" id="anfitriao" class="form-control @error('anfitriao') is-invalid @enderror" placeholder="Nome do Anfitrião" minlength="4" value="{{ $gceu->anfitriao }}" required maxlength="100">
                        @error('anfitriao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->
                </div>
                <!-- <div class="row">
                    <div class="form-group mb-4 col-md-5">
                        <label class="control-label" for="email">E-mail</label>
                        <input id="email" name="email" type="email" placeholder="E-mail" class="form-control @error('email') is-invalid @enderror" value="{{ $gceu->email }}" maxlength="100">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4 col-md-3">
                        <label class="control-label" for="contato">* Contato</label>
                        <input id="contato" name="contato" type="text" class="form-control @error('contato') is-invalid @enderror" required placeholder="(00) 0000-0000" value="{{ $gceu->contato }}" maxlength="15">
                        @error('contato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">* Congregação</label>
                        <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" required>
                            <option value="" {{ old('congregacao_id') == '' ? 'selected' : '' }}>Selecione</option>
                            <option value="sede" {{ $gceu->congregacao_id == null ? 'selected' : '' }}>SEDE</option>
                            @foreach ($congregacoes as $congregacao)
                                <option value="{{ $congregacao->id }}" {{ $gceu->congregacao_id == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
                            @endforeach
                        </select>
                        @error('congregacao_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Endereço GCEU</h4>
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">* CEP</label>
                        <input id="cep" name="cep" type="text" maxlength="10" class="form-control @error('cep') is-invalid @enderror" placeholder="00000-000" value="{{ $gceu->cep }}" required>
                        @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-6 col-md-6">
                        <label class="control-label">Endereco</label>
                        <input id="endereco" name="endereco" type="text" class="form-control @error('rua') is-invalid @enderror" placeholder="Endereco" maxlength="120" value="{{ $gceu->endereco }}">
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-2 col-md-2">
                        <label class="control-label">Número</label>
                        <input id="numero" name="numero" type="text" maxlength="11" class="form-control @error('numero') is-invalid @enderror" placeholder="Nº" value="{{ $gceu->numero }}">
                        @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">Bairro</label>
                        <input id="bairro" name="bairro" type="text" maxlength="50" class="form-control @error('bairro') is-invalid @enderror" placeholder="Bairro" value="{{ $gceu->bairro }}">
                        @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">Cidade</label>
                        <input id="cidade" name="cidade" type="text" maxlength="50" class="form-control @error('cidade') is-invalid @enderror" placeholder="Cidade" value="{{ $gceu->cidade }}">
                        @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4 col-md-4">
                        <label class="control-label">Estado</label>
                        <input id="estado" name="estado" type="text" maxlength="30" class="form-control @error('estado') is-invalid @enderror" placeholder="Estado" value="{{ $gceu->uf }}">
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <input type="submit" value="Salvar" class="btn btn-primary btn-lg mt-3">
            </form>
            @else
            <div class="alert alert-warning" role="alert">
                CGEU não encontrado.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('visitantes/js/editar.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#contato').mask('(00) 00000-0000');
        $('#cep').mask('000000-00');
    });
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length != 8) {
            return;
        }
        $.getJSON('/api/cep/' + cep, function(data) {
            if (!("erro" in data)) {
                $('#endereco').val(data.logradouro);
                $('#bairro').val(data.bairro);
                $('#cidade').val(data.localidade);
                $('#estado').val(data.uf);
            } else {
                toastr.warning('CEP não encontrado.');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        Inputmask("999.999.999-99").mask(document.getElementById("cpf"));
        Inputmask("99999-999").mask(document.getElementById("cep"));
        Inputmask("(99) 99999-9999").mask(document.getElementById("contato"));
        Inputmask("(99) 99999-9999").mask(document.getElementById("telefone_alternativo"));
        Inputmask("9999 9999 9999").mask(document.getElementById("titulo_eleitor"));
        Inputmask("99.999.999-9").mask(document.getElementById("identidade"));
    });
</script>
@endsection