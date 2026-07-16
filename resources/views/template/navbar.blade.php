 <!--  BEGIN NAVBAR  -->
 <div class="header-container fixed-top">
     <header class="header navbar navbar-expand-sm">
         <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                 <line x1="3" y1="12" x2="21" y2="12"></line>
                 <line x1="3" y1="6" x2="21" y2="6"></line>
                 <line x1="3" y1="18" x2="21" y2="18"></line>
             </svg></a>
         <ul class="navbar-item flex-row search-ul">
            <li class="nav-item align-self-center search-animated">
                                              
            </li>
	         </ul>
	         <ul class="navbar-item flex-row navbar-dropdown">
                @php
                    $locales = config('locales.supported', []);
                    $currentLocale = app()->getLocale();
                @endphp
                <li class="nav-item align-self-center mr-2">
                    <form action="{{ route('locale.update') }}" method="POST" class="mb-0">
                        @csrf
                        <label class="sr-only" for="navbar-locale-select">{{ __('app.language.select') }}</label>
                        <select id="navbar-locale-select" name="locale" class="form-control form-control-sm" onchange="this.form.submit()" aria-label="{{ __('app.language.select') }}" style="min-width: 210px; height: 38px;">
                            @foreach($locales as $locale => $settings)
                                <option value="{{ $locale }}" {{ $currentLocale === $locale ? 'selected' : '' }}>
                                    {{ $settings['flag'] ?? '' }} {{ $settings['short_name'] ?? strtoupper($locale) }} - {{ $settings['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </li>

	            @if($baseParams->notificacoesTransferencia && $baseParams->notificacoesTransferencia->count())
                <li class="nav-item dropdown notification-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-success"></span>
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="notificationDropdown">
                        <div class="notification-scroll">

                            @foreach ($baseParams->notificacoesTransferencia as $notificacao)
                                <div class="dropdown-item">
                                    <a class="media server-log" href="{{ route('membro.receber_membro_externo', ['notificacao' => $notificacao->id]) }}">
                                        <x-bx-door-open />
                                        <div class="media-body">
                                            <div class="data-info">
                                                <h6 class="">{{ __('Nova transferência para esta instituição') }}</h6>
                                                <p class="">{{ $notificacao->membro->nome }}</p>
                                            </div>

                                            <div class="icon-status">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>
            @endif

             <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                 <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img src="{{ asset('theme/assets/img/90x90.jpg') }}" alt="{{ __('avatar') }}">
                 </a>
                 <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                     <div class="user-profile-section">
                         <div class="media mx-auto">
                            {{--    <img src="{{ asset('theme/assets/img/90x90.jpg') }}" class="img-fluid mr-2" alt="{{ __('avatar') }}"> --}}
                             <div class="media-body">
                                 <h5>{{ $firstName }} {{ $lastName }}</h5>
                               {{--   <p>{{ __('Administrador') }}</p> --}}
                             </div>
                         </div>
                     </div>
                     <div class="dropdown-item">
                         <a href="{{ route('perfil.index') }}">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                 <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                 <circle cx="12" cy="7" r="4"></circle>
                             </svg> <span> {{ __('app.navigation.profile') }}</span>
                         </a>
                     </div>
                     <div class="dropdown-item">
                        <a href="{{ route('selecionarPerfil') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-repeat __web-inspector-hide-shortcut__"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                            <span> {{ __('app.navigation.change_institution') }}</span>
                        </a>
                    </div>
                    
                     <div class="dropdown-item">
                         <!-- Este link atua como um botão para submeter o formulário de logout -->
                         <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                 <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                 <polyline points="16 17 21 12 16 7"></polyline>
                                 <line x1="21" y1="12" x2="9" y2="12"></line>
                             </svg> <span>{{ __('app.navigation.logout') }}</span>
                         </a>
                         <!-- Formulário oculto que será submetido para fazer o logout -->
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                             @csrf
                         </form>
                     </div>

                 </div>
             </li>
         </ul>
     </header>
 </div>
 <!--  END NAVBAR  -->
