@if(!$igreja->deleted_at && !$igreja->dt_extincao)
    {{-- Editar congregação --}}
    @if(false) // criar edição apenas para role de região
        <a  href="{{ route('igrejas.regiao.editar', $igreja->id) }}" 
            title="{{ __('Editar') }}"
            class="btn btn-sm btn-dark mr-2 btn-rounded bs-tooltip">
            <x-bx-pencil />
        </a>
    @endif

    {{-- Igreja-Região porém estamos reutilizando o mesmo link de igrejas--}}

    <a  href="{{ route('igrejas.regiao.estatistica-ano-eclesiastico', ['igreja' => $igreja->id]) }}"
        title="{{ __('Estatística do Ano Eclesiástico') }}" 
        class="btn btn-sm btn-secondary mr-2 btn-rounded bs-tooltip">
        <x-bx-group />
    </a>
    
    <a  href="{{ route('igrejas.regiao.balancete', ['igreja' => $igreja->id]) }}" 
        title="{{ __('Balancete') }}" 
        class="btn btn-sm btn-primary mr-2 btn-rounded bs-tooltip">
        <x-bx-wallet />
    </a>

    <a  href="{{ route('igrejas.regiao.movimento-diario', ['igreja' => $igreja->id]) }}" 
        title="{{ __('Movimentação diária') }}"
        class="btn btn-sm btn-info mr-2 btn-rounded bs-tooltip">
        <x-bx-calendar />
    </a>

    <a  href="{{ route('igrejas.regiao.livrorazao', ['igreja' => $igreja->id]) }}" 
        title="{{ __('Livro Razão') }}"
        class="btn btn-sm btn-success mr-2 btn-rounded bs-tooltip">
        <x-bx-book-alt />
    </a>
@endif