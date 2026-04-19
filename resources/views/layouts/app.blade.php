<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIMP</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/2.3.1/css/dataTables.bootstrap5.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e2a3a;
            --sidebar-text: #c8d3e0;
            --topbar-height: 56px;
            --primary-color: #0d6efd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        #sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            transition: width 0.3s ease;
        }

        #sidebar.collapsed {
            width: 68px;
        }

        #sidebar .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        #sidebar.collapsed .sidebar-header span {
            display: none;
        }

        #sidebar .nav-section {
            padding: 20px 0 10px;
        }

        #sidebar .nav-section-title {
            padding: 0 16px;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: rgba(200, 211, 224, 0.6);
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        #sidebar.collapsed .nav-section-title {
            display: none;
        }

        #sidebar .nav-link {
            padding: 10px 16px;
            margin: 2px 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        #sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        #sidebar .nav-link i {
            font-size: 1.1rem;
            min-width: 24px;
        }

        #sidebar.collapsed .nav-link span {
            display: none;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            margin: 2px 4px;
            padding: 10px 8px;
        }

        #sidebar .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
        }

        #sidebar.collapsed .sidebar-footer span {
            display: none;
        }

        #main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        #main-wrapper.collapsed {
            margin-left: 68px;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 999;
            transition: left 0.3s ease;
        }

        #main-wrapper.collapsed .topbar {
            left: 68px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: #333;
            padding: 4px 8px;
        }

        .topbar-title {
            font-weight: bold;
            font-size: 1rem;
            color: #0d6efd;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .content-area {
            margin-top: var(--topbar-height);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-height));
        }

        .alert-dismissible .btn-close {
            padding: 0;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0 0 16px 0;
            margin-bottom: 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a2332;
        }

        .btn-custom {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .table-responsive {
            margin-top: 16px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .badge-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-label {
            font-weight: 500;
            color: #1a2332;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 16px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
        }

        .dropdown-menu {
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        @media (max-width: 768px) {
            #sidebar {
                width: 68px;
            }

            #main-wrapper {
                margin-left: 68px;
            }

            .topbar {
                left: 68px;
            }

            #sidebar .nav-section-title,
            #sidebar .nav-link span,
            #sidebar .sidebar-footer span {
                display: none;
            }

            .sidebar-header span {
                display: none;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .content-area {
                padding: 16px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-box-seam" style="font-size: 1.5rem;"></i>
            <span>SIMP</span>
        </div>

        <div class="nav-section">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Panel de Control</span>
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="nav-section">
            <div class="nav-section-title">Administracion</div>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Usuarios</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole(['admin', 'warehouse_manager']))
        <div class="nav-section">
            <div class="nav-section-title">Catalogos</div>
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i>
                <span>Categorias</span>
            </a>
            <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>
                <span>Unidades</span>
            </a>
            <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Ubicaciones</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'purchasing']))
        <div class="nav-section">
            <div class="nav-section-title">Proveedores</div>
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>Proveedores</span>
            </a>
        </div>
        @endif

        @if(!auth()->user()->hasRole(['production', 'quality']))
        <div class="nav-section">
            <div class="nav-section-title">Inventario</div>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Productos</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']))
        <div class="nav-section">
            <div class="nav-section-title">Operaciones</div>
            <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-down-square-fill"></i>
                <span>Recepciones</span>
            </a>
            <a href="{{ route('dispatches.index') }}" class="nav-link {{ request()->routeIs('dispatches.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-square-fill"></i>
                <span>Despachos</span>
            </a>
            <a href="{{ route('adjustments.index') }}" class="nav-link {{ request()->routeIs('adjustments.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check"></i>
                <span>Ajustes</span>
            </a>
        </div>
        @elseif(auth()->user()->hasRole(['production']))
        <div class="nav-section">
            <div class="nav-section-title">Operaciones</div>
            <a href="{{ route('dispatches.index') }}" class="nav-link {{ request()->routeIs('dispatches.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-square-fill"></i>
                <span>Despachos</span>
            </a>
        </div>
        @endif

        <div class="nav-section">
            <div class="nav-section-title">Monitoreo</div>
            <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i>
                <span>Alertas <span class="badge bg-danger" id="alerts-badge" style="display: none;"></span></span>
            </a>
        </div>

        @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'quality', 'purchasing']))
        <div class="nav-section">
            <div class="nav-section-title">Reportes</div>
            <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Inicio</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole(['admin', 'warehouse_manager', 'purchasing']))
        <div class="nav-section">
            <div class="nav-section-title">Compras</div>
            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i>
                <span>Ordenes</span>
            </a>
        </div>
        @endif

        <div class="sidebar-footer">
            <div><strong>{{ auth()->user()->name }}</strong></div>
            <div class="text-muted" style="font-size: 0.8rem;">{{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}</div>
        </div>
    </div>

    <div id="main-wrapper">
        <div class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebar-toggle" title="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">SIMP</div>
            </div>
            <div class="topbar-right">
                <a href="{{ route('alerts.index') }}" class="btn-icon" title="Alertas">
                    <i class="bi bi-bell"></i>
                    <span class="badge bg-danger position-absolute" id="topbar-alerts-badge" style="display: none; top: 5px; right: 5px; font-size: 0.65rem;"></span>
                </a>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><hr class="dropdown-divider"></li>
                        <li><form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesion</button>
                        </form></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-area">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap5.js"></script>

    <script>
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('main-wrapper');

        // Restaurar estado del sidebar desde localStorage
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainWrapper.classList.add('collapsed');
        }

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });

        // Inicializar DataTables (DataTables 2.x sin jQuery)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.simp-datatable').forEach(function(el) {
                if (!DataTable.isDataTable(el)) {
                    new DataTable(el, {
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/2.3.1/i18n/es-MX.json'
                        },
                        pageLength: 25,
                        responsive: true
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
