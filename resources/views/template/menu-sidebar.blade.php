 <style>
     #sidebar ul.menu-categories li.menu>#notificacao {
         background-color: inherit !important;
         color: inherit !important;
     }

     .comunicacao-badge {
         display: inline-flex;
         align-items: center;
         justify-content: center;
         min-width: 18px;
         height: 18px;
         padding: 0 5px;
         margin-left: 6px;
         border-radius: 9999px;
         background: #ff4d4f;
         color: #fff;
         font-size: 11px;
         font-weight: 700;
         line-height: 1;
         box-shadow: 0 0 0 rgba(255, 77, 79, 0.7);
         animation: comunicacaoPulse 1.4s infinite;
         vertical-align: middle;
     }

     @keyframes comunicacaoPulse {
         0% {
             transform: scale(0.95);
             box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.7);
         }
         70% {
             transform: scale(1);
             box-shadow: 0 0 0 10px rgba(255, 77, 79, 0);
         }
         100% {
             transform: scale(0.95);
             box-shadow: 0 0 0 0 rgba(255, 77, 79, 0);
         }
     }

     /* Menu com barra sempre visível quando houver overflow */
     #sidebar ul.menu-categories {
         overflow-y: auto !important;
         scrollbar-width: thin;
         scrollbar-color: #8a8fa8 rgba(255, 255, 255, 0.06);
     }

     #sidebar ul.menu-categories::-webkit-scrollbar {
         width: 8px;
     }

     #sidebar ul.menu-categories::-webkit-scrollbar-track {
         background: rgba(255, 255, 255, 0.06);
     }

     #sidebar ul.menu-categories::-webkit-scrollbar-thumb {
         background: #8a8fa8;
         border-radius: 8px;
     }

     /* Esconde a trilha overlay do PerfectScrollbar nesse menu */
     #sidebar ul.menu-categories.ps > .ps__rail-y {
         display: none !important;
     }
 </style>
 <!--  BEGIN SIDEBAR  -->
 <div class="sidebar-wrapper sidebar-theme">
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
                     <img src="{{ asset('theme/assets/img/perfil.png') }}" alt="{{ __('User') }}"
                         style="width: 50px; height: 50px; border-radius: 50%;">
                 </div>
                 <div class="user-details" style="text-align: center;">
                     <b class="text-bold text-white" style="font-size: 12px ;">{{ __($firstName) }}
                         {{ __($lastName) }}</b><br>
                     @if (session('session_perfil'))
                         <span style="font-size: 12px ;">{{ __(session('session_perfil')->perfil_nome) }}</span> <br>
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
                         <span>{{ __('Dashboard') }}</span>
                     </div>
                 </a>
             </li>
              @if (auth()->check() && auth()->user()->hasPerfilRegra('comunicacao'))
                  <li class="menu {{ Request::is('comunicacao*') ? 'active' : '' }}">
                     <a href="{{ route('comunicacao.index') }}" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-message-square">
                             <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                             </svg>
                             <span>{{ __('Comunicação') }}</span>
                             @if (!empty($baseParams->quantidadeNovasComunicacoes))
                                 <span class="comunicacao-badge" title="{{ __('Novas comunicações') }}">
                                     {{ (int) $baseParams->quantidadeNovasComunicacoes > 99 ? '99+' : (int) $baseParams->quantidadeNovasComunicacoes }}
                                 </span>
                             @endif
                         </div>
                     </a>
                 </li>
            @endif
            @if (auth()->check() && auth()->user()->hasPerfilRegra('evento'))
                 <li class="menu {{ Request::is('eventos*') ? 'active' : '' }}">
                     <a href="#eventos-menu" data-toggle="collapse" aria-expanded="{{ Request::is('eventos*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="feather feather-calendar">
                                 <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                 <line x1="16" y1="2" x2="16" y2="6"></line>
                                 <line x1="8" y1="2" x2="8" y2="6"></line>
                                 <line x1="3" y1="10" x2="21" y2="10"></line>
                             </svg>
                             <span>{{ __('Eventos') }}</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('eventos*') ? 'collapse show' : '' }}" id="eventos-menu" data-parent="#accordionExample">
                         <li {!! Request::is('eventos') || Request::is('eventos/novo') || Request::is('eventos/detalhes*') || Request::is('eventos/editar*') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.index') }}">{{ __('Eventos') }}</a>
                         </li>
                         <li {!! Request::is('eventos/agenda') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.agenda') }}">{{ __('Agenda de Eventos') }}</a>
                         </li>
                         <li {!! Request::is('eventos/presenca') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.presenca') }}">{{ __('Presença do Evento') }}</a>
                         </li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-funcao'))
                             <li {!! Request::is('eventos/funcoes*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('eventos.funcoes.index') }}">{{ __('Funções Eventos') }}</a>
                             </li>
                         @endif
                         <li class="submenu-fixo mt-3 mb-3">
                             <span>{{ __('Relatórios') }}</span>
                         </li>
                         <li {!! Request::is('eventos/relatorio/eventos') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.relatorio') }}">{{ __('Eventos') }}</a>
                         </li>
                         <li {!! Request::is('eventos/relatorio/pessoas') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.relatorio.pessoas') }}">{{ __('Pessoas do Evento') }}</a>
                         </li>
                         <li {!! Request::is('eventos/relatorio/inscritos') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.relatorio.inscritos') }}">{{ __('Inscritos no Evento') }}</a>
                         </li>
                         <li {!! Request::is('eventos/relatorio/presencas') ? 'class="active"' : '' !!}>
                             <a href="{{ route('eventos.relatorio.presencas') }}">{{ __('Histórico de Presença') }}</a>
                         </li>
                     </ul>
                 </li>
            @endif
            @if (auth()->check() && auth()->user()->hasPerfilRegra('categoria-comunicacao'))
                 <li class="menu {{ Request::is('categoria-comunicacao*') ? 'active' : '' }}">
                     <a href="{{ route('categoria-comunicacao.index') }}" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-tag">
                                 <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"></path>
                                 <line x1="7" y1="7" x2="7.01" y2="7"></line>
                             </svg>
                             <span>{{ __('Categoria Comunicação') }}</span>
                         </div>
                     </a>
                 </li>
                  @endif
             @if (optional($baseParams->notificacoesTransferencia)->count())
                 <li class="menu container-fluid col-xs-4">
                     <a href="{{ route('notificacoes-tranferencia.index') }}" aria-expanded="false"
                         class="dropdown-toggle" id="notificacao">
                         <span class="badge badge-secondary" style="padding: 6px; border-radius: 6px;">{{ __('Nova Notificação') }}
                         </span>
                     </a>
                 </li>
             @endif

             <li class="menu menu-heading">
                 <div class="heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                         <circle cx="12" cy="12" r="10"></circle>
                     </svg><span>{{ __('MENU PRINCIPAL') }}</span></div>
             </li>
             @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-secretaria') || auth()->user()->hasPerfilRegra('membresia-validacao'))
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
                             <span>{{ __('Secretaria') }}</span>
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
                                 <a href="{{ route('membro.index') }}">{{ __('Membros') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/recadastramento-membro*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('membresia-validacao'))
                                 <a href="{{ route('recadastramento-membro.indexRecadastramento') }}">{{ __('Membresia Validação') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/congregado*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('congregados-index'))
                                 <a href="{{ route('congregado.index') }}">{{ __('Congregados') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/visitante*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('visitantes-index'))
                                 <a href="{{ route('visitante.index') }}">{{ __('Visitantes') }}</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-secretaria'))
                                 <span>{{ __('Relatórios') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membresia') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-membresia'))
                                 <a href="{{ route('relatorio.membresia') }}">{{ __('Membresia') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membros-por-bairro') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-membros-por-bairro'))
                                 <a href="{{ route('relatorio.membros-por-bairro') }}">{{ __('Membros por Bairro') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/aniversariantes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-aniversariantes'))
                                 <a href="{{ route('relatorio.aniversariantes') }}">{{ __('Aniversariantes') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/conjuges') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-conjuges'))
                                 <a href="{{ route('relatorio.conjuges') }}">{{ __('Cônjuges') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/congregados') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && (auth()->user()->hasPerfilRegra('relatorio-congregados') || auth()->user()->hasPerfilRegra('menu-relatorios-secretaria')))
                                 <a href="{{ route('relatorio.congregados') }}">{{ __('Congregados') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/familia') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-familia'))
                                 <a href="{{ route('relatorio.familia') }}">Família</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membros-por-ministerios') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-historico-eclesiastico'))
                                 <a href="{{ route('relatorio.membros-por-ministerios') }}">{{ __('Membros por Ministérios') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/historico-eclesiastico') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-historico-eclesiastico'))
                                 <a href="{{ route('relatorio.historico-eclesiastico') }}">{{ __('Função Ministerial') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/membros-disciplinados') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-membro-disciplinado'))
                                 <a href="{{ route('relatorio.membros-disciplinados') }}">{{ __('Membros Disciplinados') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('secretaria/relatorio/funcao-eclesiastica') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-funcao-eclesiastica'))
                                 <a href="{{ route('relatorio.funcao-eclesiastica') }}">{{ __('Função Eclesiástica') }}</a>
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
                             <span>{{ __('Instituicões') }}</span>
                         </div>

                     </a>
                 </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                 <li class="menu mx-3">
                     <a href="">{{ __('Igrejas') }}</a>
                 </li>
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
                             <span>{{ __('Igrejas') }}</span>
                         </div>
                     </a>
                 </li>

                 {{-- Menu Clérigos --}}
                 <li class="menu mx-3">
                     <a href="">{{ __('Clérigos') }}</a>
                 </li>
                 <li class="menu x-2">
                     <a href="#clerigos" data-toggle="collapse" aria-expanded="{{ Request::is('clerigos*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-users">
                                 <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                 <circle cx="12" cy="7" r="4"></circle>
                                 <path d="M16 21v-2a4 4 0 0 0-3-3.87"></path>
                                 <path d="M8 21v-2a4 4 0 0 1 3-3.87"></path>
                             </svg>
                             <span>{{ __('Clérigos') }}</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('clerigos*') ? 'collapse show' : '' }}" id="clerigos" data-parent="#accordionExample">
                         <li {!! Request::is('clerigos', 'clerigos/nomeacoes/*', 'clerigos/novo*', 'clerigos/editar/*') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.index') }}">{{ __('Clérigos') }}</a>
                         </li>
                         <li {!! Request::is('clerigos/prebendas','clerigos/prebendas/*') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.prebendas.index') }}">{{ __('Prebendas') }}</a>
                         </li>
                     </ul>
                 </li>
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
                         <span>{{ __('Financeiro') }}</span>
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
                             <a href="{{ route('financeiro.movimento.caixa') }}">{{ __('Movimento de Caixa') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/consolidar-caixa') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-consolidarcaixa'))
                             <a href="{{ route('financeiro.consolidar.caixa') }}">{{ __('Consolidação de Caixa') }}</a>
                         @endif
                     </li>
                      <li {!! Request::is('financeiro/cota-orcamentaria') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('cota-orcamentaria'))
                             <a href="{{ route('financeiro.cota.orcamentaria') }}">{{ __('Cota Orçamentária') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/plano-conta') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-planoconta'))
                             <a href="{{ route('financeiro.plano.conta') }}">{{ __('Plano Conta') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/caixas') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-caixas'))
                             <a href="{{ route('financeiro.caixas') }}">{{ __('Caixas') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/fornecedor', 'financeiro/fornecedor/*') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('fornecedores-index'))
                             <a href="{{ route('fornecedor.index') }}">{{ __('Fornecedores') }}</a>
                         @endif
                     </li>
                     <li class="submenu-fixo mt-3 mb-3">
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-financeiro'))
                             <span>{{ __('Relatórios') }}</span>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/movimento-diario') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-movimento-diario'))
                             <a href="{{ route('financeiro.relatorio-movimento-diario') }}">{{ __('Movimento Diário') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrocaixa') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrocaixa'))
                             <a href="{{ route('financeiro.relatorio-livrocaixa') }}">{{ __('Livro Caixa') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/balancete') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-balancete'))
                             <a href="{{ route('financeiro.relatorio-balancete') }}">{{ __('Balancete') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrograde') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrograde'))
                             <a href="{{ route('financeiro.relatorio-livrograde') }}">{{ __('Livro Grade') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/livrorazao') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-livrorazao'))
                             <a href="{{ route('financeiro.relatorio-livrorazao') }}">{{ __('Livro Razão') }}</a>
                         @endif
                     </li>
                     <li {!! Request::is('financeiro/relatorio/movimento-bancario') ? 'class="active"' : '' !!}>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-movimento-bancario'))
                             <a href="{{ route('financeiro.movimento-bancario') }}">{{ __('Movimento Bancário') }}</a>
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
                             <span>{{ __('Relatórios Distritais') }}</span>
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
                                 <span>{{ __('Financeiro') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/lancamentodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-lancamento-das-igrejas'))
                                 <a href="{{ route('distrito.relatorio.lancamentodasigrejas') }}">{{ __('Lançamento das Igrejas') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/saldodasigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-saldo-das-igrejas'))
                                 <a href="{{ route('distrito.relatorio.saldodasigrejas') }}">{{ __('Saldo de Caixas') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/livrorazaogeral') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-livro-razao-geral'))
                                 <a href="{{ route('distrito.relatorio.livrorazaogeral') }}">{{ __('Livro Razão Geral') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/orcamento') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-orcamento'))
                                 <a href="{{ route('distrito.relatorio.orcamento') }}">{{ __('Orçamento') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/variacaofinanceira') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-variacao-financeira'))
                                 <a href="{{ route('distrito.relatorio.variacaofinanceira') }}">{{ __('Variação Financeira') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/cota-orcamentaria') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-cota-orcamentaria'))
                                <a href="{{ route('distrito.cota.orcamentaria') }}">{{ __('Cota Orçamentária') }}</a>
                            @endif
                        </li>
                        <li {!! Request::is('distrito/recursos-humanos') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-recurso-humano'))
                                <a href="{{ route('distrito.recurso.humano') }}">{{ __('Recursos humanos') }}</a>
                            @endif
                        </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio'))
                                 <span>{{ __('Membresia') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/membrosministerio') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-membros-ministerio'))
                                <a href="{{ route('distrito.relatorio.membrosministerio') }}">{{ __('Membros por Ministério') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/quantidademembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-quantidade-membros'))
                                 <a href="{{ route('distrito.relatorio.quantidademembros') }}">{{ __('Quantidade de Membros') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/conjuges') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio-conjuges'))
                                 <a href="{{ route('distrito.relatorio.conjuges') }}">{{ __('Cônjuges') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/congregados') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && (auth()->user()->hasPerfilRegra('distrito-menu-relatorio-congregados') || auth()->user()->hasPerfilRegra('distrito-menu-relatorio')))
                                 <a href="{{ route('distrito.relatorio.congregados') }}">{{ __('Congregados') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/estatisticagenero') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-estatistica-genero'))
                                 <a href="{{ route('distrito.relatorio.estatisticagenero') }}">{{ __('Estatística por Gênero') }}</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-relatorio'))
                                 <span>{{ __('Estatísticas') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/relatorio/estatisticas-gceu') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-estatistica-gceu'))
                                 <a href="{{ route('distrito.relatorio.estatisticas.gceu') }}">{{ __('Estatísticas GCEU') }}</a>
                             @endif
                         </li>


                         @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-clerigos-aniversariantes'))
                            <li class="submenu-fixo mt-3 mb-3">
                                        <span>{{ __('Clérigos') }}</span>
                            </li>
                            <li {!! Request::is('regiao/relatorio/clerigos-aniversariantes') ? 'class="active"' : '' !!}>
                                        <a href="{{ route('distrito.relatorio.clerigosaniversariantes') }}">{{ __('Clérigos Aniversariantes') }}</a>
                            </li>
                         @endif
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-menu-igrejas'))
                                 <span>{{ __('Igrejas') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/congregacoes-por-igrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-congregacoes-igrejas'))
                                 <a href="{{ route('distrito.relatorio.congregacaoporigreja') }}">{{ __('Congregações por Igrejas') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('distrito/apirantes-por-igrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-relatorio-aspirantes-igrejas'))
                                 <a href="{{ route('distrito.relatorio.apirateporigreja') }}">{{ __('Aspirantes por Igrejas') }}</a>
                             @endif
                         </li>

                        @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-relatorios'))
                            <li class="submenu-fixo mt-3 mb-3">
                                <span>{{ __('GCEU') }}</span>
                            </li>

                            <li {!! Request::is('distrito/relatorio-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-gceu'))
                                    <a href="{{ route('distrito.relatorio.gceu') }}">{{ __('Lista GCEU') }}</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-carta-pastoral-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-relatorio-carta-pastoral'))
                                    <a href="{{ route('distrito.relatorio.carta-pastoral-distrito') }}">{{ __('Carta Pastoral') }}</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-funcoes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-funcoes'))
                                    <a href="{{ route('distrito.relatorio.funcoes.gceu') }}">{{ __('Lista Função') }}</a>
                                @endif
                            </li>

                            <li {!! Request::is('distrito/relatorio-aniversariantes-gceu') ? 'class="active"' : '' !!}>
                                @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-distrito-lista-aniversariantes'))
                                    <a href="{{ route('distrito.relatorio.aniversariantes.gceu') }}">{{ __('Lista Aniversariantes') }}</a>
                                @endif
                            </li>
                        @endif
                     </ul>
                 </li>
             @endif

             @if (auth()->check() && (auth()->user()->hasPerfilRegra('regiao-menu-relatorio')))
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
                             <span>{{ __('Relatórios Regionais') }}</span>
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
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-financeiro'))
                                 <span>{{ __('Contabilidade') }}</span>
                             @endif
                         </li> -->
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-financeiro'))
                         <li class="submenu-fixo mt-3 mb-3">

                                 <span>{{ __('Financeiro') }}</span>

                         </li>
                         @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-lancamento-das-igrejas'))
                         <li {!! Request::is('regiao/relatorio/irrf') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.irrf') }}">{{ __('IRRF Repasse') }}</a>

                         </li>
                         @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-lancamento-das-igrejas'))
                         <li {!! Request::is('regiao/relatorio/lancamentodasigrejas') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.lancamentodasigrejas') }}">{{ __('Lançamento das Igrejas') }}</a>

                         </li>
                         @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-relatorio-balancete'))
                         <li {!! Request::is('regiao/relatorio/financeiro/balancete') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio-balancete-regiao') }}">{{ __('Balancete') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-saldo-das-igrejas'))
                         <li {!! Request::is('regiao/relatorio/saldodasigrejas') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.saldodasigrejas') }}">{{ __('Saldo de Caixas') }}</a>

                         </li>
                        @endif
                          @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-livro-razao-geral'))
                         <li {!! Request::is('regiao/relatorio/livrorazaogeral') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.livrorazaogeral') }}">{{ __('Livro Razão Geral') }}</a>

                         </li>
                        @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-orcamento'))
                         <li {!! Request::is('regiao/relatorio/orcamento') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.orcamento') }}">{{ __('Orçamento') }}</a>

                         </li>
                         @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-variacao-financeira'))
                         <li {!! Request::is('regiao/relatorio/variacaofinanceira') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.variacaofinanceira') }}">{{ __('Variação Financeira') }}</a>

                         </li>
                         @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('financeiro-por-categoria'))
                         <li {!! Request::is('regiao/relatorio/financeiro-por-categoria') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.financeiroPorCategoria') }}">{{ __('Financeiro por Categoria') }}</a>

                         </li>
                         @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-financeiro'))
                         <li {!! Request::is('regiao/relatorio/cota-orcamentaria') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.cota.orcamentaria') }}">{{ __('Cota Orçamentária') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-ebd'))
                        <li class="submenu-fixo mt-3 mb-3">

                        <span>{{ __('EBD') }}</span>

                        </li>
                        @endif

                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-dashboardo'))
                        <li {!! Request::is('regiao/relatorio/ebd/dashboard') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.dashboard') }}">{{ __('Dashboard') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-estatisticas'))
                        <li {!! Request::is('regiao/relatorio/ebd/estatisticas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.estatisticas') }}">Estatísticas EBD</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-turmas'))
                        <li {!! Request::is('regiao/relatorio/ebd/turmas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.turmas') }}">EBDs</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-alunos'))
                        <li {!! Request::is('regiao/relatorio/ebd/alunos') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.alunos') }}">Alunos</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-professores'))
                        <li {!! Request::is('regiao/relatorio/ebd/professores') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.professores') }}">Professores</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-liderancas'))
                        <li {!! Request::is('regiao/relatorio/ebd/liderancas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.liderancas') }}">Liderança</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-classes'))
                        <li {!! Request::is('regiao/relatorio/ebd/classes') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.classes') }}">Classes</a>
                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-diarios'))
                        <li {!! Request::is('regiao/relatorio/ebd/diarios') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.ebd.diarios') }}">Diário</a>
                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-agendas'))
                        <li {!! Request::is('regiao/relatorio/ebd/agendas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.agendas') }}">Agenda</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-ebd-geral'))
                        <li {!! Request::is('regiao/relatorio/ebd/geral') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.ebd.geral') }}">Relatório Geral EBD</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-membros-ministerio'))
                         <li class="submenu-fixo mt-3 mb-3">

                                 <span>{{ __('Membresia') }}</span>

                         </li>
                         @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-membros-ministerio'))
                         <li {!! Request::is('regiao/relatorio/membrosministerio') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.membrosministerio') }}">{{ __('Membros por Ministério') }}</a>

                         </li>
                         @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-quantidade-membros'))
                         <li {!! Request::is('regiao/relatorio/quantidademembros') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.quantidademembros') }}">{{ __('Quantidade de Membros') }}</a>

                         </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-conjuges'))
                         <li {!! Request::is('regiao/relatorio/conjuges') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.conjuges') }}">{{ __('Cônjuges') }}</a>

                         </li>
                        @endif
                        @if (auth()->check() && (auth()->user()->hasPerfilRegra('regiao-menu-relatorio-congregados') || auth()->user()->hasPerfilRegra('regiao-menu-relatorio')))
                         <li {!! Request::is('regiao/relatorio/congregados') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.congregados') }}">{{ __('Congregados') }}</a>

                         </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-acompanhamento-validacoes'))
                         <li {!! Request::is('regiao/relatorio/acompanhamento-validacoes') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('regiao.relatorio.acompanhamento-validacoes') }}">{{ __('Validação de Membros') }}</a>
                         </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-perfil-membros-recebidos'))
                         <li {!! Request::is('regiao/relatorio/perfil-membros-recebidos') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('regiao.relatorio.perfil-membros-recebidos') }}">{{ __('Perfil dos Membros Recebidos') }}</a>
                         </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-perfil-membros-excluidos'))
                         <li {!! Request::is('regiao/relatorio/perfil-membros-excluidos') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('regiao.relatorio.perfil-membros-excluidos') }}">{{ __('Perfil dos Membros Excluídos') }}</a>
                         </li>
                        @endif
                          @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-estatistica-genero'))
                         <li {!! Request::is('regiao/relatorio/estatisticagenero') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.estatisticagenero') }}">{{ __('Estatística por Gênero') }}</a>

                         </li>
                         @endif
                          @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-ano-eclesiastico'))
                         <li {!! Request::is('regiao/relatorio/ano-eclesiastico') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.relatorio.ano.eclesiastico') }}">{{ __('Mapa Estatístico Membros') }}</a>

                         </li>
                         @endif
                          @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-instituicoes-igrejas'))
                        <li class="submenu-fixo mt-3 mb-3">

                                <span>{{ __('Clérigos') }}</span>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-aniversariantes'))
                        <li {!! Request::is('regiao/relatorio/clerigos-aniversariantes') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigosaniversariantes') }}">{{ __('Clérigos Aniversariantes') }}</a>

                        </li>
                        @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-dados'))
                        <li {!! Request::is('regiao/relatorio/esposas-de-pastores') ? 'class="active"' : '' !!}>
                            <a href="{{ route('regiao.relatorio.esposas-de-pastores') }}">{{ __('Cônjuges dos Clérigos') }}</a>
                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-dados'))
                        <li {!! Request::is('regiao/relatorio/clerigos-dados') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigosdados') }}">{{ __('Clérigos Documentação') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-categoria'))
                        <li {!! Request::is('regiao/relatorio/clerigos-categorias') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigoscategoria') }}">{{ __('Clérigos Categorias') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-status'))
                        <li {!! Request::is('regiao/relatorio/clerigos-status') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigosstatus') }}">{{ __('Clérigos Status') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('relatorio-clerigos-vinculos'))
                        <li {!! Request::is('regiao/relatorio/clerigos-vinculos') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigosvinculos') }}">{{ __('Clérigos por Vínculo') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-hitorico-nomeacoes'))
                         <li {!! Request::is('regiao/relatorio/historiconomeacoes') ? 'class="active"' : '' !!}>

                                 <a href="{{ route('regiao.estatistica.historiconomeacoes.regionais') }}">{{ __('Histórico de Nomeacões') }}</a>

                         </li>
                         @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('menu-relatorios-congregacoes-igrejas'))
                         <li class="submenu-fixo mt-3 mb-3">

                                <span>{{ __('Igrejas') }}</span>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-congregacoes-igrejas'))
                        <li {!! Request::is('regiao/relatorio/congregacoes-por-igrejas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.congregacaoporigreja') }}">{{ __('Congregações por Igrejas') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-cnpj-igreja'))
                        <li {!! Request::is('regiao/relatorio/cnpj-igrejas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.cnpj.igreja') }}">{{ __('CNPJ por Igreja') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-contato-igreja'))
                        <li {!! Request::is('regiao/relatorio/contato-igrejas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.contato.igreja') }}">{{ __('Contatos por Igreja') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-conta-bancaria-igreja'))
                        <li {!! Request::is('regiao/relatorio/conta-bancaria-igrejas') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.conta.bancaria.igreja') }}">{{ __('Conta Bancária por Igreja') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-congregacoes-igrejas'))
                        <li {!! Request::is('regiao/relatorio/igrejas-por-clerigos') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.igrejas.clerigos') }}">{{ __('Igrejas por Clérigos') }}</a>

                        </li>
                        @endif
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-congregacoes-igrejas'))
                        <li {!! Request::is('regiao/relatorio/clerigo-por-igreja') ? 'class="active"' : '' !!}>

                                <a href="{{ route('regiao.relatorio.clerigo.por.igreja') }}">{{ __('Clérigo por Igreja') }}</a>

                        </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio-patrimonio'))
                            <li class="submenu-fixo mt-3 mb-3">
                                <span>{{ __('Patrimônio') }}</span>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/imoveis_cadastrados') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'imoveis_cadastrados']) }}">{{ __('Imóveis cadastrados') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/bens_moveis_cadastrados') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'bens_moveis_cadastrados']) }}">{{ __('Bens móveis cadastrados') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/imoveis_regularizacao_pendente') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'imoveis_regularizacao_pendente']) }}">{{ __('Regularização pendente') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/documentos_vencidos') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'documentos_vencidos']) }}">{{ __('Documentos vencidos') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/avcb_vencido') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'avcb_vencido']) }}">{{ __('AVCB vencido') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/bens_depreciados') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'bens_depreciados']) }}">{{ __('Bens depreciados') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/baixas_patrimoniais') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'baixas_patrimoniais']) }}">{{ __('Baixas patrimoniais') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/valor_total_por_categoria') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'valor_total_por_categoria']) }}">{{ __('Valor total por categoria') }}</a>
                            </li>
                            <li {!! Request::is('regiao/relatorio/patrimonio/bens_por_igreja_unidade') ? 'class="active"' : '' !!}>
                                <a href="{{ route('regiao.relatorio.patrimonio.lista', ['relatorio' => 'bens_por_igreja_unidade']) }}">{{ __('Bens por igreja/unidade') }}</a>
                            </li>
                        @endif
                        @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-relatorios'))
                            <li class="submenu-fixo mt-3 mb-3">
                                <span>{{ __('GCEU') }}</span>
                            </li>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-gceu'))
                            <li {!! Request::is('regiao/relatorio/gceu') ? 'class="active"' : '' !!}>

                                    <a href="{{ route('regiao.relatorio.gceu') }}">{{ __('Lista GCEU') }}</a>

                            </li>
                            @endif
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-relatorio-carta-pastoral'))
                            <li {!! Request::is('regiao/relatorio/relatorio-carta-pastoral-gceu') ? 'class="active"' : '' !!}>

                                    <a href="{{ route('regiao.relatorio.carta-pastoral-regiao') }}">{{ __('Carta Pastoral') }}</a>

                            </li>
                            @endif
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-funcoes'))
                            <li {!! Request::is('regiao/relatorio/relatorio-funcoes-gceu') ? 'class="active"' : '' !!}>

                                    <a href="{{ route('regiao.relatorio.funcoes.gceu') }}">{{ __('Lista Função') }}</a>

                            </li>
                            @endif
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-regiao-lista-aniversariantes'))
                            <li {!! Request::is('regiao/relatorio/relatorio-aniversariantes-gceu') ? 'class="active"' : '' !!}>

                                    <a href="{{ route('regiao.relatorio.aniversariantes.gceu') }}">{{ __('Lista Aniversariantes') }}</a>

                            </li>
                            @endif
                        @endif
	                     </ul>
	                 </li>
	             @endif


             @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao'))
                 <li class="menu">
                     <a href="#juridico-regiao" data-toggle="collapse" aria-expanded="{{ Request::is('regiao/juridico*') ? 'true' : 'false' }}"
                         class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase">
                                 <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                 <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                             </svg>
                             <span>Jurídico</span>
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
                     <ul class="collapse submenu list-unstyled {{ Request::is('regiao/juridico*') ? 'collapse show' : '' }}" id="juridico-regiao"
                         data-parent="#juridico-regiao">
                         <li {!! Request::is('regiao/juridico/acoes*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-acoes'))
                                 <a href="{{ route('regiao.juridico.acoes.index') }}">Ações Judiciais</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/juridico/advogados*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-advogados'))
                                 <a href="{{ route('regiao.juridico.advogados.index') }}">Advogados</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-relatorios'))
                                 <span>{{ __('Relatórios') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/juridico/relatorios*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('juridico-regiao-relatorios'))
                                 <a href="{{ route('regiao.juridico.relatorios') }}">Relatórios</a>
                             @endif
                         </li>
                     </ul>
                 </li>
             @endif

	             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-estatistica'))
                 <li class="menu">
                    <a href="#estatistica-regiao" data-toggle="collapse" aria-expanded="{{ (Request::is('regiao/estatistica/*') || Request::is('regiao/relatorio/estatisticas-gceu') || Request::is('regiao/relatorio/aspirantes-por-igrejas')) ? 'true' : 'false' }}"
                         class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart">
                                 <line x1="12" y1="20" x2="12" y2="10"></line>
                                 <line x1="18" y1="20" x2="18" y2="4"></line>
                                 <line x1="6" y1="20" x2="6" y2="16"></line>
                             </svg>

                             <span>{{ __('Estatísticas') }}</span>
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
                    <ul class="collapse submenu list-unstyled {{ (Request::is('regiao/estatistica/*') || Request::is('regiao/relatorio/estatisticas-gceu') || Request::is('regiao/relatorio/aspirantes-por-igrejas')) ? 'collapse show' : '' }}" id="estatistica-regiao"
                         data-parent="#estatistica-regiao">
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-evolucao'))
                                 <span>{{ __('Membros') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/membresia*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-membresia'))
                                 <a href="{{ route('regiao.membresia') }}">{{ __('Membresia') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatistica-membros-evolucao') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-evolucao'))
                                 <a href="{{ route('regiao.estatistica.evolucao') }}">{{ __('Evolução') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatistica-total-membresia') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-total-membresia'))
                                 <a href="{{ route('regiao.estatistica.totalMembresia') }}">{{ __('Total Membresia') }}</a>
                             @endif

                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticaescolaridade') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-escolaridade'))
                                 <a href="{{ route('regiao.relatorio.estatisticaescolaridade') }}">{{ __('Escolaridade') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticaestadocivil') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-estado-civl'))
                                 <a href="{{ route('regiao.relatorio.estatisticaestadocivil') }}">{{ __('Estado Civil') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticageneroporcentagem') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-genero'))
                                 <a href="{{ route('regiao.relatorio.estatisticageneroporcentagem') }}">{{ __('Gênero') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/estatisticatotalmembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-quantidade-membros'))
                                 <a href="{{ route('regiao.relatorio.estatisticatotalmembros') }}">{{ __('Quantidade de Membros') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/estatisticas-gceu') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-gceu'))
                                 <a href="{{ route('regiao.relatorio.estatisticas.gceu') }}">{{ __('Estatísticas GCEU') }}</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-estatistica'))
                                 <span>{{ __('Igrejas') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/relatorio/aspirantes-por-igrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-relatorio-aspirantes-igrejas'))
                                 <a href="{{ route('regiao.relatorio.aspiranteporigreja') }}">{{ __('Aspirantes por Igrejas') }}</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/historiconomeacoes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-hitorico-nomeacoes'))
                                 <a href="{{ route('regiao.estatistica.historiconomeacoes') }}">{{ __('Histórico de Nomeacões') }}</a>
                             @endif
                         </li>

                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-distrito'))
                                 <span>{{ __('Totalização') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totaldistritoregiao') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-distrito'))
                                 <a href="{{ route('regiao.totalizacao.totaldistritoregiao') }}">
                                     {{ __('Distrito por Região') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totaligrejasdistritos') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-igrejas'))
                                 <a href="{{ route('regiao.totalizacao.totaligrejasdistritos') }}">
                                     {{ __('Igrejas por Distrito') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalcongregacoesigrejas') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-congregacoes-igrejas'))
                                 <a href="{{ route('regiao.totalizacao.totalcongregacoesigrejas') }}">
                                     {{ __('Congregações por Igrejas') }}</a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/totalcongregacoesdistritos') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-totalizacao-congregacoes-distrito'))
                                 <a href="{{ route('regiao.totalizacao.totalcongregacoesdistritos') }}"> {{ __('Congregações por Distritos') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalfrentemissionaria') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-frente-missionaria'))
                                 <a href="{{ route('regiao.totalizacao.totalfrentemissionaria') }}"> {{ __('Frentes Missionárias') }}</a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-membros'))
                                 <span>{{ __('Os 10 Distritos') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaisbatismo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-batismo'))
                                 <a href="{{ route('regiao.dezmais.distritomaisbatismo') }}"> {{ __('Mais Batizaram') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaismembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-membros'))
                                 <a href="{{ route('regiao.dezmais.distritomaismembros') }}"> {{ __('Mais Números de Membros') }}
                                 </a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/distritomaiscrescerammembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-distrito-crescimento'))
                                 <a href="{{ route('regiao.dezmais.distritomaiscrescerammembros') }}">
                                     {{ __('Mais Cresceram em Membros') }}
                                 </a>
                             @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-batismo'))
                                 <span>{{ __('Os 10 Igrejas') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaisbatismo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-batismo'))
                                 <a href="{{ route('regiao.dezmais.igrejamaisbatismo') }}"> {{ __('Mais Batizaram') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaismembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-membros'))
                                 <a href="{{ route('regiao.dezmais.igrejamaismembros') }}"> {{ __('Mais Números de Membros') }}
                                 </a>
                             @endif
                         </li>

                         <li {!! Request::is('regiao/estatistica/relatorio/igrejamaiscrescerammembros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-igreja-crescimento'))
                                 <a href="{{ route('regiao.dezmais.igrejamaiscrescerammembros') }}">
                                     {{ __('Mais Cresceram em Membros') }}
                                 </a>
                             @endif
                         </li>

                         <li class="submenu-fixo mt-3 mb-3">
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-situacao'))
                                 <span> {{ __('Clérigos por situação') }}</span>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosnomeacoes') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-situacao'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosnomeacoes') }}">{{ __('Nomeação') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosstatus') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-status'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosstatus') }}">{{ __('Status') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosfaxiaetaria') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-faixa-etaria'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosfaxiaetaria') }}">{{ __('Faixa etária') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/totalclerigosporvinculo') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-tipo-vinculo'))
                                 <a href="{{ route('regiao.estatisticaclerigos.totalclerigosporvinculo') }}">{{ __('Tipo de vínculo') }}
                                 </a>
                             @endif
                         </li>
                         <li {!! Request::is('regiao/estatistica/relatorio/ticketmedio') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-ticket-medio'))
                                 <a href="{{ route('regiao.estatistica.ticketmedio') }}">{{ __('Ticket Médio') }}
                                 </a>
                             @endif
                         </li>
                         <!--  <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-total'))
<a href="#">{{ __('Total de Membros') }}</a>
@endif
                     </li>
                     <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-membros-recebimento-saida'))
<a href="#">{{ __('Recebimento / Saída') }}</a>
@endif
                     </li> -->
                         <!--  <li class="submenu-fixo mt-3 mb-3">
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-menu-relatorio'))
<span>{{ __('Clérigos') }}</span>
@endif
                     </li> -->
                         <!-- <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-faixa-etaria'))
<a href="#">{{ __('Faixa Etária') }}</a>
@endif
                     </li>

                     <li>
                         @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-estatistica-clerigo-recebimento-desligamento'))
<a href="#">{{ __('Recebimento / Saída') }}</a>
@endif
                     </li> -->
                     </ul>
                 </li>
             @endif
            @if (auth()->check() && auth()->user()->hasPerfilRegra('instituicoes-igrejas'))
                <li class="menu mx-3">
                    <a href="">{{ __('Contabilidade') }}</a>
                </li>
                <li class="menu x-2">
                    <a href="#contabilidade" data-toggle="collapse" aria-expanded="{{ Request::is('contabilidade/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>{{ __('SRA') }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ Request::is('contabilidade/*') ? 'collapse show' : '' }}" id="contabilidade" data-parent="#accordionExample">
                        <li {!! Request::is('contabilidade/irrf') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('contabilidade-irrf'))
                                <a href="{{ route('contabilidade.irrf') }}">{{ __('IRRF') }}</a>
                            @endif
                        </li>
                        <li {!! Request::is('contabilidade/financeiro/balancete') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('contabilidade-irrf'))
                                <a href="{{ route('contabilidade.relatorio-balancete') }}">{{ __('Balancete') }}</a>
                            @endif
                        </li>
                    </ul>
                </li>
            @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('congregacao-index'))
                 <li class="menu {{ Request::is('congregacao','congregacao/*') ? 'active' : '' }} ">
                     <a href="/congregacao" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>{{ __('Congregações') }}</span>
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
                             <span>{{ __('GCEU') }}</span>
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
                        <li {!! Request::is('gceu/dashboard*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu'))
                                <a href="{{ route('gceu.dashboard') }}">{{ __('Dashboard') }}</a>
                            @endif
                        </li>
                         <li {!! Request::is('gceu/lista') || Request::is('gceu/editar*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-lista'))
                                 <a href="{{ route('gceu.index') }}">{{ __('Cadastro') }}</a>
                             @endif
                         </li>
                         <!-- <li {!! Request::is('gceu/lista/teste') || Request::is('gceu/editar*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-lista-teste'))
                                 <a href="{{ route('gceu.index.teste') }}">{{ __('TESTE') }}</a>
                             @endif
                         </li> -->
                         <li {!! Request::is('gceu/membros') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-membros'))
                                 <a href="{{ route('gceu.membros') }}">{{ __('Membros') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/carta-pastoral*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-carta-pastoral'))
                                 <a href="{{ route('gceu.carta-pastoral') }}">{{ __('Carta Pastoral') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/diario*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-diario'))
                                 <a href="{{ route('gceu.diario') }}">{{ __('Diário') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/reuniao-pessoas*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-diario'))
                                <a href="{{ route('gceu.reuniao-pessoas') }}">{{ __('Cadastro Reunião') }}</a>
                            @endif
                         </li>
                         <li class="submenu-fixo mt-3 mb-3">
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-relatorios'))
                                <span>{{ __('Relatórios') }}</span>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-gceu*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-gceu'))
                                <a href="{{ route('gceu.relatorio.gceu') }}">{{ __('Lista de GCEU') }}</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-carta-pastoral*') ? 'class="active"' : '' !!}>
                             @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-carta-pastoral-relatorio'))
                                 <a href="{{ route('gceu.carta-pastoral-relatorio') }}">{{ __('Carta Pastoral') }}</a>
                             @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-diario*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-relatorio-diario'))
                                <a href="{{ route('gceu.diario-relatorio') }}">{{ __('Diário') }}</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-reuniao-pessoas*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-relatorio-diario'))
                                <a href="{{ route('gceu.relatorio.reuniao-pessoas') }}">{{ __('Visitantes/Convertidos') }}</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-funcoes*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-funcoes'))
                                <a href="{{ route('gceu.relatorio.funcoes') }}">{{ __('Lista de Funções') }}</a>
                            @endif
                         </li>
                         <li {!! Request::is('gceu/relatorio-aniversariantes*') ? 'class="active"' : '' !!}>
                            @if (auth()->check() && auth()->user()->hasPerfilRegra('gceu-igreja-lista-aniversariantes'))
                                <a href="{{ route('gceu.relatorio.aniversariantes') }}">{{ __('Aniversariantes') }}</a>
                            @endif
                         </li>
                     </ul>
                 </li>
             @endif

     @if (auth()->user()->hasPerfilRegra('patrimonio-dashboard'))
                 <li class="menu">
                     <a href="#patrimonio" data-toggle="collapse" aria-expanded="{{ Request::is('patrimonio*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-home">
                                 <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                 <polyline points="9 22 9 12 15 12 15 22"></polyline>
                             </svg>
                             <span>{{ __('Patrimônio') }}</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>
                     <ul class="collapse submenu list-unstyled {{ Request::is('patrimonio*') ? 'collapse show' : '' }}" id="patrimonio" data-parent="#patrimonio">
                         <li {!! Request::is('patrimonio') ? 'class="active"' : '' !!}>
                             <a href="{{ route('patrimonio.dashboard') }}">{{ __('Dashboard') }}</a>
                         </li>
                         @if (auth()->user()->hasPerfilRegra('patrimonio-bens-imoveis'))
                             <li {!! Request::is('patrimonio/bens-imoveis*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.bens-imoveis.index') }}">{{ __('Bens Imóveis') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-bens-moveis'))
                             <li {!! Request::is('patrimonio/bens-moveis*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.bens-moveis.index') }}">{{ __('Bens Móveis') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-benfeitoria'))
                             <li {!! Request::is('patrimonio/benfeitorias*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.benfeitorias.index') }}">{{ __('Benfeitorias') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-documentos'))
                             <li {!! Request::is('patrimonio/documentos*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.documentos.index') }}">{{ __('Documentos') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-baixa'))
                             <li {!! Request::is('patrimonio/baixas*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.baixas.index') }}">{{ __('Baixas') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-relatorios'))
                             <li class="submenu-fixo mt-3 mb-3">
                                 <span>{{ __('Relatórios') }}</span>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/imoveis_cadastrados') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'imoveis_cadastrados']) }}">{{ __('Imóveis cadastrados') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/bens_moveis_cadastrados') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'bens_moveis_cadastrados']) }}">{{ __('Bens móveis cadastrados') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/imoveis_regularizacao_pendente') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'imoveis_regularizacao_pendente']) }}">{{ __('Regularização pendente') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/documentos_vencidos') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'documentos_vencidos']) }}">{{ __('Documentos vencidos') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/avcb_vencido') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'avcb_vencido']) }}">{{ __('AVCB vencido') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/bens_depreciados') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'bens_depreciados']) }}">{{ __('Bens depreciados') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/baixas_patrimoniais') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'baixas_patrimoniais']) }}">{{ __('Baixas patrimoniais') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/valor_total_por_categoria') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'valor_total_por_categoria']) }}">{{ __('Valor total por categoria') }}</a>
                             </li>
                             <li {!! Request::is('patrimonio/relatorios/bens_por_igreja_unidade') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('patrimonio.relatorios.lista', ['relatorio' => 'bens_por_igreja_unidade']) }}">{{ __('Bens por igreja/unidade') }}</a>
                             </li>
                         @endif
                         @if (auth()->user()->hasPerfilRegra('patrimonio-dashboard'))
                             <li class="submenu-fixo mt-3 mb-3">
                                 <span>{{ __('Configurações') }}</span>
                             </li>
                             <li {!! Request::is('patrimonio/configuracoes*') ? 'class="active"' : '' !!}>
                                 <a href="#patrimonio-configuracoes"
                                    data-toggle="collapse"
                                    aria-expanded="{{ Request::is('patrimonio/configuracoes*') ? 'true' : 'false' }}"
                                    class="dropdown-toggle">
                                     Configurações
                                 </a>
                                 <ul class="collapse list-unstyled sub-submenu {{ Request::is('patrimonio/configuracoes*') ? 'collapse show' : '' }}" id="patrimonio-configuracoes">
                                     <li {!! Request::is('patrimonio/configuracoes/natureza*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'natureza']) }}">{{ __('Natureza') }}</a>
                                     </li>
                                     <li {!! Request::is('patrimonio/configuracoes/status*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'status']) }}">{{ __('Status') }}</a>
                                     </li>
                                     <li {!! Request::is('patrimonio/configuracoes/iptu*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'iptu']) }}">{{ __('IPTU') }}</a>
                                     </li>
                                     <li {!! Request::is('patrimonio/configuracoes/categoria*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'categoria']) }}">{{ __('Categoria') }}</a>
                                     </li>
                                     <li {!! Request::is('patrimonio/configuracoes/comprobatorio*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'comprobatorio']) }}">{{ __('Comprobatório') }}</a>
                                     </li>
                                     <li {!! Request::is('patrimonio/configuracoes/tipo_documento*') ? 'class="active"' : '' !!}>
                                         <a href="{{ route('patrimonio.configuracoes.tipos.index', ['tipo' => 'tipo_documento']) }}">{{ __('Tipo de documento') }}</a>
                                     </li>
                                 </ul>
                             </li>
                         @endif
                     </ul>
                 </li>
             @endif

             @if (auth()->check()  && auth()->user()->hasPerfilRegra('ebd-dashboard') )
                 <li class="menu">
                     <a href="#ebd" data-toggle="collapse" aria-expanded="{{ Request::is('ebd*') ? 'true' : 'false' }}" class="dropdown-toggle">
                         <div class="">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-book-open">
                                 <path d="M2 3h6a4 4 0 0 1 4 4v14a4 4 0 0 0-4-4H2z"></path>
                                 <path d="M22 3h-6a4 4 0 0 0-4 4v14a4 4 0 0 1 4-4h6z"></path>
                             </svg>
                             <span>{{ __('EBD') }}</span>
                         </div>
                         <div>
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-chevron-right">
                                 <polyline points="9 18 15 12 9 6"></polyline>
                             </svg>
                         </div>
                     </a>

                 </li>
             @endif

             @if (auth()->check() && auth()->user()->hasPerfilRegra('distrito-gestao-igrejas'))
                 <li class="menu {{ Request::is('igreja', 'igreja/*') ? 'active' : '' }}">
                     <a href="/igreja" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>{{ __('Igrejas') }}</span>
                         </div>
                     </a>
                 </li>
             @endif
             {{-- @if (auth()->check() && auth()->user()->hasPerfilRegra('regiao-gestao-igrejas'))
                 <li class="menu {{ Request::is('igreja', 'igreja/*') ? 'active' : '' }}">
                     <a href="/igreja" aria-expanded="false" class="dropdown-toggle">
                         <div class="">
                             <x-bx-church />
                             <span>{{ __('Igrejas') }}</span>
                         </div>
                     </a>
                 </li>
             @endif --}}

             <li class="menu menu-heading">
                 <div class="heading"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                         <circle cx="12" cy="12" r="10"></circle>
                     </svg><span>{{ __('APLICAÇÃO') }}</span></div>
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
                         <span>{{ __('Trocar Instituição') }}</span>
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
                         <span>{{ __('Segurança') }}</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('seguranca/*', 'auditorias*') ? 'collapse show' : '' }}" id="segurancaLocal" data-parent="#accordionExample">
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
                             <a href="{{ $hrefRoute }}"> {{ __('Gerenciar usuários') }}</a>
                         </li>

                         @if (auth()->check() && auth()->user()->hasPerfilRegra('auditoria'))
                             <li {!! Request::is('auditorias*') ? 'class="active"' : '' !!}>
                                 <a href="{{ route('auditorias.index') }}"> {{ __('Auditorias') }}</a>
                             </li>
                         @endif
                     @endif
                 </ul>
             </li>

             <li class="menu">
                 <a href="#perfil" data-toggle="collapse" aria-expanded="{{Request::is('usuario/*') ? 'true' : 'false' }}" class="dropdown-toggle">
                     <div class="">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-user">
                             <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                             <circle cx="12" cy="7" r="4"></circle>
                         </svg>
                         <span>{{ __('Perfil') }}</span>
                     </div>
                     <div>
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-chevron-right">
                             <polyline points="9 18 15 12 9 6"></polyline>
                         </svg>
                     </div>
                 </a>
                 <ul class="collapse submenu list-unstyled {{ Request::is('usuario/*') ? 'collapse show' : '' }}" id="perfil" data-parent="#accordionExample">
                     <li {!! Request::is('usuario/perfis') ? 'class="active"' : '' !!}>
                         <a href="{{ route('perfil.index') }}"> {{ __('Dados Pessoais') }}</a>
                     </li>
                     <li {!! Request::is('usuario/perfil/carteira-digital') ? 'class="active"' : '' !!}>
                         <a href="{{ route('perfil.carteira-digital') }}"> Carteira Digital</a>
                     </li>
                     @if (auth()->user()->pessoa_id)
                         <li {!! Request::is('usuario/clerigos/perfil/dependentes') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.dependentes.index') }}"> {{ __('Dependentes') }}</a>
                         </li>
                         <li {!! Request::is('usuario/clerigos/perfil/prebendas') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.prebendas.index') }}"> {{ __('Prebendas') }}</a>
                         </li>
                         <li  {!! Request::is('usuario/clerigos/perfil/imposto-de-renda') ? 'class="active"' : '' !!}>
                             <a href="{{ route('clerigos.perfil.impostoDeRenda.index') }}"> {{ __('Imposto de Renda') }}</a>
                         </li>
                         @if (App\Services\InformeRendimentos\ChecaArquivoExistenteService::execute(date('Y')))
                             <li>
                                 <a href="{{ route('informe_rendimentos.exibirPdf', ['ano' => date('Y')]) }}"
                                     target="_blank">{{ __('Informe de Rendimentos') }}</a>
                             </li>
                         @endif
                     @endif
                 </ul>
             </li>

         </ul>

     </nav>
 </div>
 <!--  END SIDEBAR  -->
