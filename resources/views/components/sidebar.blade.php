<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ url('/') }}" class="text-primary">MY-WAREHOUSE</a> 
        </div>
        <ul class="sidebar-menu">
            <li class='{{ Request::is('dashboard') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-home"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class='{{ Request::is('products') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ route('products.index') }}"><i class="fas fa-box"></i>
                    <span>Products</span></a>
            </li>
            <li class='{{ Request::is('locations') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ route('locations.index') }}"><i class="fas fa-warehouse"></i>
                    <span>Locations</span></a>
            </li>
            <li class='{{ Request::is('mutations') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ route('mutations.index') }}"><i class="fas fa-random"></i>
                    <span>Mutations</span></a>
            </li>
            <li class='{{ Request::is('app-logs') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ route('app-logs.index') }}"><i class="fas fa-clipboard-list"></i>
                    <span>Application Logs</span></a>
            </li>
        </ul>
    </aside>
</div>
