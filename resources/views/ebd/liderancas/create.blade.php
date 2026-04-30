@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Nova Liderança</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.liderancas.store') }}">
                    @csrf
                    @include('ebd.partials.member-picker')
                    @include('ebd.partials.quick-visitante-modal')

                    <div class="form-group">
                        <label>Cargo</label>
                        <select name="cargo" class="form-control" required>
                            <option value="superintendente">Superintendente</option>
                            <option value="secretario">Secretário</option>
                            <option value="tesoureiro">Tesoureiro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Data início</label>
                            <input type="date" name="data_inicio" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Data fim</label>
                            <input type="date" name="data_fim" class="form-control">
                        </div>
                    </div>

                    <button class="btn btn-success">Salvar</button>
                    <a href="{{ route('ebd.liderancas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    @include('ebd.partials.member-picker-script', ['allowedVinculos' => ['M']])
@endsection
