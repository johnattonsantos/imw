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

<div class="tab-pane fade" id="border-top-contato" role="tabpanel" aria-labelledby="border-top-contatos">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro->contato)
                @php
                    $telefoneContato = $membro->contato->telefone_preferencial
                        ?? $membro->contato->telefone_alternativo
                        ?? $membro->contato->telefone_whatsapp
                        ?? null;
                @endphp
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->contato->email_preferencial ?? 'Sem informação de e-mail' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">E-mail</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $telefoneContato ?? 'Sem informação de telefone' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Telefone</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->contato->cep ?? 'Sem informação de CEP' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">CEP</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->contato->endereco ?? 'Sem informação de endereço' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Endereço</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->contato->numero ?? 'Sem informação de número' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Número</span>
                </p>

                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->contato->complemento ?? 'Não informado' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Complemento </span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->contato->bairro ?? 'Não informado' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Bairro</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->contato->cidade ?? 'Não informado' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Cidade</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->contato->estado ?? 'Não informado' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">UF</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->contato->observacoes ?? 'Não informado' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Observações</span>
                </p>
            @else
                <span class="card-text">Sem informações</span>
            @endif
        </div>
    </div>
</div>
