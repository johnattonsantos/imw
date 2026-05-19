@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar EBD</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.turmas.update', $turma->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Classe</label>
                            <select name="classe_id" class="form-control" required>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ (int)$classe->id === (int)$turma->classe_id ? 'selected' : '' }}>{{ $classe->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Professor</label>
                            <select name="professor_id" class="form-control">
                                <option value="">Sem professor</option>
                                @foreach ($professores as $prof)
                                    <option value="{{ $prof->id }}" {{ (int)$prof->id === (int)$turma->professor_id ? 'selected' : '' }}>{{ $prof->membro->nome ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>* Congregação</label>
                            @php($congregacaoSelecionada = old('congregacao_id', $turma->congregacao_id ? (string) $turma->congregacao_id : 'sede'))
                            <select name="congregacao_id" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="sede" {{ $congregacaoSelecionada === 'sede' ? 'selected' : '' }}>SEDE</option>
                                @foreach ($congregacoes as $congregacao)
                                    <option value="{{ $congregacao->id }}" {{ (string) $congregacaoSelecionada === (string) $congregacao->id ? 'selected' : '' }}>
                                        {{ $congregacao->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" value="{{ $turma->nome }}" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Ano</label>
                            <input type="number" name="ano" class="form-control" value="{{ $turma->ano }}" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Semestre</label>
                            <select name="semestre" class="form-control">
                                <option value="" {{ !$turma->semestre ? 'selected' : '' }}>-</option>
                                <option value="1" {{ (int)$turma->semestre === 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ (int)$turma->semestre === 2 ? 'selected' : '' }}>2</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        Para incluir ou remover alunos desta EBD, use o menu <strong>Alunos</strong> e clique em vincular EBD.
                    </div>

                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1" {{ $turma->ativo ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$turma->ativo ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.turmas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
