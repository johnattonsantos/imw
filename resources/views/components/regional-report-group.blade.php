@props(['id', 'title'])
@php
    $conteudo = trim((string) $slot);
    $aberto = str_contains($conteudo, 'class="active"');
@endphp
@if($conteudo !== '')
<li class="regional-report-group">
    <a href="#{{ $id }}" class="regional-group-toggle" data-toggle="collapse" aria-controls="{{ $id }}" aria-expanded="{{ $aberto ? 'true' : 'false' }}">
        <span>{{ __($title) }}</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <ul class="collapse list-unstyled sub-submenu {{ $aberto ? 'show' : '' }}" id="{{ $id }}">
        {{ $slot }}
    </ul>
</li>
@endif
