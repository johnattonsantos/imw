<style>
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
        border-radius: 10px;
    }
</style>

<div class="tab-pane fade" id="border-top-historico" role="tabpanel" aria-labelledby="border-top-historico">
    <div class="card-body">
        @if ($membro->rolPermanente)
            @foreach ($membro->rolPermanente as $rol)
                <div class="card mb-3 mosaic">
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                        {{ $rol->status == 'A' ? ($rol->dt_recepcao ? \Carbon\Carbon::parse($rol->dt_recepcao)->format('d/m/Y') : 'Sem informações') : ($rol->dt_exclusao ? \Carbon\Carbon::parse($rol->dt_exclusao)->format('d/m/Y') : 'Sem informações') }}
                        </span>
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d" >{{ __('Data') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            @if ($rol->status == 'A')
                                Recebimento
                            @elseif ($rol->status == 'I')
                                Exclusão
                            @elseif ($rol->status == 'T')
                                Transferência
                            @else
                                Sem informações
                            @endif
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Ocorrência') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">{{ $rol->modoRecepcao->nome ?? 'Sem informações' }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Modo/Forma') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">{{ $rol->igreja->nome ?? 'Sem informações' }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Igreja') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">{{ $rol->congregacao->nome ?? 'Sem informações' }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Congregação') }}</span>
                    </p>
                </div>
            @endforeach
        @else
            <span class="card-text">{{ __('Sem informações') }}</span>
        @endif
    </div>
</div>
