<div id="breadcrumbArrowed" class="col-xl-12 col-lg-12 layout-spacing ml-2 mt-3">
    <nav class="breadcrumb-two" aria-label="{{ __('breadcrumb') }}">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}">
                <a href="{{ $breadcrumb['url'] }}">{{ __($breadcrumb['text']) }}</a>
            </li>
            @endforeach
        </ol>
    </nav>
</div>
