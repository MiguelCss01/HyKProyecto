<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - HyK Mayorista</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1;
        }
        /* Custom scrollbar for data table */
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
        /* Reglas para evitar colapso flex/grid del modal en el dashboard */
        #modal-logout {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100vw !important;
            z-index: 99999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
        }
        #modal-logout.pointer-events-none {
            pointer-events: none !important;
        }
        #modal-logout.pointer-events-auto {
            pointer-events: auto !important;
        }
        #modal-logout-card {
            width: 100% !important;
            max-width: 28rem !important;
            min-width: 280px !important;
            margin: auto !important;
            flex-shrink: 0 !important;
            box-sizing: border-box !important;
        }
    </style>
</head>
<body class="bg-background text-on-surface h-full flex antialiased">
    <!-- SideNavBar -->
    <aside class="h-full w-64 fixed left-0 top-0 bg-surface border-r border-outline-variant flex flex-col py-lg z-50">
        <!-- Brand Logo -->
        <div class="px-lg mb-xl flex items-center gap-md">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center shrink-0 shadow-sm border border-outline-variant/30">
                <span class="material-symbols-outlined text-on-primary-container fill" data-icon="storefront">storefront</span>
            </div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-primary truncate leading-tight">HyK Mayorista</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-none">Portal de Administración</p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="flex-1 flex flex-col gap-sm">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-md font-label-md py-3 px-lg transition-colors group {{ request()->routeIs('admin.dashboard') ? 'text-primary border-r-4 border-primary bg-primary-fixed-dim/10 opacity-80 transition-all duration-150' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.dashboard') ? 'fill' : 'group-hover:text-primary transition-colors' }}" data-icon="dashboard">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.productos.index') }}" class="flex items-center gap-md font-label-md py-3 px-lg transition-colors group {{ request()->routeIs('admin.productos.*') ? 'text-primary border-r-4 border-primary bg-primary-fixed-dim/10 opacity-80 transition-all duration-150' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('admin.productos.*') ? 'fill' : 'group-hover:text-primary transition-colors' }}" data-icon="inventory">inventory</span>
                <span>Catálogo y Stock</span>
            </a>
            <a href="#" class="flex items-center gap-md font-label-md py-3 px-lg transition-colors group text-on-surface-variant hover:bg-surface-container-high">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors" data-icon="receipt_long">receipt_long</span>
                <span>Pedidos Entrantes</span>
            </a>
            <a href="#" class="flex items-center gap-md font-label-md py-3 px-lg transition-colors group text-on-surface-variant hover:bg-surface-container-high">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors" data-icon="group">group</span>
                <span>Clientes</span>
            </a>
        </nav>

        <!-- User Profile (Bottom of nav) -->
        <div class="mt-auto px-lg pt-lg border-t border-outline-variant/50">
            <div class="flex items-center gap-md p-2 -mx-2 rounded-lg transition-colors">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs uppercase">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-label-md text-label-md text-on-surface truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant truncate text-[12px]">{{ auth()->user()->email ?? 'admin@hyk.com' }}</p>
                </div>
                
                <button type="button" onclick="openLogoutModal()" title="Cerrar sesión" class="ml-auto text-on-surface-variant hover:text-error transition-colors flex cursor-pointer">
                    <span class="material-symbols-outlined text-sm">logout</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="ml-64 flex-1 h-full flex flex-col overflow-hidden bg-background relative">
        @if (session('success'))
            <div class="alert-auto-dismiss m-4 bg-[#d4edda] border border-[#c3e6cb] text-[#155724] px-4 py-3 rounded z-50 absolute top-0 right-0 shadow-lg transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert-auto-dismiss m-4 bg-[#f8d7da] border border-[#f5c6cb] text-[#721c24] px-4 py-3 rounded z-50 absolute top-0 right-0 shadow-lg transition-opacity duration-500">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Ocultar alertas automáticamente después de 3 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-auto-dismiss');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>
    <x-modal-logout />
</body>
</html>
