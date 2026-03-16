<div class="tab-pane fade" id="border-top-historico" role="tabpanel" aria-labelledby="border-top-historico">
    <blockquote class="blockquote">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-4">
                <thead>
                    <tr>
                        <th>DATA</th>
                        <th>OCORRÊNCIA</th>
                        <th>MODO/FORMA</th>
                        <th>IGREJA</th>
                        <th>CONGREGAÇÃO</th>
                        <th>PASTOR</th>
                    </tr>
                </thead>
                <tbody id="historico-tbody">
                    @foreach ($pessoa->rolPermanente as $rolPermanente)
                        @php
                            if ($rolPermanente->status == 'I') {
                                $class = 'tr-red';
                                $data = [
                                    'date' => optional($rolPermanente->dt_exclusao)->format('d/m/Y'),
                                    'status' => 'Exclusão',
                                    'mode' => optional($rolPermanente->modoExclusao)->nome,
                                    'church' => $rolPermanente->igreja->nome,
                                    'congregation' => optional($rolPermanente->congregacao)->nome,
                                    'cleric' => optional($rolPermanente->clerigo)->nome,
                                ];
                            } elseif ($rolPermanente->status == 'A') {
                                $class = 'tr-green';
                                $data = [
                                    'date' => optional($rolPermanente->dt_recepcao)->format('d/m/Y'),
                                    'status' => 'Recebimento',
                                    'mode' => optional($rolPermanente->modoRecepcao)->nome,
                                    'church' => $rolPermanente->igreja->nome,
                                    'congregation' => optional($rolPermanente->congregacao)->nome,
                                    'cleric' => optional($rolPermanente->clerigo)->nome,
                                ];
                            } elseif ($rolPermanente->status == 'T') {
                                $class = 'tr-green';
                                $data = [
                                    'date' => optional($rolPermanente->dt_recepcao)->format('d/m/Y'),
                                    'status' => 'Transferência',
                                    'mode' => optional($rolPermanente->modoRecepcao)->nome,
                                    'church' => $rolPermanente->igreja->nome,
                                    'congregation' => optional($rolPermanente->congregacao)->nome,
                                    'cleric' => optional($rolPermanente->clerigo)->nome,
                                ];
                            }
                        @endphp
                        <tr class="{{ $class }}">
                            <td>{{ $data['date'] }}</td>
                            <td>{{ $data['status'] }}</td>
                            <td>{{ $data['mode'] }}</td>
                            <td>{{ $data['church'] }}</td>
                            <td>{{ $data['congregation'] }}</td>
                            <td>{{ $data['cleric'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-12">
                @if(! $pessoa->notificacaoTransferenciaAtiva)
                    <a href="{{ route('membro.exclusao_transferencia', ['id' => $pessoa->id]) }}" class="btn btn-secondary">
                        <x-bx-transfer-alt/> Transferir
                    </a>
                @endif
                <a href="{{ route('membro.disciplinar', ['id' => $pessoa->id]) }}" class="btn btn-warning">
                    <x-bx-block/> Disciplinar
                </a>
                @if($pessoa->status === \App\Models\MembresiaMembro::STATUS_ATIVO)
                    <a href="{{ route('membro.exclusao', ['id' => $pessoa->id]) }}" class="btn btn-danger">
                        <x-bx-minus-circle/> Excluir
                    </a>
                @endif
                <!-- <a href="{{ route('membro.transferencia_interna', ['id' => $pessoa->id]) }}" class="btn btn-primary">
                    <x-bx-transfer-alt/> Transferir Internamente
                </a> -->
            </div>
        </div>
    </blockquote>
</div>
