@extends('template.layout')

@section('extras-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @include('extras.alerts')

    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>EBD Professores da igreja: <u>{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <div class="alert alert-info">
                    <strong>Professor selecionado:</strong> {{ $professor->membro->nome ?? '-' }}
                </div>

                <form method="POST" action="{{ route('ebd.professores.vinculos.store', $professor->id) }}" class="mb-4">
                    @csrf
                    <div class="form-group row mb-4">
                        <div class="col-lg-2 text-right">
                            <label class="control-label">EBD:</label>
                        </div>
                        <div class="col-lg-6">
                            <select id="turma_id" name="turma_id" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}">
                                        {{ $turma->nome }} - {{ $turma->classe->nome ?? '-' }} ({{ $turma->ano }}{{ $turma->semestre ? '/'.$turma->semestre : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <button class="btn btn-primary" type="submit">
                                <x-bx-plus /> Adicionar Professor na EBD
                            </button>
                            <a href="{{ route('ebd.professores.index') }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                </form>

                <blockquote class="blockquote">
                    <h5>EBDs em que o professor está vinculado</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>EBD</th>
                                    <th>Classe</th>
                                    <th>Ano/Sem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ebdsDoProfessor as $turma)
                                    <tr>
                                        <td>{{ $turma->nome }}</td>
                                        <td>{{ $turma->classe->nome ?? '-' }}</td>
                                        <td>{{ $turma->ano }}{{ $turma->semestre ? '/'.$turma->semestre : '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Professor ainda não está vinculado em nenhuma EBD.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </blockquote>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/pt-BR.js"></script>
    <script>
        $.fn.select2.defaults.set('language', 'pt-BR');
        $('#turma_id').select2({
            width: '100%',
            placeholder: 'Selecione',
            allowClear: true
        });
    </script>
@endsection
