@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Evento</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.agendas.update', $agenda->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" class="form-control" value="{{ $agenda->titulo }}" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3">{{ $agenda->descricao }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Data início</label>
                            <input type="datetime-local" name="data_inicio" class="form-control" value="{{ optional($agenda->data_inicio)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Data fim</label>
                            <input type="datetime-local" name="data_fim" class="form-control" value="{{ optional($agenda->data_fim)->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Turma (opcional)</label>
                        <select name="turma_id" class="form-control">
                            <option value="">Sem turma</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ (int)$agenda->turma_id === (int)$turma->id ? 'selected' : '' }}>{{ $turma->nome }} - {{ $turma->ano }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Local</label>
                        <input type="text" name="local" class="form-control" value="{{ $agenda->local }}">
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.agendas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
