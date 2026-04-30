@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Turmas EBD</h5>
                <a href="{{ route('ebd.turmas.create') }}" class="btn btn-primary">Nova Turma</a>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Classe</th>
                                <th>Professor</th>
                                <th>Ano/Sem</th>
                                <th>Alunos ativos</th>
                                <th>Ativo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($turmas as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->classe->nome ?? '-' }}</td>
                                    <td>{{ $item->professor->membro->nome ?? '-' }}</td>
                                    <td>{{ $item->ano }}{{ $item->semestre ? '/'.$item->semestre : '' }}</td>
                                    <td>{{ $item->total_alunos_ativos }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.turmas.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" action="{{ route('ebd.turmas.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Nenhum registro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $turmas->links() }}
            </div>
        </div>
    </div>
@endsection
