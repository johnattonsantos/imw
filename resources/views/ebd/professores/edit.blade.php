@extends('template.layout')

@section('content')
    <div class="col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header p-3"><h5 class="mb-0">Editar Professor</h5></div>
            <div class="widget-content widget-content-area">
                <form method="POST" action="{{ route('ebd.professores.update', $professor->id) }}">
                    @csrf
                    @method('PUT')
                    @include('ebd.partials.member-picker', ['selectedMembro' => $professor->membro])
                    @include('ebd.partials.quick-visitante-modal')

                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="ativo" class="form-control" required>
                            <option value="1" {{ $professor->ativo ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$professor->ativo ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3">{{ $professor->observacoes }}</textarea>
                    </div>

                    <button class="btn btn-success">Atualizar</button>
                    <a href="{{ route('ebd.professores.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extras-scripts')
    @include('ebd.partials.member-picker-script', ['allowedVinculos' => ['M']])
@endsection
