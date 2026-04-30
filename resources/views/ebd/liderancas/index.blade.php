@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'EBD', 'url' => route('ebd.dashboard'), 'active' => false],
        ['text' => 'Liderança', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liderança EBD</h5>
                <a href="{{ route('ebd.liderancas.create') }}" class="btn btn-primary">Nova Liderança</a>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Membro</th>
                                <th>Cargo</th>
                                <th>Ativo</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($liderancas as $item)
                                <tr>
                                    <td>{{ $item->membro->nome ?? '-' }}</td>
                                    <td>{{ ucfirst($item->cargo) }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>{{ optional($item->data_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ optional($item->data_fim)->format('d/m/Y') }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.liderancas.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" action="{{ route('ebd.liderancas.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
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
                {{ $liderancas->links() }}
            </div>
        </div>
    </div>
@endsection
