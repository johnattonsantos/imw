<style>
.anexo-block {
    border-radius: 6px;
    background-color: #fafafa;
    border: none;
    transition: all 0.4s ease-in-out;
    padding: .5rem 1rem;
}

.anexo-block .mark-text {
    font-weight: bold
}

.anexo-block .small-text {
    color: #888ea8;
}

.anexo-icon {
    color: #888ea8;
    width: 2.6rem;
}

</style>

<!-- Anexos anexos -->
<div class="col-12 mb-5">
    <div class="row" style="gap: .5rem" id="anexosList">   
        @foreach ($anexos as $index => $anexo)
            @if($anexo)
                {{-- anexo existente --}}
                <div class="col-sm-12 mb-4 d-flex justify-content-between anexo-block">
                    {{-- info  --}}
                    <div class="d-flex">
                        <label class="mr-2">
                            <x-bx-file class="anexo-icon" />
                        </label>
                        <div>
                            <p class="mb-0 mark-text">{{ $anexo->descricao ?? sprintf("Anexo %s", $index+1) }}</p>  
                            <p class="small-text mb-0">Criado em {{ $anexo->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
        
                    {{-- actions --}}
                    <div>
                        <a  href="{{ route('financeiro.downloadAnexo', $anexo->id) }}" 
                            class="btn btn-secondary mr-2 rounded-circle bs-tooltip" 
                            target="_blank" 
                            title="{{ __('Baixar anexo') }}">
                            <x-bx-download />
                        </a>
                        <button class="btn btn-danger mr-2 rounded-circle delete-anexo bs-tooltip" 
                                title="{{ __('Excluir anexo') }}"
                                data-url="{{ route('financeiro.anexo.delete', $anexo->id) }}">
                            <x-bx-trash />
                        </button>
                    </div>
                </div>
            @else
                {{-- novo anexo --}}
                <div class="col-sm-12 mb-4 d-flex justify-content-between anexo-block">
                    <div class="d-flex">
                        <button class="btn btn-outline-primary rounded-circle mr-2 novo-anexo" style="border-width: 2px!important;">
                            <x-bx-plus />
                        </button>
                        <div>
                            <p class="mb-0 mark-text text-primary">{{ __('Adicione um novo anexo') }}</p> 
                            <p class="small-text mb-0">{{ __('PDF, DOC, DOCX | 2 Mb max.') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
