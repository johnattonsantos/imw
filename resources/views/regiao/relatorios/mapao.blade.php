@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Relatórios Regionais', 'url' => '#', 'active' => false],
    ['text' => 'Mapão', 'url' => route('regiao.relatorio.mapao'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<style>
    .mapao-header {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
    }

    .mapao-region {
        color: #6d7893;
        font-size: 13px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .mapao-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .mapao-period {
        background: #f8faff;
        border: 1px solid #e4e9f2;
        border-radius: 12px;
        color: #59657d;
        margin-bottom: 18px;
        padding: 16px;
    }

    .mapao-period strong {
        color: #26304d;
    }

    .mapao-card {
        background: linear-gradient(145deg, #ffffff 0%, #f7f9ff 100%);
        border: 1px solid #e2e8f3;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(31, 45, 61, .07);
        min-height: 138px;
        overflow: hidden;
        padding: 22px;
        position: relative;
    }

    .mapao-card.has-detail {
        padding-bottom: 58px;
    }

    .mapao-card::before {
        background: linear-gradient(180deg, #4361ee, #20a4f3);
        border-radius: 999px;
        content: '';
        height: 52px;
        opacity: .13;
        position: absolute;
        right: -14px;
        top: -16px;
        width: 52px;
    }

    .mapao-card-title {
        color: #7a839a;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.35;
        margin-bottom: 18px;
        min-height: 36px;
        text-transform: uppercase;
    }

    .mapao-card-value {
        color: #3046d3;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1;
    }

    .mapao-card-summary {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin: 0 0 14px;
    }

    .mapao-card-summary-item {
        background: rgba(237, 244, 255, .72);
        border: 1px solid #dbe8fb;
        border-radius: 10px;
        padding: 8px 10px;
    }

    .mapao-card-summary-label {
        color: #7a839a;
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        line-height: 1.2;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .mapao-card-summary-value {
        color: #26304d;
        display: block;
        font-size: 16px;
        font-weight: 800;
        line-height: 1;
    }

    .mapao-card-value-label {
        color: #8b95aa;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        margin-top: 6px;
        text-transform: uppercase;
    }

    .mapao-detail-button {
        align-items: center;
        background: #edf4ff;
        border: 1px solid #d6e7ff;
        border-radius: 999px;
        bottom: 16px;
        color: #2364d2;
        display: inline-flex;
        font-size: 12px;
        font-weight: 700;
        gap: 6px;
        padding: 8px 12px;
        position: absolute;
        right: 18px;
        transition: .2s ease;
    }

    .mapao-detail-button:hover {
        background: #2364d2;
        color: #fff;
        text-decoration: none;
    }

    .mapao-modal-search {
        background: #f8faff;
        border: 1px solid #e4e9f2;
        border-radius: 12px;
        margin-bottom: 18px;
        padding: 14px;
    }

    .mapao-detail-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        max-height: 58vh;
        overflow-y: auto;
        padding-right: 4px;
    }

    .mapao-gceu-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #e1e8f5;
        border-radius: 14px;
        box-shadow: 0 8px 18px rgba(31, 45, 61, .06);
        padding: 16px;
    }

    .mapao-gceu-name {
        color: #25304d;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 8px;
    }

    .mapao-gceu-context {
        color: #7a839a;
        font-size: 12px;
        line-height: 1.45;
        margin-bottom: 14px;
    }

    .mapao-gceu-total {
        color: #3046d3;
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
    }

    .mapao-gceu-total-label {
        color: #8b95aa;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        margin-top: 4px;
        text-transform: uppercase;
    }

    .mapao-empty-detail {
        border: 1px dashed #cfd8e8;
        border-radius: 12px;
        color: #7a839a;
        display: none;
        padding: 18px;
        text-align: center;
    }

    .mapao-note {
        background: #f7f9fc;
        border: 1px solid #e4e9f2;
        border-radius: 12px;
        color: #59657d;
        font-size: 13px;
        line-height: 1.55;
        padding: 14px 16px;
    }
</style>
@endsection

@include('extras.alerts')

@section('content')
@php
    $formatarValor = function ($card) {
        if ($card['tipo'] === 'moeda') {
            return 'R$ ' . number_format((float) $card['valor'], 2, ',', '.');
        }

        if ($card['tipo'] === 'decimal') {
            return number_format((float) $card['valor'], 1, ',', '.');
        }

        return number_format((float) $card['valor'], 0, ',', '.');
    };

    $distritosMinisterios = collect($detalhesMinisterios)
        ->whereNotNull('distrito_id')
        ->unique('distrito_id')
        ->sortBy('distrito_nome')
        ->values();

    $igrejasMinisterios = collect($detalhesMinisterios)
        ->whereNotNull('igreja_id')
        ->unique('igreja_id')
        ->sortBy('igreja_nome')
        ->values();

    $tiposMinisteriosTraduzidos = collect($tiposMinisterios)
        ->map(fn ($tipo) => [
            'id' => $tipo['id'],
            'nome' => __($tipo['nome']),
        ])
        ->values();
@endphp

<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-12">
                    <div class="mapao-header px-3 pt-3">
                        <div>
                            <h4 class="mb-1">{{ __('Mapão') }}</h4>
                            <div class="mapao-region">{{ __('Região') }}: {{ $regiao->nome }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-content widget-content-area">
            <div class="mapao-period">
                <strong>{{ __($periodos['descricao']) }}:</strong>
                {{ $periodos['data_inicial']->format('d/m/Y') }}
                {{ __('até') }}
                {{ $periodos['data_final']->format('d/m/Y') }}.
                {{ __('As informações são calculadas automaticamente, sem filtro manual de data.') }}
            </div>

            <div class="mapao-grid">
                @foreach ($cards as $card)
                    @continue($card['titulo'] === 'Média da arrecadação mensal')
                    @continue($card['titulo'] === 'Total de integrantes nos ministérios')
                    @php
                        $cardTemDetalheGceu = $card['titulo'] === 'Total de integrantes de GCEUs';
                        $cardTemDetalheMinisterio = $card['titulo'] === 'Total de ministérios';
                        $cardDetalhe = $card['detalhe'] ?? null;
                        $cardTemResumo = !empty($card['resumo']);
                        $cardTemDetalheRecebimentos = $cardDetalhe === 'recebimentos';
                        $cardTemDetalheExclusoes = $cardDetalhe === 'exclusoes';
                    @endphp
                    <div class="mapao-card {{ ($cardTemDetalheGceu || $cardTemDetalheMinisterio || $cardTemDetalheRecebimentos || $cardTemDetalheExclusoes) ? 'has-detail' : '' }}">
                        <div class="mapao-card-title">{{ __($card['titulo']) }}</div>
                        @if ($cardTemResumo)
                            <div class="mapao-card-summary">
                                @foreach ($card['resumo'] as $itemResumo)
                                    <div class="mapao-card-summary-item">
                                        <span class="mapao-card-summary-label">{{ __($itemResumo['titulo']) }}</span>
                                        <span class="mapao-card-summary-value">{{ number_format((float) $itemResumo['valor'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="mapao-card-value">{{ $formatarValor($card) }}</div>
                        @if ($cardTemResumo)
                            <div class="mapao-card-value-label">{{ __('Média') }}</div>
                        @endif
                        @if ($cardTemDetalheGceu)
                            <button type="button" class="mapao-detail-button" data-toggle="modal" data-target="#modalDetalheIntegrantesGceus">
                                <i class="fas fa-eye"></i>
                                {{ __('Ver detalhe') }}
                            </button>
                        @endif
                        @if ($cardTemDetalheMinisterio)
                            <button type="button" class="mapao-detail-button" data-toggle="modal" data-target="#modalDetalheMinisterios">
                                <i class="fas fa-eye"></i>
                                {{ __('Ver detalhe') }}
                            </button>
                        @endif
                        @if ($cardTemDetalheRecebimentos)
                            <button type="button" class="mapao-detail-button" data-toggle="modal" data-target="#modalDetalheRecebimentos">
                                <i class="fas fa-eye"></i>
                                {{ __('Ver detalhe') }}
                            </button>
                        @endif
                        @if ($cardTemDetalheExclusoes)
                            <button type="button" class="mapao-detail-button" data-toggle="modal" data-target="#modalDetalheExclusoes">
                                <i class="fas fa-eye"></i>
                                {{ __('Ver detalhe') }}
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mapao-note mt-4">
                <strong>{{ __('Critério das médias') }}:</strong>
                {{ __('As médias de recebimentos e exclusões consideram o mesmo intervalo dividido por') }}
                {{ $periodos['trimestres_periodo'] }} {{ __('trimestre(s) do período.') }}
                {{ __('O total que tinha considera os membros existentes no dia anterior ao início do biênio.') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalheRecebimentos" tabindex="-1" role="dialog" aria-labelledby="modalDetalheRecebimentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalheRecebimentosLabel">{{ __('Igrejas com recebimento de membros') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mapao-detail-grid">
                    @forelse ($detalhesRecebimentos as $detalhe)
                        <div class="mapao-gceu-card">
                            <div class="mapao-gceu-name">{{ $detalhe->igreja_nome ?? __('Igreja não informada') }}</div>
                            <div class="mapao-gceu-context">{{ $detalhe->distrito_nome ?? __('Distrito não informado') }}</div>
                            <div class="mapao-gceu-total">{{ number_format((int) $detalhe->total, 0, ',', '.') }}</div>
                            <div class="mapao-gceu-total-label">{{ __('Entraram') }}</div>
                        </div>
                    @empty
                        <div class="mapao-empty-detail d-block">
                            {{ __('Nenhuma igreja com recebimento de membros no período.') }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fechar') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalheExclusoes" tabindex="-1" role="dialog" aria-labelledby="modalDetalheExclusoesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalheExclusoesLabel">{{ __('Igrejas com exclusão de membros') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mapao-detail-grid">
                    @forelse ($detalhesExclusoes as $detalhe)
                        <div class="mapao-gceu-card">
                            <div class="mapao-gceu-name">{{ $detalhe->igreja_nome ?? __('Igreja não informada') }}</div>
                            <div class="mapao-gceu-context">{{ $detalhe->distrito_nome ?? __('Distrito não informado') }}</div>
                            <div class="mapao-gceu-total">{{ number_format((int) $detalhe->total, 0, ',', '.') }}</div>
                            <div class="mapao-gceu-total-label">{{ __('Saíram') }}</div>
                        </div>
                    @empty
                        <div class="mapao-empty-detail d-block">
                            {{ __('Nenhuma igreja com exclusão de membros no período.') }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fechar') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalheMinisterios" tabindex="-1" role="dialog" aria-labelledby="modalDetalheMinisteriosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalheMinisteriosLabel">{{ __('Detalhe de membros por ministério') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mapao-modal-search">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="filtroDistritoMinisterio" class="mb-2">{{ __('Distrito') }}</label>
                            <select id="filtroDistritoMinisterio" class="form-control">
                                <option value="">{{ __('Todos') }}</option>
                                @foreach ($distritosMinisterios as $distrito)
                                    <option value="{{ $distrito['distrito_id'] }}">{{ $distrito['distrito_nome'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <label for="filtroIgrejaMinisterio" class="mb-2">{{ __('Igreja') }}</label>
                            <select id="filtroIgrejaMinisterio" class="form-control">
                                <option value="">{{ __('Todas') }}</option>
                                @foreach ($igrejasMinisterios as $igreja)
                                    <option value="{{ $igreja['igreja_id'] }}" data-distrito-id="{{ $igreja['distrito_id'] }}">{{ $igreja['igreja_nome'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-12 mb-3">
                            <label for="filtroTipoMinisterio" class="mb-2">{{ __('Tipo de Ministério') }}</label>
                            <select id="filtroTipoMinisterio" class="form-control">
                                <option value="">{{ __('Todos') }}</option>
                                @foreach ($tiposMinisteriosTraduzidos as $tipo)
                                    <option value="{{ $tipo['id'] }}">{{ $tipo['nome'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mapao-detail-grid" id="listaDetalheMinisterios"></div>

                <div class="mapao-empty-detail" id="mensagemDetalheMinisterioVazio">
                    {{ __('Nenhum ministério encontrado para os filtros informados.') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fechar') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalheIntegrantesGceus" tabindex="-1" role="dialog" aria-labelledby="modalDetalheIntegrantesGceusLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalheIntegrantesGceusLabel">{{ __('Detalhe de integrantes por GCEU') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Fechar') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mapao-modal-search">
                    <label for="buscaDetalheGceu" class="mb-2">{{ __('Pesquisar por nome do GCEU') }}</label>
                    <input type="text" id="buscaDetalheGceu" class="form-control" placeholder="{{ __('Digite o nome do GCEU') }}">
                </div>

                <div class="mapao-detail-grid" id="listaDetalheGceus">
                    @forelse ($detalhesIntegrantesGceus as $gceu)
                        <div class="mapao-gceu-card" data-gceu-card data-gceu-nome="{{ $gceu->nome }}">
                            <div class="mapao-gceu-name">{{ $gceu->nome }}</div>
                            <div class="mapao-gceu-context">
                                {{ $gceu->distrito_nome ?? __('Distrito não informado') }}
                                <br>
                                {{ $gceu->igreja_nome ?? __('Igreja não informada') }}
                            </div>
                            <div class="mapao-gceu-total">{{ number_format((int) $gceu->total_integrantes, 0, ',', '.') }}</div>
                            <div class="mapao-gceu-total-label">{{ __('Integrantes') }}</div>
                        </div>
                    @empty
                        <div class="mapao-empty-detail d-block">
                            {{ __('Nenhum GCEU ativo encontrado para a região.') }}
                        </div>
                    @endforelse
                </div>

                <div class="mapao-empty-detail" id="mensagemDetalheGceuVazio">
                    {{ __('Nenhum GCEU encontrado para a busca informada.') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Fechar') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const busca = document.getElementById('buscaDetalheGceu');
        const cards = Array.from(document.querySelectorAll('[data-gceu-card]'));
        const vazio = document.getElementById('mensagemDetalheGceuVazio');
        const detalhesMinisterios = @json($detalhesMinisterios);
        const tiposMinisterios = @json($tiposMinisteriosTraduzidos);
        const filtroDistritoMinisterio = document.getElementById('filtroDistritoMinisterio');
        const filtroIgrejaMinisterio = document.getElementById('filtroIgrejaMinisterio');
        const filtroTipoMinisterio = document.getElementById('filtroTipoMinisterio');
        const listaDetalheMinisterios = document.getElementById('listaDetalheMinisterios');
        const mensagemDetalheMinisterioVazio = document.getElementById('mensagemDetalheMinisterioVazio');
        const t = function (texto) {
            return typeof window.__ === 'function' ? window.__(texto) : texto;
        };

        if (!busca || !vazio) {
            return;
        }

        busca.addEventListener('input', function () {
            const termo = busca.value.trim().toLowerCase();
            let visiveis = 0;

            cards.forEach(function (card) {
                const nome = (card.dataset.gceuNome || '').toLowerCase();
                const mostrar = nome.includes(termo);
                card.style.display = mostrar ? '' : 'none';

                if (mostrar) {
                    visiveis += 1;
                }
            });

            vazio.style.display = visiveis === 0 && cards.length > 0 ? 'block' : 'none';
        });

        $('#modalDetalheIntegrantesGceus').on('shown.bs.modal', function () {
            busca.focus();
        });

        $('#modalDetalheIntegrantesGceus').on('hidden.bs.modal', function () {
            busca.value = '';
            busca.dispatchEvent(new Event('input'));
        });

        function normalizarValor(valor) {
            return valor === null || typeof valor === 'undefined' ? '' : String(valor);
        }

        function igrejasFiltradasPorDistrito(distritoId) {
            return detalhesMinisterios
                .filter(function (item) {
                    return !distritoId || normalizarValor(item.distrito_id) === distritoId;
                })
                .reduce(function (igrejas, item) {
                    const igrejaId = normalizarValor(item.igreja_id);

                    if (igrejaId && !igrejas.some(function (igreja) { return igreja.id === igrejaId; })) {
                        igrejas.push({
                            id: igrejaId,
                            nome: item.igreja_nome || t('Igreja não informada')
                        });
                    }

                    return igrejas;
                }, [])
                .sort(function (a, b) {
                    return a.nome.localeCompare(b.nome);
                });
        }

        function atualizarIgrejasMinisterio() {
            if (!filtroDistritoMinisterio || !filtroIgrejaMinisterio) {
                return;
            }

            const distritoId = filtroDistritoMinisterio.value;
            const igrejaAtual = filtroIgrejaMinisterio.value;
            const igrejas = igrejasFiltradasPorDistrito(distritoId);

            filtroIgrejaMinisterio.innerHTML = '<option value="">' + t('Todas') + '</option>';

            igrejas.forEach(function (igreja) {
                const option = document.createElement('option');
                option.value = igreja.id;
                option.textContent = igreja.nome;
                filtroIgrejaMinisterio.appendChild(option);
            });

            if (igrejas.some(function (igreja) { return igreja.id === igrejaAtual; })) {
                filtroIgrejaMinisterio.value = igrejaAtual;
            }
        }

        function renderizarMinisterios() {
            if (!listaDetalheMinisterios || !mensagemDetalheMinisterioVazio) {
                return;
            }

            const distritoId = normalizarValor(filtroDistritoMinisterio ? filtroDistritoMinisterio.value : '');
            const igrejaId = normalizarValor(filtroIgrejaMinisterio ? filtroIgrejaMinisterio.value : '');
            const tipoId = normalizarValor(filtroTipoMinisterio ? filtroTipoMinisterio.value : '');
            const tiposParaExibir = tipoId
                ? tiposMinisterios.filter(function (tipo) { return tipo.id === tipoId; })
                : tiposMinisterios;

            const totaisPorTipo = tiposParaExibir.reduce(function (acc, tipo) {
                acc[tipo.id] = {
                    nome: tipo.nome,
                    total: 0
                };
                return acc;
            }, {});

            detalhesMinisterios.forEach(function (item) {
                if (distritoId && normalizarValor(item.distrito_id) !== distritoId) {
                    return;
                }

                if (igrejaId && normalizarValor(item.igreja_id) !== igrejaId) {
                    return;
                }

                if (tipoId && item.tipo !== tipoId) {
                    return;
                }

                if (!totaisPorTipo[item.tipo]) {
                    return;
                }

                totaisPorTipo[item.tipo].total += Number(item.total || 0);
            });

            const cardsMinisterios = Object.values(totaisPorTipo);
            listaDetalheMinisterios.innerHTML = '';

            cardsMinisterios.forEach(function (ministerio) {
                const card = document.createElement('div');
                card.className = 'mapao-gceu-card';
                card.innerHTML =
                    '<div class="mapao-gceu-name">' + ministerio.nome + '</div>' +
                    '<div class="mapao-gceu-context">' + t('Total conforme os filtros selecionados') + '</div>' +
                    '<div class="mapao-gceu-total">' + new Intl.NumberFormat('pt-BR').format(ministerio.total) + '</div>' +
                    '<div class="mapao-gceu-total-label">' + t('Membros') + '</div>';

                listaDetalheMinisterios.appendChild(card);
            });

            mensagemDetalheMinisterioVazio.style.display = cardsMinisterios.length === 0 ? 'block' : 'none';
        }

        if (filtroDistritoMinisterio && filtroIgrejaMinisterio && filtroTipoMinisterio) {
            filtroDistritoMinisterio.addEventListener('change', function () {
                atualizarIgrejasMinisterio();
                renderizarMinisterios();
            });

            filtroIgrejaMinisterio.addEventListener('change', renderizarMinisterios);
            filtroTipoMinisterio.addEventListener('change', renderizarMinisterios);

            $('#modalDetalheMinisterios').on('shown.bs.modal', function () {
                atualizarIgrejasMinisterio();
                renderizarMinisterios();
            });

            $('#modalDetalheMinisterios').on('hidden.bs.modal', function () {
                filtroDistritoMinisterio.value = '';
                atualizarIgrejasMinisterio();
                filtroIgrejaMinisterio.value = '';
                filtroTipoMinisterio.value = '';
                renderizarMinisterios();
            });
        }
    });
</script>
@endsection
