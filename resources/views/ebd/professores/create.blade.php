@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Novo Professor</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.professores.store') }}">
                    @csrf
                    @include('ebd.partials.member-picker')
                    @include('ebd.partials.quick-visitante-modal')

                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3"></textarea>
                    </div>

                    <button class="btn btn-success">Salvar</button>
                    <a href="{{ route('ebd.professores.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    @include('ebd.partials.member-picker-script', ['allowedVinculos' => ['M']])
@endsection
