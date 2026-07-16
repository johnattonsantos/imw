<div class="tab-pane fade" id="border-top-formacao" role="tabpanel" aria-labelledby="border-top-formacao">
    <blockquote class="blockquote">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-4">
                <thead>
                    <tr>
                        <th>{{ __('CURSO') }}</th>
                        <th>{{ __('INICIO') }}</th>
                        <th>{{ __('CONCLUSÂO') }}</th>
                        <th>{{ __('OBSERVAÇÃO') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="formacao-tbody">
                    @forelse ($pessoa->formacoesEclesiasticas as $formacaoEclesiastica)
                    <tr>
                        <td>
                            <select class="form-control curso-nome" name="curso-nome[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($cursos as $curso)
                                    <option value="{{ $curso->id }}"
                                        {{ $formacaoEclesiastica->curso_id == $curso->id ? 'selected' : '' }}>
                                        {{ $curso->nome }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="date" class="form-control" name="curso-data-inicio[]" value="{{ $formacaoEclesiastica->inicio }}">
                        </td>
                        <td>
                            <input type="date" class="form-control" name="curso-data-conclusao[]" value="{{ $formacaoEclesiastica->termino }}">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="curso-observacao[]"  value="{{ $formacaoEclesiastica->observacao }}">
                        </td>
                        <td style="width: 200px;">
                            <div class="centralizado">
                                <!-- Botão Adicionar -->
                                <button type="button" title="{{ __('Adicionar Linha') }}"
                                    class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-formacaoEclesiastica">
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
                                    class="btn btn-sm btn-danger btn-rounded apagar-linha-formacaoEclesiastica">
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
                            <select class="form-control curso-nome" name="curso-nome[]">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($cursos as $curso)
                                    <option value="{{ $curso->id }}">{{ $curso->nome }}</option>
                                @endforeach
                            </select>

                        </td>
                        <td>
                            <input type="date" class="form-control" name="curso-data-inicio[]" value="">
                        </td>
                        <td>
                            <input type="date" class="form-control" name="curso-data-conclusao[]" value="">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="curso-observacao[]"  value="">
                        </td>
                        <td style="width: 200px;">
                            <div class="centralizado">
                                <!-- Botão Adicionar -->
                                <button type="button" title="{{ __('Adicionar Linha') }}"
                                    class="btn btn-sm btn-secondary mr-2 btn-rounded adicionar-formacaoEclesiastica">
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
                                    class="btn btn-sm btn-danger btn-rounded apagar-linha-formacaoEclesiastica">
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
