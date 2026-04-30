@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Diário de Classe</h5>
                <a href="{{ route('ebd.diarios.create') }}" class="btn btn-primary">Novo Diário</a>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Turno</th>
                                <th>Turma</th>
                                <th>Tema</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($diarios as $item)
                                <tr>
                                    <td>{{ optional($item->data_aula)->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($item->hora_inicio || $item->hora_fim)
                                            {{ $item->hora_inicio ? substr($item->hora_inicio, 0, 5) : '--:--' }}
                                            -
                                            {{ $item->hora_fim ? substr($item->hora_fim, 0, 5) : '--:--' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->periodo_aula === 'manha')
                                            Manhã
                                        @elseif ($item->periodo_aula === 'noite')
                                            Noite
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $item->turma->nome ?? '-' }}</td>
                                    <td>{{ $item->tema_aula }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.diarios.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" action="{{ route('ebd.diarios.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Nenhum registro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $diarios->links() }}
            </div>
        </div>
    </div>
@endsection
