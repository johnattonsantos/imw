@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Classe</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.classes.update', $classe->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="nome" value="{{ $classe->nome }}" required>
                    </div>
                    <div class="form-group">
                        <label>Faixa etária</label>
                        <input type="text" class="form-control" name="faixa_etaria" value="{{ $classe->faixa_etaria }}">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3">{{ $classe->descricao }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1" {{ $classe->ativo ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$classe->ativo ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.classes.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
