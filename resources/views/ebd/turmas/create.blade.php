@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Nova EBD</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.turmas.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Classe</label>
                            <select name="classe_id" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>* Congregação</label>
                            <select name="congregacao_id" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="sede" {{ old('congregacao_id') === 'sede' ? 'selected' : '' }}>SEDE</option>
                                @foreach ($congregacoes as $congregacao)
                                    <option value="{{ $congregacao->id }}" {{ (string) old('congregacao_id') === (string) $congregacao->id ? 'selected' : '' }}>
                                        {{ $congregacao->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Ano</label>
                            <input type="number" name="ano" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Semestre</label>
                            <select name="semestre" class="form-control">
                                <option value="">-</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        Após criar a EBD, a inclusão de professor e alunos é feita nas telas de Professores e Alunos.
                    </div>

                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>

                    <button class="btn btn-success">Salvar</button>
                    <a href="{{ route('ebd.turmas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
