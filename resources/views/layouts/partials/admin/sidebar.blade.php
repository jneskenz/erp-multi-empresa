<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m317.575 45.518 10.998-24.746c3.313-7.453-.044-16.182-7.498-19.494-7.455-3.314-16.182.044-19.494 7.498l-15.794 35.536h-59.573L210.42 8.775c-3.312-7.453-12.041-10.811-19.494-7.498-7.453 3.312-10.81 12.041-7.498 19.494l10.998 24.746c-29.401 5.77-51.655 31.726-51.655 62.793v24.615h226.46V108.31c-.001-31.065-22.255-57.023-51.656-62.792z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M497.229 260.925h-88.615v-30.708c44.584-7.102 78.769-45.816 78.769-92.368v-19.692c0-8.156-6.613-14.769-14.769-14.769s-14.769 6.613-14.769 14.769v19.692c0 30.206-21.04 55.573-49.23 62.261v-13.03c0-13.573-11.042-24.615-24.615-24.615H270.77v172.306c0 8.156-6.613 14.769-14.769 14.769-8.157 0-14.769-6.613-14.769-14.769V162.464h-113.23c-13.573 0-24.615 11.042-24.615 24.615v13.03c-28.19-6.688-49.23-32.055-49.23-62.261v-19.692c0-8.156-6.613-14.769-14.769-14.769S24.619 110 24.619 118.156v19.692c0 46.552 34.185 85.265 78.769 92.368v30.708H14.771c-8.156 0-14.769 6.613-14.769 14.769s6.613 14.769 14.769 14.769h88.615v30.708c-44.584 7.102-78.769 45.816-78.769 92.368v19.692c0 8.156 6.613 14.769 14.769 14.769s14.769-6.613 14.769-14.769v-19.692c0-30.206 21.04-55.573 49.23-62.261v8.107C103.386 443.537 171.849 512 256 512s152.614-68.463 152.614-152.614v-8.107c28.19 6.688 49.23 32.055 49.23 62.261v19.692c0 8.156 6.613 14.769 14.769 14.769s14.769-6.613 14.769-14.769V413.54c0-46.552-34.185-85.265-78.769-92.368v-30.708h88.615c8.156 0 14.769-6.613 14.769-14.769s-6.612-14.77-14.768-14.77z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- MENU VERTICAL --}}

        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Gestión de Usuarios -->
        @canany(['users.view', 'roles.view'])
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Backoffice</span>
            </li>
        @endcanany

        @superadmin

        {{-- CRM --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="CRM">CRM</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.lead-cliente.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-cliente.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-building-bank"></i>
                        <div data-i18n="Analytics">Leads</div>
                    </a>
                </li>
            </ul>
        </li>
        {{-- Config. del sistema --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-buildings"></i>
                    <div data-i18n="Layouts">Adm. Empresarial</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.grupos.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-building-bank"></i>
                            <div data-i18n="Analytics">Gestión Empresas/Tenants</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-report"></i>
                            <div data-i18n="Support">Usuarios SaaS</div>
                        </a>
                    </li>
                    {{-- <li class="menu-item">
                        <a href="{{ route('customization.index') }}" class="menu-link">
                            <div data-i18n="Container">Personalización</div>
                        </a>
                    </li> --}}
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-report"></i>
                            <div data-i18n="Support">Planes y Suscripciones</div>
                        </a>
                    </li>
                    

                </ul>
            </li>
        
            @canany(['users.view', 'roles.view'])
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Reporte y Análisis</span>
                </li>
            @endcanany

            <li class="menu-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                <a href="{{ route('admin.logs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-bug"></i>
                    <div data-i18n="Analytics">Logs System</div>
                    <div class="badge text-bg-danger rounded-pill ms-auto" id="errorCount" style="display: none;">
                        0
                    </div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-report"></i>
                    <div data-i18n="Support">Integraciones (Rest API)</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-report"></i>
                    <div data-i18n="Support">Métricas y Reportes</div>
                </a>
            </li>
        @endsuperadmin

    </ul>
</aside>
