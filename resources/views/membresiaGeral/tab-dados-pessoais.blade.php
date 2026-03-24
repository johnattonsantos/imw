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

<div class=" card tab-pane fade show active" id="border-top-dados-pessoal" role="tabpanel"
    aria-labelledby="border-top-dados-pessoais">
    <div class="card-body">
        <div class="card mb-3 mosaic">
            @if ($membro)
                <p type='date' class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ old('data_nascimento', $membro->data_nascimento ? \Carbon\Carbon::parse($membro->data_nascimento)->format('d/m/Y') : null) ?? 'Sem informações' }}

                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Data de Nascimento</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->cpf ? formatStr($membro->cpf, '###.###.###-##') : 'Sem informações' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">CPF</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->sexo === 'F' ? 'feminino' : ($membro->sexo === 'M' ? 'masculino' : 'outro') ?? 'Sem informações' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Sexo</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->estado_civil === 'S' ? 'Solteiro' : 'Casado' ?? 'Sem informações' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Estado Civil</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block" style="font-weight: bold">
                        {{ $membro->nacionalidade ?? 'Sem informações' }}
                    </span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Nacionalidade</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->naturalidade ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Naturalidade</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->uf ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">UF</span>
                </p>
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->escolaridade ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Escolaridade</span>
                </p>
                @if ($membro->profissao)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $membro->profissao }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Profissão</span>
                    </p>
                @endif
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->rg ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">RG</span>
                </p>
                @if ($membro->Funcao_Eclesiastica)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $membro->Funcao_Eclesiastica }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Função
                            Eclesiástica</span>
                    </p>
                @endif
                @if ($membro->cnh)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">{{ $membro->cnh }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">CNH</span>
                    </p>
                @endif
                @if ($membro->documento)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $membro->documento }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Documento</span>
                    </p>
                @endif
                @if ($membro->documento_complemento)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $membro->documento_complemento }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Documento
                            Complemento</span>
                    </p>
                @endif
                @if ($membro->data_conversao)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">
                            {{ $membro->data_conversao ? \Carbon\Carbon::parse($membro->data_conversao)->format('d/m/Y') : 'sem informações' }}
                        </span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Data de
                            Conversão</span>
                    </p>
                @endif
                @if ($membro->data_batismo)
                    <p class="card-text">
                        <span class="text-center d-block"
                            style="font-weight: bold">{{ $membro->data_batismo ? \Carbon\Carbon::parse($membro->data_batismo)->format('d/m/Y') : 'sem informações' }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Data de
                            Batismo</span>
                    </p>
                @endif
                @if ($membro->data_batismo_espirito)
                    <p class="card-text">
                        <span class="text-center d-block"
                            style="font-weight: bold">{{ $membro->data_batismo_espirito }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Data de Batismo
                            Espirito Santo</span>
                    </p>
                @endif
                @if ($membro->historico)
                    <p class="card-text">
                        <span class="text-center d-block" style="font-weight: bold">{{ $membro->historico }}</span>
                        <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Histórico</span>
                    </p>
                @endif
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->congregacao->nome ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">Congregação</span>
                </p>
                @if ($membro->vinculo == 'V')
                <p class="card-text">
                    <span class="text-center d-block"
                        style="font-weight: bold">{{ $membro->gceu->nome ?? 'Sem informações' }}</span>
                    <span class="text-center d-block" style="font-size: .8rem; color: #6c757d">GCEU</span>
                </p>
                @endif
            @else
                <span class="card-text">Sem informações</span>
            @endif
        </div>
    </div>
</div>
