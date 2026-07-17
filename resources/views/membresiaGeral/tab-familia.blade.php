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

<div class="tab-pane fade" id="border-top-familia" role="tabpanel" aria-labelledby="border-top-familiar">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro->familiar)
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->pai_nome ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Nome do Pai') }}</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->mae_nome ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Nome da Mãe') }}</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->conjuge_nome ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Nome do Cônjuge') }}</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->filhos ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Quantidade de Filhos') }}</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->histórico_familiar ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Histórico Familiar') }}</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">{{ $membro->familiar->data_Casamento ? \Carbon\Carbon::parse($membro->familiar->data_Casamento)->format('d/m/Y') : 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">{{ __('Data de Casamento') }}</span>
                </p>
            @endif
        </div>
    </div>
</div>
