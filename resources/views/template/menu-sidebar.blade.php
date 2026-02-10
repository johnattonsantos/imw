 <style>
     #sidebar ul.menu-categories li.menu>#notificacao {
         background-color: inherit !important;
         color: inherit !important;
     }
 </style>
 <!--  BEGIN SIDEBAR  -->
 <div class="sidebar-wrapper sidebar-theme" style="overflow-y: scroll; scrollbar-width: thin; ">
     <nav id="sidebar">

         <ul class="navbar-nav theme-brand flex-row  text-center">
             <li class="nav-item theme-text">
                 <a href="/" class="nav-link"> <img src="{{ url('auth/images/logo_branco.png') }}" alt=""> </a>
             </li>
             <li class="nav-item toggle-sidebar">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="feather sidebarCollapse feather-chevrons-left">
                     <polyline points="11 17 6 12 11 7"></polyline>
                     <polyline points="18 17 13 12 18 7"></polyline>
                 </svg>
             </li>
         </ul>
         <div class="shadow-bottom"></div>

         <ul class="list-unstyled menu-categories" id="accordionExample">
             <li class="menu text-center mb-2">

                 <div class="user-photo mb-2" style="text-align: center; ">
                     <img src="{{ asset('theme/assets/img/perfil.png') }}" alt="User"
                         style="width: 50px; height: 50px; border-radius: 50%;">
                 </div>
                 <div class="user-details" style="text-align: center;">
                     <b class="text-bold text-white" style="font-size: 12px ;">{{ $firstName }}
                         {{ $lastName }}</b><br>
                     @if (session('session_perfil'))
                         <span style="font-size: 12px ;">{{ session('session_perfil')->perfil_nome }}</span> <br>
                         <span style="font-size: 12px ;">{{ session('session_perfil')->instituicao_nome }}</span> <br>
                     @endif

                 </div>
             </li>
             <li class="menu {{Request::is('') ? 'active' : '' }}">
                 <a href="/" aria-expanded="true" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-home">
                             <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                             <polyline points="9 22 9 12 15 12 15 22"></polyline>
                         </svg>
                         <span>Dashboard</span>
                     </div>
                 </a>
             </li>
             @if (session('session_perfil') && (int) session('session_perfil')->perfil_id === 3)
                 <!-- <li class="menu {{ Request::is('comunicacao*') ? 'active' : '' }}">
                     <a href="{{ route('comunicacao.index') }}" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-message-square">
                                 <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                             </svg>
                             <span>Comunicação</span>
                         </div>
                     </a>
                 </li>
                 <li class="menu {{ Request::is('categoria-comunicacao*') ? 'active' : '' }}">
                     <a href="{{ route('categoria-comunicacao.index') }}" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-tag">
                                 <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"></path>
                                 <line x1="7" y1="7" x2="7.01" y2="7"></line>
                             </svg>
                             <span>Categoria Comunicação</span>
                         </div>
                     </a>
                 </li> -->
             @endif
             @if (optional($baseParams->notificacoesTransferencia)->count())
                 <li class="menu container-fluid col-xs-4">
                     <a href="{{ route('notificacoes-tranferencia.index') }}" aria-expanded="false"
                         class="dropdown-toggle" id="notificacao">
                         <span class="badge badge-secondary" style="padding: 6px; border-radius: 6px;">Nova
                             Notificação
                         </span>
                     </a>
                 </li>
             @endif

             <li class="menu menu-heading">
                 <div class="heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                         <circle cx="12" cy="12" r="10"></circle>
                     </svg><span>MENU PRINCIPAL</span></div>
             </li>
             @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-secretaria'))
                 <li class="menu">
                     <a href="#secretaria" data-toggle="collapse" aria-expanded="{{Request::is('secretaria/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                             </svg>
                             <span>Secretaria</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('secretaria/*') ? 'collapse show' : '' }}" id="secretaria" data-parent="#secretaria">
                         <li {!! Request::is('secretaria/membro*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('membros-index'))
                                 <a href="{{ route('membro.index') }}">Membros</a>
                             @endif
                         </li>
                         <!-- <li {!! Request::is('secretaria/recadastramento-membro*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('membros-index'))
                                 <a href="{{ route('recadastramento-membro.indexRecadastramento') }}">Membresia Validação</a>
                             @endif
                         </li> -->
                         <li {!! Request::is('secretaria/congregado*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('congregados-index'))
                                 <a href="{{ route('congregado.index') }}">Congregados</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/visitante*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('visitantes-index'))
                                 <a href="{{ route('visitante.index') }}">Visitantes</a>
                             @endif
                         </li>
                         <li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-secretaria'))
                                 <span>Relatórios</span>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membresia') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-membresia'))
                                 <a href="{{ route('relatorio.membresia') }}">Membresia</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/aniversariantes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-aniversariantes'))
                                 <a href="{{ route('relatorio.aniversariantes') }}">Aniversariantes</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/historico-eclesiastico') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-historico-eclesiastico'))
                                 <a href="{{ route('relatorio.historico-eclesiastico') }}">Membros por Ministério</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membros-disciplinados') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-membro-disciplinado'))
                                 <a href="{{ route('relatorio.membros-disciplinados') }}">Membros Disciplinados</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/funcao-eclesiastica') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-funcao-eclesiastica'))
                                 <a href="{{ route('relatorio.funcao-eclesiastica') }}">Função Eclesiástica</a>
                             @endif
                         </li>
                     </ul>
                 </li>
             @endif





             @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-instituicoes'))
                 <li class="menu {{Request::is('instituicoes-regiao*') ? 'active' : '' }}">
                     <a href="/instituicoes-regiao?tipo_instituicao_id=1" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                             </svg>
                             <span>Instituicões</span>
                         </div>

                     </a>
                 </li>
             @endif

             <li class="menu mx-3">
                 @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                     <a href="">Igrejas</a>
                 @endif
                 @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                 <li class="menu {{ Request::is('igreja', 'igreja/*', 'igrejas-regiao*') ? 'active' : '' }}">
                 <a href="{{ route('igrejas.regiao.index') }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-church">
                             <path d="M12 2L8 6h8l-4-4z"></path>
                             <rect x="4" y="6" width="16" height="16" rx="2"></rect>
                             <path d="M8 12h8M12 16v4"></path>
                         </svg>
                         <span>Igrejas</span>
                     </div>
                 </a>
             </li>
             @endif
             </li>

             {{-- Menu Clérigos --}}

             <li class="menu mx-3">
                 @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                     <a href="">Clérigos</a>
                 @endif
                 @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
             <li class="menu x-2">
                 <a href="#clerigos" data-toggle="collapse" aria-expanded="{{Request::is('clerigos*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-users">
                             <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                             <circle cx="12" cy="7" r="4"></circle>
                             <path d="M16 21v-2a4 4 0 0 0-3-3.87"></path>
                             <path d="M8 21v-2a4 4 0 0 1 3-3.87"></path>
                         </svg>

                         <span>Clérigos</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('clerigos*') ? 'collapse show' : '' }}" id="clerigos" data-parent="#clerigos">
                     <li {!! Request::is('clerigos', 'clerigos/nomeacoes/*', 'clerigos/novo*', 'clerigos/editar/*') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                             <a href="{{ route('clerigos.index') }}">Clérigos</a>
                         @endif
                     </li>
                     <li {!! Request::is('clerigos/prebendas','clerigos/prebendas/*') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                             <a href="{{ route('clerigos.prebendas.index') }}">Prebendas</a>
                         @endif
                     </li>
                 </ul>
                 @endif


                 @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-financeiro'))
             <li class="menu">
                 <a href="#financeiro" data-toggle="collapse" aria-expanded="{{Request::is('financeiro/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-dollar-sign">
                             <line x1="12" y1="1" x2="12" y2="23"></line>
                             <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                         </svg>
                         <span>Financeiro</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('financeiro/*') ? 'collapse show' : '' }}" id="financeiro" data-parent="#financeiro">
                     <li {!! Request::is('financeiro/movimento-caixa') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-movimentocaixa-index'))
                             <a href="{{ route('financeiro.movimento.caixa') }}">Movimento de Caixa</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/consolidar-caixa') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-consolidarcaixa'))
                             <a href="{{ route('financeiro.consolidar.caixa') }}">Consolidação de Caixa</a>
                         @endif
                     </li>
                      <li {!! Request::is('financeiro/cota-orcamentaria') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('cota-orcamentaria'))
                             <a href="{{ route('financeiro.cota.orcamentaria') }}">Cota Orçamentária</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/plano-conta') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-planoconta'))
                             <a href="{{ route('financeiro.plano.conta') }}">Plano Conta</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/caixas') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-caixas'))
                             <a href="{{ route('financeiro.caixas') }}">Caixas</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/fornecedor', 'financeiro/fornecedor/*') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('fornecedores-index'))
                             <a href="{{ route('fornecedor.index') }}">Fornecedores</a>
                         @endif
                     </li>
                     <li class="submenu-fixo mt-3 mb-3">
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-financeiro'))
                             <span>Relatórios</span>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/movimento-diario') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-movimento-diario'))
                             <a href="{{ route('financeiro.relatorio-movimento-diario') }}">Movimento Diário</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrocaixa') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrocaixa'))
                             <a href="{{ route('financeiro.relatorio-livrocaixa') }}">Livro Caixa</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/balancete') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-balancete'))
                             <a href="{{ route('financeiro.relatorio-balancete') }}">Balancete</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrograde') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrograde'))
                             <a href="{{ route('financeiro.relatorio-livrograde') }}">Livro Grade</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrorazao') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrorazao'))
                             <a href="{{ route('financeiro.relatorio-livrorazao') }}">Livro Razão</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/movimento-bancario') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-movimento-bancario'))
                             <a href="{{ route('financeiro.movimento-bancario') }}">Movimento Bancário</a>
                         @endif
                     </li>

                 </ul>
             </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio'))
                 <li class="menu">
                     <a href="#financeiro-distrito" data-toggle="collapse" aria-expanded="{{Request::is('distrito/*') ? 'true' : 'false' }}"
                         class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                 <polyline points="14 2 14 8 20 8"></polyline>
                                 <line x1="16" y1="13" x2="8" y2="13"></line>
                                 <line x1="16" y1="17" x2="8" y2="17"></line>
                                 <polyline points="10 9 9 9 8 9"></polyline>
                             </svg>
                             <span>Relatórios Distritais</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('distrito/*') ? 'collapse show' : '' }}" id="financeiro-distrito"
                         data-parent="#financeiro-distrito">
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio'))
                                 <span>Financeiro</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/lancamentodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-lancamento-das-igrejas'))
                                 <a href="{{ route('distrito.relatorio.lancamentodasigrejas') }}">Lançamento das
                                     Igrejas</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/saldodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-saldo-das-igrejas'))
                                 <a href="{{ route('distrito.relatorio.saldodasigrejas') }}">Saldo de Caixas</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/livrorazaogeral') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-livro-razao-geral'))
                                 <a href="{{ route('distrito.relatorio.livrorazaogeral') }}">Livro Razão Geral</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/orcamento') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-orcamento'))
                                 <a href="{{ route('distrito.relatorio.orcamento') }}">Orçamento</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/variacaofinanceira') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-variacao-financeira'))
                                 <a href="{{ route('distrito.relatorio.variacaofinanceira') }}">Variação Financeira</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/cota-orcamentaria') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-cota-orcamentaria'))
                                <a href="{{ route('distrito.cota.orcamentaria') }}">Cota Orçamentária</a>
                            @endif
                        </li>
                        <li {!! Request::is('distrito/recursos-humanos') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-recurso-humano'))
                                <a href="{{ route('distrito.recurso.humano') }}">Recursos humanos</a>
                            @endif
                        </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio'))
                                 <span>Membresia</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/membrosministerio') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-membros-ministerio'))
                                 <a href="{{ route('distrito.relatorio.membrosministerio') }}">Membros por
                                     Ministério</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/quantidademembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-quantidade-membros'))
                                 <a href="{{ route('distrito.relatorio.quantidademembros') }}">Quantidade de
                                     Membros</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/estatisticagenero') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-estatistica-genero'))
                                 <a href="{{ route('distrito.relatorio.estatisticagenero') }}">Estatística por
                                     Gênero</a>
                             @endif
                         </li>

                         <li {!! Request::is('distrito/apirantes-por-igrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-aspirantes-igrejas'))
                                 <a href="{{ route('distrito.relatorio.apirateporigreja') }}">Aspirantes por Igreja</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-igrejas'))
                                 <span>Igrejas</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/congregacoes-por-igrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-congregacoes-igrejas'))
                                 <a href="{{ route('distrito.relatorio.congregacaoporigreja') }}">Congregações por Igrejas</a>
                             @endif
                         </li>

                        @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-relatorios'))
                            <li class="submenu-fixo mt-3 mb-3">
                                <span>GCEU</span>                                
                            </li>

                            <li {!! Request::is('distrito/relatorio-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-gceu'))
                                    <a href="{{ route('distrito.relatorio.gceu') }}">Lista GCEU</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-carta-pastoral-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-relatorio-carta-pastoral'))
                                    <a href="{{ route('distrito.relatorio.carta-pastoral-distrito') }}">Carta Pastoral</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-funcoes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-funcoes'))
                                    <a href="{{ route('distrito.relatorio.funcoes.gceu') }}">Lista Função</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-aniversariantes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-aniversariantes'))
                                    <a href="{{ route('distrito.relatorio.aniversariantes.gceu') }}">Lista Aniversariantes</a>
                                @endif
                            </li>
                        @endif
                     </ul>
                 </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
                 <li class="menu">
                     <a href="#financeiro-regiao" data-toggle="collapse" aria-expanded="{{Request::is('regiao/relatorio/*') ? 'true' : 'false' }}"
                         class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                 <polyline points="14 2 14 8 20 8"></polyline>
                                 <line x1="16" y1="13" x2="8" y2="13"></line>
                                 <line x1="16" y1="17" x2="8" y2="17"></line>
                                 <polyline points="10 9 9 9 8 9"></polyline>
                             </svg>
                             <span>Relatórios Regionais</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('regiao/relatorio/*') ? 'collapse show' : '' }}" id="financeiro-regiao"
                         data-parent="#financeiro-regiao">
                         <!-- <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
                                 <span>Contabilidade</span>
                             @endif
                         </li> -->
                         
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
                                 <span>Financeiro</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/irrf') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-lancamento-das-igrejas'))
                                 <a href="{{ route('regiao.relatorio.irrf') }}">IRRF Repasse</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/lancamentodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-lancamento-das-igrejas'))
                                 <a href="{{ route('regiao.relatorio.lancamentodasigrejas') }}">Lançamento das
                                     Igrejas</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/financeiro/balancete') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-balancete'))
                                <a href="{{ route('regiao.relatorio-balancete-regiao') }}">Balancete</a>
                            @endif
                        </li>
                         <li {!! Request::is('regiao/relatorio/saldodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-saldo-das-igrejas'))
                                 <a href="{{ route('regiao.relatorio.saldodasigrejas') }}">Saldo de Caixas</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/livrorazaogeral') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-livro-razao-geral'))
                                 <a href="{{ route('regiao.relatorio.livrorazaogeral') }}">Livro Razão Geral</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/orcamento') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-orcamento'))
                                 <a href="{{ route('regiao.relatorio.orcamento') }}">Orçamento</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/variacaofinanceira') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-variacao-financeira'))
                                 <a href="{{ route('regiao.relatorio.variacaofinanceira') }}">Variação Financeira</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/relatorio/financeiro-por-categoria') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-por-categoria'))
                                 <a href="{{ route('regiao.relatorio.financeiroPorCategoria') }}">Financeiro por Categoria</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/relatorio/cota-orcamentaria') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
                                <a href="{{ route('regiao.cota.orcamentaria') }}">Cota Orçamentária</a>
                            @endif
                        </li>

                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
                                 <span>Membresia</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/membrosministerio') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-membros-ministerio'))
                                 <a href="{{ route('regiao.relatorio.membrosministerio') }}">Membros por
                                     Ministério</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/quantidademembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-quantidade-membros'))
                                 <a href="{{ route('regiao.relatorio.quantidademembros') }}">Quantidade de Membros</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/estatisticagenero') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-estatistica-genero'))
                                 <a href="{{ route('regiao.relatorio.estatisticagenero') }}">Estatística por
                                     Gênero</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/ano-eclesiastico') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-ano-eclesiastico'))
                                 <a href="{{ route('regiao.relatorio.ano.eclesiastico') }}">Mapa Estatístico Membros</a>
                             @endif
                         </li>
                        <li class="submenu-fixo mt-3 mb-3">
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-instituicoes-igrejas'))
                                <span>Clérigos</span>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/clerigos-aniversariantes') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-aniversariantes'))
                                <a href="{{ route('regiao.relatorio.clerigosaniversariantes') }}">Clérigos Aniversariantes</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/clerigos-esposas') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-esposas'))
                                <a href="{{ route('regiao.relatorio.clerigosesposas') }}">Clérigos Esposas</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/clerigos-dados') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-dados'))
                                <a href="{{ route('regiao.relatorio.clerigosdados') }}">Clérigos Documentação</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/clerigos-categorias') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-categoria'))
                                <a href="{{ route('regiao.relatorio.clerigoscategoria') }}">Clérigos Categorias</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/clerigos-status') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-status'))
                                <a href="{{ route('regiao.relatorio.clerigosstatus') }}">Clérigos Status</a>
                            @endif
                        </li>

                         <li {!! Request::is('regiao/relatorio/historiconomeacoes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-hitorico-nomeacoes'))
                                 <a href="{{ route('regiao.estatistica.historiconomeacoes.regionais') }}">Histórico de
                                     Nomeacões</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-congregacoes-igrejas'))
                                <span>Igrejas</span>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/congregacoes-por-igrejas') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-congregacoes-igrejas'))
                                <a href="{{ route('regiao.relatorio.congregacaoporigreja') }}">Congregações por Igrejas</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/cnpj-igrejas') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-cnpj-igreja'))
                                <a href="{{ route('regiao.cnpj.igreja') }}">CNPJ por Igreja</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/contato-igrejas') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-contato-igreja'))
                                <a href="{{ route('regiao.contato.igreja') }}">Contatos por Igreja</a>
                            @endif
                        </li>
                        <li {!! Request::is('regiao/relatorio/conta-bancaria-igrejas') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-conta-bancaria-igreja'))
                                <a href="{{ route('regiao.conta.bancaria.igreja') }}">Conta Bancária por Igreja</a>
                            @endif
                        </li>

                        @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-relatorios'))
                            <li class="submenu-fixo mt-3 mb-3">
                                <span>GCEU</span>
                            </li>
                            <li {!! Request::is('regiao/relatorio/gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-gceu'))
                                    <a href="{{ route('regiao.relatorio.gceu') }}">Lista GCEU</a>
                                @endif
                            </li>
                            <li {!! Request::is('regiao/relatorio/relatorio-carta-pastoral-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-relatorio-carta-pastoral'))
                                    <a href="{{ route('regiao.relatorio.carta-pastoral-regiao') }}">Carta Pastoral</a>
                                @endif
                            </li>
                            <li {!! Request::is('regiao/relatorio/relatorio-funcoes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-funcoes'))
                                    <a href="{{ route('regiao.relatorio.funcoes.gceu') }}">Lista Função</a>
                                @endif
                            </li>
                            <li {!! Request::is('regiao/relatorio/relatorio-aniversariantes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-aniversariantes'))
                                    <a href="{{ route('regiao.relatorio.aniversariantes.gceu') }}">Lista Aniversariantes</a>
                                @endif
                            </li>
                        @endif
                     </ul>
                 </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-estatistica'))
                 <li class="menu">
                     <a href="#estatistica-regiao" data-toggle="collapse" aria-expanded="{{Request::is('regiao/estatistica/*') ? 'true' : 'false' }}"
                         class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart">
                                 <line x1="12" y1="20" x2="12" y2="10"></line>
                                 <line x1="18" y1="20" x2="18" y2="4"></line>
                                 <line x1="6" y1="20" x2="6" y2="16"></line>
                             </svg>

                             <span>Estatísticas</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('regiao/estatistica/*') ? 'collapse show' : '' }}" id="estatistica-regiao"
                         data-parent="#estatistica-regiao">
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-evolucao'))
                                 <span>Membros</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/membresia*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-membresia'))
                                 <a href="{{ route('regiao.membresia') }}">Membresia</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatistica-membros-evolucao') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-evolucao'))
                                 <a href="{{ route('regiao.estatistica.evolucao') }}">Evolução</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatistica-total-membresia') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-total-membresia'))
                                 <a href="{{ route('regiao.estatistica.totalMembresia') }}">Total Membresia</a>
                             @endif

                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticaescolaridade') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-escolaridade'))
                                 <a href="{{ route('regiao.relatorio.estatisticaescolaridade') }}">Escolaridade</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticaestadocivil') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-estado-civl'))
                                 <a href="{{ route('regiao.relatorio.estatisticaestadocivil') }}">Estado Cívil</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticageneroporcentagem') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-genero'))
                                 <a href="{{ route('regiao.relatorio.estatisticageneroporcentagem') }}">Gênero</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticatotalmembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-quantidade-membros'))
                                 <a href="{{ route('regiao.relatorio.estatisticatotalmembros') }}">Quantidade de
                                     Membros</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/historiconomeacoes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-hitorico-nomeacoes'))
                                 <a href="{{ route('regiao.estatistica.historiconomeacoes') }}">Histórico de
                                     Nomeacões</a>
                             @endif
                         </li>

                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-distrito'))
                                 <span>Totalização</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totaldistritoregiao') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-distrito'))
                                 <a href="{{ route('regiao.totalizacao.totaldistritoregiao') }}">
                                     Distrito por Região</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totaligrejasdistritos') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-igrejas'))
                                 <a href="{{ route('regiao.totalizacao.totaligrejasdistritos') }}">
                                     Igrejas por Distrito</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalcongregacoesigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-congregacoes-igrejas'))
                                 <a href="{{ route('regiao.totalizacao.totalcongregacoesigrejas') }}">
                                     Congregações por Igrejas</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/totalcongregacoesdistritos') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-congregacoes-distrito'))
                                 <a href="{{ route('regiao.totalizacao.totalcongregacoesdistritos') }}"> Congregações
                                     por Distritos</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalfrentemissionaria') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-frente-missionaria'))
                                 <a href="{{ route('regiao.totalizacao.totalfrentemissionaria') }}"> Frentes
                                     Missionárias</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-membros'))
                                 <span>Os 10 Distritos</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaisbatismo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-batismo'))
                                 <a href="{{ route('regiao.dezmais.distritomaisbatismo') }}"> Mais
                                     Batizaram
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaismembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-membros'))
                                 <a href="{{ route('regiao.dezmais.distritomaismembros') }}"> Mais Números
                                     de Membros
                                 </a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaiscrescerammembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-crescimento'))
                                 <a href="{{ route('regiao.dezmais.distritomaiscrescerammembros') }}">
                                     Mais Cresceram em Membros
                                 </a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-batismo'))
                                 <span>Os 10 Igrejas</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaisbatismo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-batismo'))
                                 <a href="{{ route('regiao.dezmais.igrejamaisbatismo') }}"> Mais
                                     Batizaram
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaismembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-membros'))
                                 <a href="{{ route('regiao.dezmais.igrejamaismembros') }}"> Mais Números
                                     de Membros
                                 </a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaiscrescerammembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-crescimento'))
                                 <a href="{{ route('regiao.dezmais.igrejamaiscrescerammembros') }}">
                                     Mais Cresceram em Membros
                                 </a>
                             @endif
                         </li>

                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-situacao'))
                                 <span> Clérigos
                                     por situação</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosnomeacoes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-situacao'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosnomeacoes') }}">Nomeação
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosstatus') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-status'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosstatus') }}">Status
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosfaxiaetaria') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-faixa-etaria'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosfaxiaetaria') }}">Faixa
                                     etária
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosporvinculo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-tipo-vinculo'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosporvinculo') }}">Tipo de
                                     vínculo
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/ticketmedio') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-ticket-medio'))
                                 <a href="{{ route('regiao.estatistica.ticketmedio') }}">Ticket Médio
                                 </a>
                             @endif
                         </li>
                         <!--  <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-total'))
<a href="#">Total de Membros</a>
@endif
                     </li>
                     <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-recebimento-saida'))
<a href="#">Recebimento / Saída</a>
@endif
                     </li> -->
                         <!--  <li class="submenu-fixo mt-3 mb-3">
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
<span>Clérigos</span>
@endif
                     </li> -->
                         <!-- <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-faixa-etaria'))
<a href="#">Faixa Etária</a>
@endif
                     </li>

                     <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-recebimento-desligamento'))
<a href="#">Recebimento / Saída</a>
@endif
                     </li> -->
                     </ul>
                 </li>
             @endif
            <li class="menu mx-3">
                @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                    <a href="">Contabilidade</a>
                @endif
             @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
             <li class="menu x-2">
                 <a href="#contabilidade" data-toggle="collapse" aria-expanded="{{Request::is('contabilidade/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>SRA</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('contabilidade/*') ? 'collapse show' : '' }}" id="contabilidade" data-parent="#contabilidade">
                     <li {!! Request::is('contabilidade/irrf') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('contabilidade-irrf'))
                             <a href="{{ route('contabilidade.irrf') }}">IRRF</a>
                         @endif
                     </li>
                    <li {!! Request::is('contabilidade/financeiro/balancete') ? 'class="active"' : '' !!}>
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('contabilidade-irrf'))
                            <a href="{{ route('contabilidade.relatorio-balancete') }}">Balancete</a>
                        @endif
                    </li>
                 </ul>
                 @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('congregacao-index'))
                 <li class="menu {{ Request::is('congregacao','congregacao/*') ? 'active' : '' }} ">
                     <a href="/congregacao" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>Congregações</span>
                         </div>
                     </a>
                 </li>
             @endif

              @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu'))
                 <li class="menu">
                     <a href="#gceu" data-toggle="collapse" aria-expanded="{{Request::is('gceu/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                             </svg>
                             <span>GCEU</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('gceu/*') ? 'collapse show' : '' }}" id="gceu" data-parent="#gceu">
                         <li {!! Request::is('gceu/lista') || Request::is('gceu/editar*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-lista'))
                                 <a href="{{ route('gceu.index') }}">Cadastro</a>
                             @endif
                         </li>
                         <!-- <li {!! Request::is('gceu/lista/teste') || Request::is('gceu/editar*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-lista-teste'))
                                 <a href="{{ route('gceu.index.teste') }}">TESTE</a>
                             @endif
                         </li> -->
                         <li {!! Request::is('gceu/membros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-membros'))
                                 <a href="{{ route('gceu.membros') }}">Membros</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/carta-pastoral*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-carta-pastoral'))
                                 <a href="{{ route('gceu.carta-pastoral') }}">Carta Pastoral</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/diario*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-diario'))
                                 <a href="{{ route('gceu.diario') }}">Diário</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-relatorios'))
                                <span>Relatórios</span>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-gceu*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-gceu'))
                                <a href="{{ route('gceu.relatorio.gceu') }}">Lista de GCEU</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-carta-pastoral*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-carta-pastoral-relatorio'))
                                 <a href="{{ route('gceu.carta-pastoral-relatorio') }}">Carta Pastoral</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-diario*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-relatorio-diario'))
                                <a href="{{ route('gceu.diario-relatorio') }}">Diário</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-funcoes*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-funcoes'))
                                <a href="{{ route('gceu.relatorio.funcoes') }}">Lista de Funções</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-aniversariantes*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-aniversariantes'))
                                <a href="{{ route('gceu.relatorio.aniversariantes') }}">Aniversariantes</a>
                            @endif
                         </li>
                     </ul>
                 </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-gestao-igrejas'))
                 <li class="menu {{ Request::is('igreja', 'igreja/*') ? 'active' : '' }}">
                     <a href="/igreja" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>Igrejas</span>
                         </div>
                     </a>
                 </li>
             @endif
             {{-- @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-gestao-igrejas'))
                 <li class="menu {{ Request::is('igreja', 'igreja/*') ? 'active' : '' }}">
                     <a href="/igreja" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>Igrejas</span>
                         </div>
                     </a>
                 </li>
             @endif --}}
             
             <li class="menu menu-heading">
                 <div class="heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                         <circle cx="12" cy="12" r="10"></circle>
                     </svg><span>APLICAÇÃO</span></div>
             </li>

             <li class="menu">
                 <a target="_blank" href="{{ route('selecionarPerfil') }}" aria-expanded="false"
                     class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-map">
                             <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                             <line x1="8" y1="2" x2="8" y2="18"></line>
                             <line x1="16" y1="6" x2="16" y2="22"></line>
                         </svg>
                         <span>Trocar Instituição</span>
                     </div>
                 </a>
             </li>

             <li class="menu">
                 <a href="#segurancaLocal" data-toggle="collapse" aria-expanded="{{ Request::is('seguranca/*', 'auditorias*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-lock">
                             <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                             <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                         </svg>
                         <span>Segurança</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('seguranca/*', 'auditorias*') ? 'collapse show' : '' }}" id="segurancaLocal" data-parent="#segurancaLocal">
                     @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-usuarios-instituicao'))
                         @php
                             // Obtém o perfil_id da sessão
                             $perfilID = session('session_perfil')->perfil_id;

                             // Recupera o nível do perfil baseado no perfil_id
                             $perfilUsuario = \App\Models\Perfil::where('id', $perfilID)->first();

                             // Verifica o nível do perfil
                             $hrefRoute =
                                 $perfilUsuario && $perfilUsuario->nivel === 'S'
                                     ? route('admin.index')
                                     : route('usuarios.index');
                         @endphp

                         <li  {!! Request::is('seguranca/users', 'seguranca/users/*') ? 'class="active"' : '' !!}>
                             <a href="{{ $hrefRoute }}"> Gerenciar usuários</a>
                         </li>

                         @if (auth()->check() && auth()->user()->hasPerfilRegra('auditoria'))
                             <li {!! Request::is('auditorias*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('auditorias.index') }}"> Auditorias</a>
                             </li>
                         @endif
                     @endif

                 </ul>

                 <a href="#perfil" data-toggle="collapse" aria-expanded="{{Request::is('usuario/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-user">
                             <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                             <circle cx="12" cy="7" r="4"></circle>
                         </svg>
                         <span>Perfil</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('usuario/*') ? 'collapse show' : '' }}" id="perfil" data-parent="#perfil">
                     <li {!! Request::is('usuario/perfis') ? 'class="active"' : '' !!}>
                         <a href="{{ route('perfil.index') }}"> Dados Pessoais</a>
                     </li>
                     <li {!! Request::is('usuario/perfil/carteira-digital') ? 'class="active"' : '' !!}>
                        <a href="{{ route('perfil.carteira-digital') }}"> Carteira Digital</a>
                    </li>
                     @if (auth()->user()->pessoa_id)
                         <li {!! Request::is('usuario/clerigos/perfil/dependentes') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.dependentes.index') }}"> Dependentes</a>
                         </li>
                         <li {!! Request::is('usuario/clerigos/perfil/prebendas') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.prebendas.index') }}"> Prebendas</a>
                         </li>
                         <li  {!! Request::is('usuario/clerigos/perfil/imposto-de-renda') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.impostoDeRenda.index') }}"> Imposto de Renda</a>
                         </li>
                         @if (App\Services\InformeRendimentos\ChecaArquivoExistenteService::execute(date('Y')))
                             <li>
                                 <a href="{{ route('informe_rendimentos.exibirPdf', ['ano' => date('Y')]) }}"
                                     target="_blank">Informe de Rendimentos</a>
                             </li>
                         @endif
                     @endif
                 </ul>
             </li>

         </ul>

     </nav>
 </div>
 <!--  END SIDEBAR  -->
