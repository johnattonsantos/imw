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

<div class="tab-pane fade" id="border-top-formacao" role="tabpanel" aria-labelledby="border-top-formacao">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro->formacoesEclesiasticas)
            @foreach ($membro->formacoesEclesiasticas as $formacao)
            <p class="card-text">
                <span class="text-center d-block" style="font-weight: bold">{{ $formacao->curso->nome ?? 'Sem informações' }}</span>
                <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Curso') }}</span>
            </p>
            <p class="card-text">
                <span class="text-center d-block" style="font-weight: bold">{{ $formacao->inicio ? \Carbon\Carbon::parse($formacao->inicio)->format('d/m/Y') : 'Sem informações' }}</span>
                <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Data de Início') }}</span>
            </p>


            <p class="card-text">
                <span class="text-center d-block" style="font-weight: bold">{{ $formacao->termino ? \Carbon\Carbon::parse($formacao->termino)->format('d/m/Y') : 'Sem informações' }}</span>
                <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Data de Conclusão') }}</span>
            </p>
            @if ($formacao->observacao)
            <p class="card-text">
                <span class="text-center d-block" style="font-weight: bold">{{ $formacao->observacao }}</span>
                <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Observações') }}</span>
            </p>
            @endif
            @endforeach
            @else
            <span class="card-text">{{ __('Sem informações') }}</span>
            @endif
        </div>
    </div>
</div>