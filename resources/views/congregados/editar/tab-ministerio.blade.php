<div class="tab-pane fade" id="border-top-ministerio" role="tabpanel" aria-labelledby="border-top-ministerial">
    <blockquote class="blockquote">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-4">
                <thead>
                    <tr>
                        <th>{{ __('MINISTÉRIOS') }}</th>
                        <th>{{ __('FUNÇÃO') }}</th>
                        <th>{{ __('NOMEAÇÃO') }}</th>
                        <th>{{ __('EXONERAÇÃO') }}</th>
                        <th>{{ __('OBSERVAÇÕES') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="ministerial-tbody">
                    @forelse ($pessoa->funcoesMinisteriais as $funcaoMinisterial)
                        <tr>
                            <td>
                                <select class="form-control ministerial-departamento" name="ministerial-departamento[]">
                                    <option value="">{{ __('Selecione') }}</option>
                                    @foreach ($ministerios as $ministerio)
                                        <option value="{{ $ministerio->id }}"
                                            {{ $funcaoMinisterial->setor_id == $ministerio->id ? 'selected' : '' }}>
                                            {{ $ministerio->descricao }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control ministerial-funcao" name="ministerial-funcao[]">
                                    <option value="">{{ __('Selecione') }}</option>
                                    @foreach ($funcoes as $funcao)
                                        <option value="{{ $funcao->id }}"
                                            {{ $funcaoMinisterial->tipoatuacao_id == $funcao->id ? 'selected' : '' }}>
                                            {{ $funcao->descricao }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" class="form-control" name="ministerial-nomeacao[]"
                                    value="{{ $funcaoMinisterial->data_entrada }}">
                            </td>
                            <td>
                                <input type="date" class="form-control" name="ministerial-exoneracao[]"
                                    value="{{ $funcaoMinisterial->data_saida }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="ministerial-observacao[]"
                                    value="{{ $funcaoMinisterial->observacoes }}">
                            </td>
                            <td style="width: 200px;">
                                <div class="centralizado">
                                    <!-- Botão Adicionar -->
                                    <button type="button" title="{{ __('Adicionar Linha') }}"
                                        class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-ministerial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-plus-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg>
                                    </button>
                                    <!-- Botão Apagar -->
                                    <button type="button" title="{{ __('Apagar Linha') }}"
                                        class="btn btn-sm btn-danger btn-rounded apagar-linha-ministerial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                                <select class="form-control ministerial-departamento" name="ministerial-departamento[]">
                                    <option value="">{{ __('Selecione') }}</option>
                                    @foreach ($ministerios as $ministerio)
                                        <option value="{{ $ministerio->id }}">{{ $ministerio->descricao }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control ministerial-funcao" name="ministerial-funcao[]">
                                    <option value="">{{ __('Selecione') }}</option>
                                    @foreach ($funcoes as $funcao)
                                        <option value="{{ $funcao->id }}">{{ $funcao->descricao }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" class="form-control" name="ministerial-nomeacao[]" value="">
                            </td>
                            <td>
                                <input type="date" class="form-control" name="ministerial-exoneracao[]" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="ministerial-observacao[]" value="">
                            </td>
                            <td style="width: 200px;">
                                <div class="centralizado">
                                    <!-- Botão Adicionar -->
                                    <button type="button" title="{{ __('Adicionar Linha') }}"
                                        class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-ministerial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-plus-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="16"></line>
                                            <line x1="8" y1="12" x2="16" y2="12"></line>
                                        </svg>
                                    </button>
                                    <!-- Botão Apagar -->
                                    <button type="button" title="{{ __('Apagar Linha') }}"
                                        class="btn btn-sm btn-danger btn-rounded apagar-linha-ministerial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </blockquote>
</div>
