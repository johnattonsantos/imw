@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Liderança</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.liderancas.update', $lideranca->id) }}">
                    @csrf
                    @method('PUT')
                    @include('ebd.partials.member-picker', ['selectedMembro' => $lideranca->membro])
                    @include('ebd.partials.quick-visitante-modal')

                    <div class="form-group">
                        <label>Cargo</label>
                        <select name="cargo" class="form-control" required>
                            <option value="superintendente" {{ $lideranca->cargo === 'superintendente' ? 'selected' : '' }}>Superintendente</option>
                            <option value="secretario" {{ $lideranca->cargo === 'secretario' ? 'selected' : '' }}>Secretário</option>
                            <option value="tesoureiro" {{ $lideranca->cargo === 'tesoureiro' ? 'selected' : '' }}>Tesoureiro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1" {{ $lideranca->ativo ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$lideranca->ativo ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Data início</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ optional($lideranca->data_inicio)->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Data fim</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ optional($lideranca->data_fim)->format('Y-m-d') }}">
                        </div>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.liderancas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    @include('ebd.partials.member-picker-script', ['allowedVinculos' => ['M']])
@endsection
