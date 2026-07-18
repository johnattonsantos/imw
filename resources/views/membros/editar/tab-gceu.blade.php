<div class="tab-pane fade" id="border-top-gceu" role="tabpanel" aria-labelledby="border-top-gceuIndex">
    <blockquote class="blockquote">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-4">
                <thead>
                    <tr>
                        <th>{{ __('GCEU') }}</th>
                        <th>{{ __('FUNÇÃO') }}</th>
                        <!-- <th>{{ __('OBSERVAÇÕES') }}</th> -->
                        <th></th>
                    </tr>
                </thead>
                <tbody id="gceuIndex-tbody">
                    @forelse ($gceuMembros as $gceuMembro)
                    <tr>
                        <td>
                            <select class="form-control gceu" name="gceu[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($gceus as $gceu)
                                <option value="{{ $gceu->id }}" {{ $gceuMembro->gceu_cadastro_id == $gceu->id ? 'selected' : '' }}>
                                    {{ $gceu->nome }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control gceu-funcao" name="gceu-funcao[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($gceuFuncoes as $funcao)
                                    @if($funcao->id != 6)
                                        <option value="{{ $funcao->id }}" {{ $gceuMembro->gceu_funcao_id == $funcao->id ? 'selected' : '' }}>
                                            {{ $funcao->funcao }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td style="width: 200px;">
                            <div class="centralizado">
                                <!-- Botão Adicionar -->
                                <button type="button" title="{{ __('Adicionar Linha') }}" class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-gceuIndex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                </button>
                                <!-- Botão Apagar -->
                                <button type="button" title="{{ __('Apagar Linha') }}" class="btn btn-sm btn-danger btn-rounded apagar-linha-gceuIndex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
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
                            <select class="form-control gceu" name="gceu[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($gceus as $gceu)
                                <option value="{{ $gceu->id }}">{{ $gceu->nome }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control gceu-funcao" name="gceu-funcao[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($gceuFuncoes as $funcao)
                                <option value="{{ $funcao->id }}">{{ $funcao->funcao }}</option>
                                @endforeach
                            </select>
                        </td>
                        <!-- <td>
                            <input type="text" class="form-control ministerial-observacao" name="ministerial-observacao[]" value="">
                        </td> -->
                        <td style="width: 200px;">
                            <div class="centralizado">
                                <!-- Botão Adicionar -->
                                <button type="button" title="{{ __('Adicionar Linha') }}" class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-linha-gceuIndex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                </button>
                                <!-- Botão Apagar -->
                                <button type="button" title="{{ __('Apagar Linha') }}" class="btn btn-sm btn-danger btn-rounded apagar-linha-gceuIndex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
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