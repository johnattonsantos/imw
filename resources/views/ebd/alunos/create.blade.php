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
                        <h4>EBD Alunos da igreja: <u>{{ $instituicao }}</u></h4>
                    </div>
                </div>
            </div>

            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.alunos.store') }}">
                    @csrf

                    <div class="form-group row mb-4">
                        <div class="col-lg-2 text-right">
                            <label class="control-label">Membros:</label>
                        </div>
                        <div class="col-lg-6">
                            <select id="membro_id" name="membro_id" class="form-control @error('membro_id') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                @foreach ($membros as $membro)
                                    <option value="{{ $membro->id }}" {{ old('membro_id') == $membro->id ? 'selected' : '' }}>
                                        {{ $membro->nome }} ({{ $membro->vinculo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('membro_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <button class="btn btn-primary" type="submit">
                                <x-bx-plus /> Adicionar Aluno na EBD
                            </button>
                            <a href="{{ route('ebd.alunos.index') }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>

                    <input type="hidden" name="ativo" value="1">
                    <input type="hidden" name="observacoes" value="{{ old('observacoes', '') }}">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/pt-BR.js"></script>
    <script>
        $.fn.select2.defaults.set('language', 'pt-BR');
        $('#membro_id').select2({
            width: '100%',
            placeholder: 'Selecione',
            allowClear: true
        });
    </script>
@endsection
