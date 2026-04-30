@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Agenda EBD</h5>
                <a href="{{ route('ebd.agendas.create') }}" class="btn btn-primary">Novo Evento</a>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Turma</th>
                                <th>Local</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agendas as $item)
                                <tr>
                                    <td>{{ $item->titulo }}</td>
                                    <td>{{ optional($item->data_inicio)->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($item->data_fim)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item->turma->nome ?? '-' }}</td>
                                    <td>{{ $item->local ?? '-' }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.agendas.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" action="{{ route('ebd.agendas.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
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
                {{ $agendas->links() }}
            </div>
        </div>
    </div>
@endsection
