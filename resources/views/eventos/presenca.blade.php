@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Presença do Evento', 'url' => route('eventos.presenca'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<style>
    #qr-reader {
        width: 100%;
        max-width: 520px;
        border: 1px solid #d6dce5;
        border-radius: 8px;
        overflow: hidden;
        background: #f7f9fc;
    }

    .presence-result {
        border-left: 5px solid #2196f3;
    }

    .presence-result.entrada {
        border-left-color: #1abc9c;
    }

    .presence-result.saida {
        border-left-color: #e7515a;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-12">
                    <h4>{{ __('Presença do Evento') }}</h4>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="form-group">
                        <label for="evento_id">{{ __('Evento') }}</label>
                        <select id="evento_id" class="form-control">
                            <option value="">{{ __('Todos os eventos') }}</option>
                            @foreach ($eventOptions as $eventOption)
                                <option value="{{ $eventOption->id }}">
                                    {{ optional($eventOption->data_inicio)->format('d/m/Y') }} - {{ $eventOption->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="qr-reader" class="mb-3"></div>

                    <div class="d-flex flex-wrap mb-3">
                        <button type="button" id="start-camera" class="btn btn-primary mr-2 mb-2">{{ __('Iniciar câmera') }}</button>
                        <button type="button" id="stop-camera" class="btn btn-light mb-2" disabled>{{ __('Parar câmera') }}</button>
                    </div>

                    <div class="form-group">
                        <label for="qr_token_manual">{{ __('Código manual') }}</label>
                        <div class="input-group">
                            <input type="text" id="qr_token_manual" class="form-control" placeholder="{{ __('Cole ou digite o código do QR') }}">
                            <div class="input-group-append">
                                <button type="button" id="confirm-manual" class="btn btn-primary">{{ __('Confirmar') }}</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ __('A cada leitura o sistema alterna automaticamente entre entrada e saída.') }}</small>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div id="presence-feedback" class="alert d-none"></div>

                    <div id="presence-result" class="card presence-result d-none mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div>
                                    <h5 id="presence-name" class="mb-1">-</h5>
                                    <p id="presence-event" class="mb-1 text-muted">-</p>
                                    <p id="presence-location" class="mb-0 text-muted">-</p>
                                </div>
                                <span id="presence-type" class="badge badge-primary">-</span>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>{{ __('CPF') }}:</strong> <span id="presence-cpf">-</span></p>
                                    <p class="mb-1"><strong>{{ __('Telefone') }}:</strong> <span id="presence-phone">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>{{ __('Igreja') }}:</strong> <span id="presence-church">-</span></p>
                                    <p class="mb-1"><strong>{{ __('Função Eclesiástica') }}:</strong> <span id="presence-role">-</span></p>
                                </div>
                            </div>
                            <p class="mb-0"><strong>{{ __('Registrado em') }}:</strong> <span id="presence-time">-</span></p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Últimos movimentos') }}</th>
                                    <th>{{ __('Data/Hora') }}</th>
                                </tr>
                            </thead>
                            <tbody id="presence-history">
                                <tr>
                                    <td colspan="2" class="text-center">{{ __('Nenhuma leitura realizada.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const registerUrl = @json(route('eventos.presenca.registrar'));
        const csrfToken = @json(csrf_token());
        const eventSelect = document.getElementById('evento_id');
        const feedback = document.getElementById('presence-feedback');
        const resultCard = document.getElementById('presence-result');
        const startButton = document.getElementById('start-camera');
        const stopButton = document.getElementById('stop-camera');
        const manualInput = document.getElementById('qr_token_manual');
        const manualButton = document.getElementById('confirm-manual');
        const historyBody = document.getElementById('presence-history');
        let scanner = null;
        let lastRead = {token: null, at: 0};

        function showFeedback(type, message) {
            feedback.className = 'alert alert-' + type;
            feedback.innerHTML = message;
        }

        function clearFeedback() {
            feedback.className = 'alert d-none';
            feedback.innerHTML = '';
        }

        function updateResult(data) {
            const type = data.tipo || 'entrada';
            resultCard.className = 'card presence-result ' + type + ' mb-3';
            resultCard.classList.remove('d-none');

            document.getElementById('presence-name').textContent = data.inscrito.nome || '-';
            document.getElementById('presence-event').textContent = data.evento.titulo || '-';
            document.getElementById('presence-location').textContent = data.evento.local || '-';
            document.getElementById('presence-type').textContent = data.tipo_label || '-';
            document.getElementById('presence-type').className = 'badge ' + (type === 'entrada' ? 'badge-success' : 'badge-danger');
            document.getElementById('presence-cpf').textContent = data.inscrito.cpf || '-';
            document.getElementById('presence-phone').textContent = data.inscrito.telefone || '-';
            document.getElementById('presence-church').textContent = data.inscrito.igreja || '-';
            document.getElementById('presence-role').textContent = data.inscrito.funcao || '-';
            document.getElementById('presence-time').textContent = data.registrado_em || '-';

            historyBody.innerHTML = '';
            (data.historico || []).forEach(function (item) {
                const row = document.createElement('tr');
                row.innerHTML = '<td>' + item.tipo_label + '</td><td>' + item.registrado_em + '</td>';
                historyBody.appendChild(row);
            });
        }

        function registerToken(token) {
            token = String(token || '').trim();

            if (!token) {
                showFeedback('danger', @json(__('Leia ou informe o QR Code do inscrito.')));
                return;
            }

            clearFeedback();

            const payload = new FormData();
            payload.append('qr_token', token);
            payload.append('evento_id', eventSelect.value || '');

            fetch(registerUrl, {
                method: 'POST',
                body: payload,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(async function (response) {
                    const data = await response.json().catch(function () {
                        return {};
                    });

                    if (!response.ok) {
                        const errors = data.errors
                            ? Object.values(data.errors).flat().join('<br>')
                            : (data.message || @json(__('Não foi possível registrar a presença.')));
                        throw new Error(errors);
                    }

                    return data;
                })
                .then(function (data) {
                    showFeedback(data.tipo === 'entrada' ? 'success' : 'warning', data.message);
                    updateResult(data);
                    if (manualInput) {
                        manualInput.value = '';
                    }
                })
                .catch(function (error) {
                    showFeedback('danger', error.message);
                });
        }

        function onScanSuccess(decodedText) {
            const now = Date.now();
            const token = String(decodedText || '').trim();

            if (lastRead.token === token && now - lastRead.at < 2500) {
                return;
            }

            lastRead = {token: token, at: now};
            registerToken(token);
        }

        if (manualButton) {
            manualButton.addEventListener('click', function () {
                registerToken(manualInput.value);
            });
        }

        if (manualInput) {
            manualInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    registerToken(manualInput.value);
                }
            });
        }

        if (startButton) {
            startButton.addEventListener('click', function () {
                if (typeof Html5Qrcode === 'undefined') {
                    showFeedback('danger', @json(__('Leitor de QR Code não carregado. Verifique a conexão e tente novamente.')));
                    return;
                }

                scanner = scanner || new Html5Qrcode('qr-reader');
                scanner.start(
                    {facingMode: 'environment'},
                    {fps: 10, qrbox: {width: 250, height: 250}},
                    onScanSuccess
                ).then(function () {
                    startButton.disabled = true;
                    stopButton.disabled = false;
                }).catch(function () {
                    showFeedback('danger', @json(__('Não foi possível acessar a câmera. Verifique a permissão do navegador.')));
                });
            });
        }

        if (stopButton) {
            stopButton.addEventListener('click', function () {
                if (!scanner) {
                    return;
                }

                scanner.stop().then(function () {
                    startButton.disabled = false;
                    stopButton.disabled = true;
                });
            });
        }
    });
</script>
@endsection
