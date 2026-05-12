@extends('template.layout')

@section('content')
    @include('extras.alerts')
    <div class="container-fluid d-flex justify-content-between">
        <div>
            <a href="{{ route('ebd.alunos.create') }}" class="btn btn-primary position-relative mt-3 mb-3 ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-plus-circle">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span class="ml-2">NOVO ALUNO</span>
            </a>
        </div>
    </div>

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Lista de Alunos EBD</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-4">
                        <thead>
                            <tr>
                                <th>Membro</th>
                                <th>Sexo</th>
                                <th>Telefone</th>
                                <th>Ativo</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alunos as $item)
                                @php
                                    $sexo = strtoupper((string) ($item->membro->sexo ?? ''));
                                    $sexoTexto = match ($sexo) {
                                        'M' => 'Masculino',
                                        'F' => 'Feminino',
                                        'N' => 'Não informado',
                                        default => ($item->membro->sexo ?? '-'),
                                    };

                                    $telefone = $item->membro->contato->telefone_preferencial
                                        ?? $item->membro->contato->telefone_whatsapp
                                        ?? $item->membro->contato->telefone_alternativo
                                        ?? null;
                                    $telefoneFormatado = $telefone ? formatStr($telefone, '(##) #####-####') : '-';
                                @endphp
                                <tr>
                                    <td>{{ $item->membro->nome ?? '-' }}</td>
                                    <td>{{ $sexoTexto }}</td>
                                    <td>{{ $telefoneFormatado }}</td>
                                    <td>{{ $item->ativo ? 'Sim' : 'Não' }}</td>
                                    <td>{{ $item->observacoes }}</td>
                                    <td class="d-flex" style="gap: .5rem;">
                                        <a href="{{ route('ebd.alunos.edit', $item->id) }}" class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip" title="Editar" aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('ebd.alunos.destroy', $item->id) }}" onsubmit="return confirm('Remover registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger btn-rounded bs-tooltip" title="Excluir" aria-label="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Nenhum registro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $alunos->links() }}
            </div>
        </div>
    </div>
@endsection
