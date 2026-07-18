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

<div class="tab-pane fade" id="border-top-ministerio" role="tabpanel" aria-labelledby="border-top-ministerial">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro->funcoesMinisteriais)
                @foreach ($membro->funcoesMinisteriais as $funcaoMinisterial)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $funcaoMinisterial->ministerio ? $funcaoMinisterial->ministerio->descricao : 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">
                            {{ __('Ministério') }}
                        </span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $funcaoMinisterial->tipoAtuacao->descricao ?? 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">
                            {{ __('Função') }}
                        </span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $funcaoMinisterial->data_entrada ? \Carbon\Carbon::parse($funcaoMinisterial->data_entrada)->format('d/m/Y') : 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">
                            {{ __('Nomeação') }}
                        </span>
                    </p>
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $funcaoMinisterial->data_saida ? \Carbon\Carbon::parse($funcaoMinisterial->data_saida)->format('d/m/Y') : 'Sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">
                            {{ __('Exoneração') }}
                        </span>
                    </p>
                    @if ($funcaoMinisterial->observacoes)
                        <p class="card-text">
                            <span class="text-center d-block" style="font-weight: bold">
                                {{ $funcaoMinisterial->observacoes }}
                            </span>
                            <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">
                                {{ __('Observações') }}
                            </span>
                        </p>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
