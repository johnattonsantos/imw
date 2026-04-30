@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Novo Evento</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.agendas.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Data início</label>
                            <input type="datetime-local" name="data_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Data fim</label>
                            <input type="datetime-local" name="data_fim" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Turma (opcional)</label>
                        <select name="turma_id" class="form-control">
                            <option value="">Sem turma</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}">{{ $turma->nome }} - {{ $turma->ano }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Local</label>
                        <input type="text" name="local" class="form-control">
                    </div>

                    <button class="btn btn-success">Salvar</button>
                    <a href="{{ route('ebd.agendas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
