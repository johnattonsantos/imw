<Style>
    .mosaic {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        padding: 20px;
        background-color: #f0f0f0;
    }

    .mosaic p {
        background-color: white;
        border: 1px solid #ccc;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        border-radius: 10px;
    }
</Style>

<div class="tab-pane fade" id="border-top-disciplina" role="tabpanel" aria-labelledby="border-top-disciplina">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro->disciplinas)
                @foreach ($membro->disciplinas as $disciplina)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $disciplina->dt_inicio->format('d/m/Y') }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Data de Início') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $disciplina->dt_termino ? $disciplina->dt_termino->format('d/m/Y') : 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Data de Término') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ optional($disciplina->igreja)->nome ?? 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Igreja') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ optional($disciplina->pastor)->nome ?? 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Pastor') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block"
                            style="font-weight: bold">{{ $membro->observacao ?? 'sem informações' }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Observações') }}</span>
                    </p>
                    @if ($disciplina->dt_termino == null)
                        <p class="card-text">
                            <button type="button" class="btn btn-warning"
                                onclick="encerrarDisciplina(event, {{ $disciplina->id }})">
                                <x-bx-block /> {{ __('Encerrar Disciplina') }}
                            </button>
                        </p>
                    @endif
                @endforeach
            @else
                <span class="card-text">{{ __('Sem informações') }}</span>
            @endif
        </div>
    </div>
</div>
