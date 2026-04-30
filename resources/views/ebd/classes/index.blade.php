@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Classes EBD</h5>
                <a href="{{ route('ebd.classes.create') }}" class="btn btn-primary">Nova Classe</a>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Faixa etária</th>
                                <th>Ativo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classes as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->faixa_etaria ?? '-' }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.classes.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" action="{{ route('ebd.classes.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Nenhum registro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $classes->links() }}
            </div>
        </div>
    </div>
@endsection
