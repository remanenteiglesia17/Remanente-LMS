@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\preloaderHelper')

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

{{-- Default Content Wrapper --}}
<div class="{{ $layoutHelper->makeContentWrapperClasses() }}">

    {{-- Preloader Animation (cwrapper mode) --}}
    @if($preloaderHelper->isPreloaderEnabled('cwrapper'))
        @include('adminlte::partials.common.preloader')
    @endif

    {{-- Banner de impersonación: visible mientras un admin está viendo la plataforma como un estudiante --}}
    @if (session()->has('impersonator_id'))
        <div class="alert alert-warning text-center mb-0 rounded-0" style="border: none;">
            <i class="fas fa-user-secret"></i>
            Estás viendo la plataforma como <strong>{{ auth()->user()->name }}</strong> (estudiante de ejemplo).
            <a href="{{ route('admin.impersonate.detener') }}" class="btn btn-sm btn-dark ml-2">
                <i class="fas fa-arrow-left"></i> Volver a mi cuenta
            </a>
        </div>
    @endif

    {{-- Content Header --}}
    @hasSection('content_header')
        <div class="content-header">
            <div class="{{ config('adminlte.classes_content_header') ?: $def_container_class }}">
                @yield('content_header')
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="content">
        <div class="{{ config('adminlte.classes_content') ?: $def_container_class }}">
            @stack('content')
            @yield('content')
        </div>
    </div>

</div>
