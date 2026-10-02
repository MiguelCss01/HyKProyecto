<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>HyK Mayorista - Portal de Compras</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-container-highest": "#e1e3e4", "on-surface": "#191c1d", "tertiary-container": "#454e55",
              "on-tertiary-container": "#b6bfc8", "on-secondary-container": "#007327", "surface-bright": "#f8f9fa",
              "primary": "#00317e", "tertiary-fixed-dim": "#bfc8d0", "on-secondary": "#ffffff",
              "outline-variant": "#c3c6d5", "secondary-fixed-dim": "#66df75", "surface-variant": "#e1e3e4",
              "secondary-fixed": "#83fc8e", "on-background": "#191c1d", "inverse-primary": "#b2c5ff",
              "inverse-on-surface": "#f0f1f2", "secondary-container": "#80f98b", "primary-container": "#0046ad",
              "error": "#ba1a1a", "on-secondary-fixed-variant": "#00531a", "tertiary": "#2f373e",
              "on-tertiary-fixed": "#141d23", "on-primary-fixed-variant": "#0040a0", "surface-tint": "#2559bf",
              "inverse-surface": "#2e3132", "error-container": "#ffdad6", "surface-dim": "#d9dadb",
              "on-error": "#ffffff", "primary-fixed": "#dae2ff", "on-error-container": "#93000a",
              "surface-container-lowest": "#ffffff", "on-secondary-fixed": "#002106", "tertiary-fixed": "#dbe4ed",
              "on-surface-variant": "#434653", "on-tertiary-fixed-variant": "#3f484f", "on-primary-fixed": "#001847",
              "background": "#f8f9fa", "on-primary-container": "#a5bdff", "surface-container-low": "#f3f4f5",
              "outline": "#737784", "surface": "#f8f9fa", "surface-container-high": "#e7e8e9",
              "secondary": "#006e25", "on-primary": "#ffffff", "surface-container": "#edeeef",
              "primary-fixed-dim": "#b2c5ff", "on-tertiary": "#ffffff"
            },
            spacing: { "margin-mobile": "16px", "sm": "8px", "xs": "4px", "gutter": "20px", "xl": "40px", "lg": "24px", "margin-desktop": "64px", "md": "16px" },
            fontFamily: { "headline-lg": ["Inter"], "label-md": ["Inter"], "body-sm": ["Inter"], "price-display": ["Inter"], "headline-sm": ["Inter"], "body-md": ["Inter"] }
          }
        }
      }
</script>
<style>
  .scrollbar-none::-webkit-scrollbar { display: none; }
  .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased">
<!-- TopNavBar -->
<header class="h-16 bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-tertiary flex items-center justify-between px-margin-mobile md:px-margin-desktop sticky top-0 z-50">
    <div class="flex items-center gap-sm">
        <button id="mobile-menu-btn" type="button" class="md:hidden p-2 text-on-surface-variant hover:bg-surface-container-low rounded-full cursor-pointer transition-colors" aria-label="Abrir categorías">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="/" class="font-headline-lg font-bold text-primary dark:text-primary-fixed-dim tracking-tight text-xl">HyK Mayorista</a>
    </div>
    <div class="hidden md:flex flex-1 max-w-2xl mx-lg relative items-center">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-xl">search</span>
        <input id="search-input-desktop" class="w-full pl-10 pr-10 py-2 bg-surface-bright border border-outline-variant rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-body-md" placeholder="Buscar productos, marcas o códigos..." type="text" autocomplete="off"/>
        <button type="button" id="search-clear-desktop" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface p-1 rounded-full hover:bg-surface-container-high transition-colors cursor-pointer" title="Limpiar búsqueda">
            <span class="material-symbols-outlined text-base">close</span>
        </button>
    </div>
    <div class="flex items-center gap-sm">
        @auth
            <a href="{{ route('pedidos.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-slate-700 hover:text-primary transition-colors mr-3" title="Ver mis pedidos">
                <span class="material-symbols-outlined text-base text-slate-500">receipt_long</span>
                Mis Pedidos
            </a>
            <a href="{{ Route::has('perfil.index') ? route('perfil.index') : '#' }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-slate-700 hover:text-primary transition-colors mr-4" title="Ver mi perfil">
                <span class="material-symbols-outlined text-base text-slate-500">person</span>
                Mi Perfil
            </a>
            <span class="hidden md:block mr-4 text-sm font-bold text-slate-800">Hola, {{ auth()->user()->name }}</span>
            <button type="button" onclick="openLogoutModal()" class="text-sm font-bold text-red-600 hover:underline mr-4 cursor-pointer">Salir</button>
        @else
            <a href="{{ route('login') }}" class="text-sm font-bold text-primary hover:underline mr-4">Ingresar / Registrarse</a>
        @endauth

        @php
            $conteoCarrito = app(\App\Services\CartService::class)->conteoTotal();
        @endphp

        <a href="{{ route('carrito.index') }}" class="p-2 text-slate-700 hover:text-emerald-700 hover:bg-surface-container-low transition-colors rounded-full relative flex items-center justify-center" title="Ver Carrito de Compras">
            <span class="material-symbols-outlined">shopping_cart</span>
            @if($conteoCarrito > 0)
                <span class="absolute top-0 right-0 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-error text-on-error text-[10px] font-bold leading-none">
                    {{ $conteoCarrito }}
                </span>
            @else
                <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-surface-variant text-on-surface-variant text-[10px] font-bold leading-none">
                    0
                </span>
            @endif
        </a>
    </div>
</header>

<!-- Mobile Drawer Backdrop & Drawer -->
<div id="mobile-drawer-backdrop" class="fixed inset-0 bg-black/50 z-50 hidden transition-opacity duration-300 opacity-0 backdrop-blur-xs"></div>
<aside id="mobile-drawer" class="fixed top-0 left-0 bottom-0 w-80 max-w-[85vw] bg-surface-bright z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-2xl">
    <div class="p-4 border-b border-outline-variant flex items-center justify-between bg-surface">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">category</span>
            <span class="font-bold text-on-surface">Categorías</span>
        </div>
        <button type="button" id="mobile-drawer-close" class="p-1.5 rounded-full text-on-surface-variant hover:bg-surface-container cursor-pointer transition-colors" aria-label="Cerrar categorías">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div class="p-4 border-b border-outline-variant flex items-center gap-sm">
        <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-xs">
            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'B' }}
        </div>
        <div>
            <p class="font-label-md font-bold text-on-surface">{{ auth()->check() ? auth()->user()->name : 'Bienvenido' }}</p>
            <p class="text-xs text-on-surface-variant">{{ auth()->check() ? (auth()->user()->tipo_cliente == 'mayorista' ? 'Cliente Mayorista' : 'Cliente Minorista') : 'Inicie sesión' }}</p>
        </div>
    </div>
    <ul class="flex flex-col py-sm flex-1 overflow-y-auto" id="categorias-mobile-list">
        @auth
            <li class="px-sm py-xs mb-1">
                <a class="flex items-center gap-sm px-4 py-2 rounded-lg text-slate-700 font-semibold hover:bg-slate-100 hover:text-slate-900 transition-all" href="{{ route('pedidos.index') }}">
                    <span class="material-symbols-outlined text-slate-500">receipt_long</span>
                    <span>Mis Pedidos</span>
                </a>
            </li>
            <li class="px-sm py-xs mb-2 border-b border-outline-variant/60 pb-2">
                <a class="flex items-center gap-sm px-4 py-2 rounded-lg text-slate-700 font-semibold hover:bg-slate-100 hover:text-slate-900 transition-all" href="{{ Route::has('perfil.index') ? route('perfil.index') : '#' }}">
                    <span class="material-symbols-outlined text-slate-500">person</span>
                    <span>Mi Perfil</span>
                </a>
            </li>
        @endauth
        <li class="px-sm py-xs">
            <button type="button" data-categoria-id="todas" class="categoria-btn w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-left font-semibold transition-all bg-primary text-white shadow-sm cursor-pointer">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-xl">apps</span>
                    <span class="categoria-nombre">Todas las categorías</span>
                </div>
                <span class="categoria-count text-xs bg-white/20 text-white font-bold px-2 py-0.5 rounded-full">{{ $productos->count() }}</span>
            </button>
        </li>
        @foreach($categorias as $cat)
            <li class="px-sm py-xs">
                <button type="button" data-categoria-id="{{ $cat->id }}" class="categoria-btn w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-left text-on-surface-variant hover:bg-surface-container transition-all font-medium cursor-pointer">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined text-xl">category</span>
                        <span class="categoria-nombre">{{ $cat->nombre_categoria }}</span>
                    </div>
                    <span class="categoria-count text-xs text-on-surface-variant/70 bg-surface-container-high px-2 py-0.5 rounded-full font-bold">{{ $cat->productos_count ?? $cat->productos->count() }}</span>
                </button>
            </li>
        @endforeach
    </ul>
</aside>

<!-- SideNavBar (Desktop) -->
<nav class="hidden md:flex flex-col fixed left-0 top-16 h-[calc(100vh-64px)] w-64 z-40 bg-surface-bright dark:bg-surface-dim border-r border-outline-variant font-body-md">
    <div class="p-lg border-b border-outline-variant flex items-center gap-sm">
        <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-xs">
            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'B' }}
        </div>
        <div>
            <p class="font-label-md font-bold text-on-surface">{{ auth()->check() ? auth()->user()->name : 'Bienvenido' }}</p>
            <p class="text-sm text-on-surface-variant">{{ auth()->check() ? (auth()->user()->tipo_cliente == 'mayorista' ? 'Cliente Mayorista' : 'Cliente Minorista') : 'Inicie sesión' }}</p>
        </div>
    </div>
    <ul class="flex flex-col py-sm flex-1 overflow-y-auto" id="categorias-desktop-list">
        @auth
            <li class="px-sm py-xs mb-1">
                <a class="flex items-center gap-sm px-4 py-2 rounded-lg text-slate-700 font-semibold hover:bg-slate-100 hover:text-slate-900 transition-all" href="{{ route('pedidos.index') }}">
                    <span class="material-symbols-outlined text-slate-500">receipt_long</span>
                    <span>Mis Pedidos</span>
                </a>
            </li>
            <li class="px-sm py-xs mb-2 border-b border-outline-variant/60 pb-2">
                <a class="flex items-center gap-sm px-4 py-2 rounded-lg text-slate-700 font-semibold hover:bg-slate-100 hover:text-slate-900 transition-all" href="{{ Route::has('perfil.index') ? route('perfil.index') : '#' }}">
                    <span class="material-symbols-outlined text-slate-500">person</span>
                    <span>Mi Perfil</span>
                </a>
            </li>
        @endauth
        <li class="px-sm py-xs">
            <button type="button" data-categoria-id="todas" class="categoria-btn w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-left font-semibold transition-all bg-primary text-white shadow-sm cursor-pointer">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-xl">apps</span>
                    <span class="categoria-nombre">Todas las categorías</span>
                </div>
                <span class="categoria-count text-xs bg-white/20 text-white font-bold px-2 py-0.5 rounded-full">{{ $productos->count() }}</span>
            </button>
        </li>
        @foreach($categorias as $cat)
            <li class="px-sm py-xs">
                <button type="button" data-categoria-id="{{ $cat->id }}" class="categoria-btn w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-left text-on-surface-variant hover:bg-surface-container transition-all font-medium cursor-pointer">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined text-xl">category</span>
                        <span class="categoria-nombre">{{ $cat->nombre_categoria }}</span>
                    </div>
                    <span class="categoria-count text-xs text-on-surface-variant/70 bg-surface-container-high px-2 py-0.5 rounded-full font-bold">{{ $cat->productos_count ?? $cat->productos->count() }}</span>
                </button>
            </li>
        @endforeach
    </ul>
</nav>

<!-- Main Content Canvas -->
<!-- Main Content Canvas -->
<main class="md:ml-64 flex-1 min-w-0 pb-24 md:pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Alertas Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <a href="{{ route('carrito.index') }}" class="text-xs font-bold uppercase tracking-wider text-green-800 underline hover:text-green-900 ml-4">
                    Ver carrito &rarr;
                </a>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Mobile Search Bar -->
        <div class="md:hidden mb-4 bg-surface-bright rounded-2xl shadow-sm border border-outline-variant p-2">
            <div class="relative w-full flex items-center">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-xl">search</span>
                <input id="search-input-mobile" class="w-full pl-10 pr-10 py-2.5 bg-surface-bright border border-outline-variant rounded-xl focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm font-body-md" placeholder="Buscar productos, marcas o códigos..." type="text" autocomplete="off"/>
                <button type="button" id="search-clear-mobile" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface p-1 rounded-full hover:bg-surface-container-high transition-colors cursor-pointer" title="Limpiar búsqueda">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
        </div>

        <!-- Product Grid & Filters -->
        <section>
            <!-- Category Pills Bar (Horizontal scrollable on mobile and desktop) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none" id="category-pills-bar" style="-webkit-overflow-scrolling: touch;">
                <button type="button" data-categoria-id="todas" class="categoria-pill flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all bg-primary text-white shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">apps</span>
                    <span>Todas</span>
                    <span class="text-[11px] opacity-80 font-bold">({{ $productos->count() }})</span>
                </button>
                @foreach($categorias as $cat)
                    <button type="button" data-categoria-id="{{ $cat->id }}" class="categoria-pill flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all bg-surface-container hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">category</span>
                        <span>{{ $cat->nombre_categoria }}</span>
                        <span class="text-[11px] opacity-70 font-bold">({{ $cat->productos_count ?? $cat->productos->count() }})</span>
                    </button>
                @endforeach
            </div>

            <!-- Header Catálogo -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-on-surface tracking-tight">Catálogo de Productos</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span id="contador-productos" class="text-sm text-on-surface-variant font-medium">
                            Mostrando {{ $productos->count() }} productos
                        </span>
                        <span id="filtro-categoria-badge" class="hidden inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-primary/10 text-primary font-semibold">
                            <span class="material-symbols-outlined text-xs">category</span>
                            <span id="filtro-categoria-texto">Categoría</span>
                            <button type="button" id="btn-remover-filtro-categoria" class="hover:text-red-600 flex items-center ml-0.5 cursor-pointer" title="Quitar filtro de categoría">
                                <span class="material-symbols-outlined text-xs">close</span>
                            </button>
                        </span>
                        <span id="filtro-busqueda-badge" class="hidden inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-amber-100 text-amber-900 font-semibold">
                            <span class="material-symbols-outlined text-xs">search</span>
                            <span>Búsqueda: "<span id="filtro-busqueda-texto"></span>"</span>
                            <button type="button" id="btn-remover-filtro-busqueda" class="hover:text-red-600 flex items-center ml-0.5 cursor-pointer" title="Quitar búsqueda">
                                <span class="material-symbols-outlined text-xs">close</span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sin resultados -->
            <div id="sin-resultados" class="hidden py-16 px-4 text-center bg-surface-container-low/60 rounded-2xl border border-dashed border-outline-variant my-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-3xl text-outline">search_off</span>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-1">No se encontraron productos</h3>
                <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-5" id="sin-resultados-detalle">
                    No encontramos ningún producto que coincida con los criterios de búsqueda.
                </p>
                <button type="button" id="btn-limpiar-todos-filtros" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition-colors shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-base">refresh</span>
                    Restablecer filtros y ver todos
                </button>
            </div>

            <!-- Grilla Vertical 3 Columnas -->
            <div id="productos-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($productos as $prod)
                    @php
                        $presMinorista = $prod->presentaciones->where('tipo', 'unidad')->first();
                        $presMayorista = $prod->presentaciones->where('tipo', '!=', 'unidad')->first();
                        
                        // Si el usuario es mayorista y existe presentación mayorista, usar esos precios, sino minorista.
                        // Si no está logueado, mostramos ambos como referencia.
                        $isMayorista = auth()->check() && auth()->user()->tipo_cliente == 'mayorista';
                    @endphp

                    <article class="producto-card bg-surface-bright rounded-2xl border border-outline-variant/70 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden group"
                        data-nombre="{{ strtolower($prod->nombre_producto) }}"
                        data-categoria-id="{{ $prod->categoria_id }}"
                        data-categoria-nombre="{{ strtolower($prod->categoria?->nombre_categoria ?? '') }}"
                        data-descripcion="{{ strtolower($prod->descripcion_producto ?? '') }}">
                        
                        <!-- Imagen y Badges -->
                        <div class="relative h-56 w-full bg-gradient-to-b from-slate-50 to-white dark:from-slate-800/40 dark:to-slate-800/80 flex items-center justify-center p-6 overflow-hidden border-b border-outline-variant/40">
                            <img class="object-contain h-full w-full mix-blend-multiply dark:mix-blend-normal group-hover:scale-105 transition-transform duration-300" src="{{ $prod->imagen_url }}" alt="{{ $prod->nombre_producto }}">
                            
                            @if($prod->stock_actual > 0)
                                <span class="absolute top-3 left-3 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold px-2.5 py-1 rounded-full text-[11px] uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    En Stock
                                </span>
                            @else
                                <span class="absolute top-3 left-3 bg-rose-50 text-rose-700 border border-rose-200 font-bold px-2.5 py-1 rounded-full text-[11px] uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Sin Stock
                                </span>
                            @endif

                            @if($prod->categoria)
                                <span class="absolute top-3 right-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xs text-on-surface-variant font-semibold px-2.5 py-0.5 rounded-md text-[11px] border border-outline-variant/60 shadow-xs flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">category</span>
                                    {{ $prod->categoria->nombre_categoria }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Detalles del Producto -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">
                                    {{ explode(' - ', $prod->nombre_producto)[0] ?? 'Marca' }}
                                </span>
                                <h2 class="text-base font-bold text-on-surface mb-2 line-clamp-2 min-h-[44px] group-hover:text-emerald-700 transition-colors leading-snug">
                                    {{ $prod->nombre_producto }}
                                </h2>
                                @if($prod->descripcion_producto)
                                    <p class="text-xs text-on-surface-variant line-clamp-2 mb-4 leading-relaxed">
                                        {{ $prod->descripcion_producto }}
                                    </p>
                                @endif
                            </div>
                            
                            <div>
                                <!-- Caja de Precios -->
                                <div class="pt-3 pb-3 px-3.5 bg-surface-container-low/70 dark:bg-surface-variant/20 rounded-xl border border-outline-variant/40 flex flex-col gap-1 mb-4">
                                    @if($presMayorista)
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-on-surface-variant line-through">Minorista: ${{ number_format($presMinorista->precio_minorista, 0, ',', '.') }}</span>
                                            <span class="bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-bold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider">
                                                {{ $presMayorista->tipo }} x{{ $presMayorista->cantidad_contenida }}
                                            </span>
                                        </div>
                                        <div class="flex items-baseline justify-between mt-0.5">
                                            <div class="font-extrabold text-2xl text-slate-900 dark:text-white tracking-tight">
                                                $<span class="price-val">{{ number_format($isMayorista ? $presMayorista->precio_mayorista : $presMayorista->precio_minorista, 0, ',', '.') }}</span>
                                                <span class="text-xs font-normal text-slate-500">/total</span>
                                            </div>
                                            <span class="text-xs font-semibold text-on-surface-variant bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-outline-variant/40">
                                                {{ $isMayorista ? 'Mayorista' : 'Venta' }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-baseline justify-between">
                                            <div class="font-extrabold text-2xl text-slate-900 dark:text-white tracking-tight">
                                                $<span class="price-val">{{ number_format($presMinorista->precio_minorista ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <span class="text-xs font-semibold text-on-surface-variant bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-outline-variant/40">
                                                Por unidad
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Formulario de Compra -->
                                <form action="{{ route('carrito.agregar') }}" method="POST" class="flex flex-col gap-3">
                                    @csrf
                                    @if($prod->presentaciones->count() > 1)
                                        <div>
                                            <label class="text-[11px] font-bold text-on-surface-variant block mb-1">Presentación:</label>
                                            <div class="relative">
                                                <select name="presentacion_id" onchange="this.closest('article').querySelector('.price-val').innerText = this.options[this.selectedIndex].dataset.precio" class="w-full text-xs font-semibold py-2 pl-3 pr-8 bg-surface-bright border border-outline-variant rounded-xl focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 cursor-pointer appearance-none">
                                                    @foreach($prod->presentaciones as $pres)
                                                        @php
                                                            $precioPres = $isMayorista ? $pres->precio_mayorista : $pres->precio_minorista;
                                                        @endphp
                                                        <option value="{{ $pres->id }}" data-precio="{{ number_format($precioPres, 0, ',', '.') }}" {{ ($isMayorista && $pres->tipo != 'unidad') ? 'selected' : '' }}>
                                                             {{ ucfirst($pres->tipo) }} (x{{ $pres->cantidad_contenida }}) - ${{ number_format($precioPres, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="material-symbols-outlined text-base absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                                            </div>
                                        </div>
                                    @elseif($prod->presentaciones->isNotEmpty())
                                        <input type="hidden" name="presentacion_id" value="{{ $prod->presentaciones->first()->id }}">
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center border border-outline-variant rounded-xl bg-surface h-11 w-28 px-1">
                                            <button type="button" onclick="const input = this.nextElementSibling; if(parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;" class="w-8 h-8 rounded-lg text-on-surface-variant hover:text-emerald-700 hover:bg-emerald-50 transition-colors flex items-center justify-center cursor-pointer">
                                                <span class="material-symbols-outlined text-base">remove</span>
                                            </button>
                                            <input name="cantidad" class="w-8 text-center text-sm font-bold text-on-surface bg-transparent border-none p-0 focus:ring-0" min="1" max="999" type="number" value="1"/>
                                            <button type="button" onclick="const input = this.previousElementSibling; input.value = parseInt(input.value) + 1;" class="w-8 h-8 rounded-lg text-on-surface-variant hover:text-emerald-700 hover:bg-emerald-50 transition-colors flex items-center justify-center cursor-pointer">
                                                <span class="material-symbols-outlined text-base">add</span>
                                            </button>
                                        </div>
                                        @if($prod->stock_actual > 0)
                                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white h-11 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                                <span class="material-symbols-outlined text-[19px]">add_shopping_cart</span>
                                                Agregar
                                            </button>
                                        @else
                                            <button type="button" disabled class="flex-1 bg-slate-100 text-slate-400 h-11 rounded-xl text-sm font-bold cursor-not-allowed flex items-center justify-center gap-1 border border-slate-200">
                                                <span class="material-symbols-outlined text-base">block</span>
                                                Sin Stock
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</main>

<!-- BottomNavBar (Mobile Only) -->
<nav class="fixed bottom-0 w-full z-40 flex justify-around items-center py-2 px-4 md:hidden bg-surface-container-lowest dark:bg-inverse-surface shadow-[0_-4px_12px_rgba(0,0,0,0.1)] border-t border-outline-variant">
    <a class="flex flex-col items-center justify-center text-primary px-4 py-1" href="{{ route('catalogo') }}">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home</span>
        <span class="text-[10px] mt-0.5 font-bold">Inicio</span>
    </a>
    <button type="button" id="bottom-nav-categorias" class="flex flex-col items-center justify-center text-on-surface-variant hover:text-primary px-4 py-1 cursor-pointer transition-colors">
        <span class="material-symbols-outlined">category</span>
        <span class="text-[10px] mt-0.5 font-medium">Categorías</span>
    </button>
    <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1 relative" href="{{ route('carrito.index') }}">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span class="text-[10px] mt-0.5 font-bold">Carrito</span>
        @if($conteoCarrito > 0)
            <span class="absolute top-0 right-3 bg-error text-on-error text-[9px] font-bold rounded-full h-3.5 min-w-3.5 px-1 flex items-center justify-center">
                {{ $conteoCarrito }}
            </span>
        @endif
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1" href="{{ auth()->check() ? route('pedidos.index') : route('login') }}">
        <span class="material-symbols-outlined">receipt_long</span>
        <span class="text-[10px] mt-0.5 font-medium">Pedidos</span>
    </a>
</nav>

<!-- Floating Action Button (FAB) -->
<button class="fixed bottom-20 md:bottom-lg right-margin-mobile md:right-lg z-30 w-14 h-14 bg-secondary text-on-secondary rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all duration-200 cursor-pointer">
    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">chat_spark</span>
</button>

@auth
    <x-modal-logout />
@endauth

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inputs de búsqueda
    const desktopInput = document.getElementById('search-input-desktop');
    const mobileInput = document.getElementById('search-input-mobile');
    const desktopClear = document.getElementById('search-clear-desktop');
    const mobileClear = document.getElementById('search-clear-mobile');

    // Tarjetas y contenedor
    const productCards = Array.from(document.querySelectorAll('.producto-card'));
    const totalProductos = productCards.length;
    const contadorProductos = document.getElementById('contador-productos');
    const sinResultados = document.getElementById('sin-resultados');
    const sinResultadosDetalle = document.getElementById('sin-resultados-detalle');
    const gridProductos = document.getElementById('productos-grid');

    // Botones de categoría (exclusivamente pills y botones del sidebar/drawer, no las tarjetas)
    const categoryButtons = document.querySelectorAll('.categoria-pill, .categoria-btn');

    // Badges de filtros activos
    const badgeCategoria = document.getElementById('filtro-categoria-badge');
    const textoCategoria = document.getElementById('filtro-categoria-texto');
    const btnQuitarCat = document.getElementById('btn-remover-filtro-categoria');

    const badgeBusqueda = document.getElementById('filtro-busqueda-badge');
    const textoBusqueda = document.getElementById('filtro-busqueda-texto');
    const btnQuitarBusqueda = document.getElementById('btn-remover-filtro-busqueda');

    const btnLimpiarTodos = document.getElementById('btn-limpiar-todos-filtros');

    // Drawer móvil
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileNavCategorias = document.getElementById('bottom-nav-categorias');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');
    const mobileDrawerClose = document.getElementById('mobile-drawer-close');

    let currentCategory = 'todas';
    let currentSearch = '';

    // Normalizador de texto (sin acentos, minúsculas)
    function normalize(text) {
        return (text || '')
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    // Sincronizar inputs
    function syncInputs(val) {
        if (desktopInput && desktopInput.value !== val) {
            desktopInput.value = val;
        }
        if (mobileInput && mobileInput.value !== val) {
            mobileInput.value = val;
        }

        if (desktopClear) {
            desktopClear.classList.toggle('hidden', val.length === 0);
        }
        if (mobileClear) {
            mobileClear.classList.toggle('hidden', val.length === 0);
        }
    }

    // Actualizar estilos activos de categoría
    function updateActiveCategoryStyles(selectedId) {
        categoryButtons.forEach(btn => {
            const btnCat = btn.dataset.categoriaId;
            const isMatch = (btnCat === String(selectedId)) || (selectedId === 'todas' && btnCat === 'todas');

            if (btn.classList.contains('categoria-pill')) {
                if (isMatch) {
                    btn.classList.remove('bg-surface-container', 'hover:bg-surface-container-high', 'text-on-surface-variant');
                    btn.classList.add('bg-primary', 'text-white', 'shadow-sm', 'font-bold');
                } else {
                    btn.classList.remove('bg-primary', 'text-white', 'shadow-sm', 'font-bold');
                    btn.classList.add('bg-surface-container', 'hover:bg-surface-container-high', 'text-on-surface-variant');
                }
            } else if (btn.classList.contains('categoria-btn')) {
                const countBadge = btn.querySelector('.categoria-count');
                const nombreSpan = btn.querySelector('.categoria-nombre');

                // Asegurar que el texto de la categoría no reciba fondos o estilos de resaltado
                if (nombreSpan) {
                    nombreSpan.classList.remove('bg-surface-container-high', 'bg-white/20', 'text-white', 'text-on-surface-variant/70');
                }

                if (isMatch) {
                    btn.classList.remove('text-on-surface-variant', 'hover:bg-surface-container', 'font-medium');
                    btn.classList.add('bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
                    if (countBadge) {
                        countBadge.classList.remove('text-on-surface-variant/70', 'bg-surface-container-high');
                        countBadge.classList.add('bg-white/20', 'text-white');
                    }
                } else {
                    btn.classList.remove('bg-primary', 'text-white', 'shadow-sm', 'font-semibold');
                    btn.classList.add('text-on-surface-variant', 'hover:bg-surface-container', 'font-medium');
                    if (countBadge) {
                        countBadge.classList.remove('bg-white/20', 'text-white');
                        countBadge.classList.add('text-on-surface-variant/70', 'bg-surface-container-high');
                    }
                }
            }
        });
    }

    // Actualizar URL sin recargar
    function updateUrl() {
        const params = new URLSearchParams();
        if (currentCategory && currentCategory !== 'todas') {
            params.set('categoria', currentCategory);
        }
        if (currentSearch && currentSearch.trim() !== '') {
            params.set('buscar', currentSearch.trim());
        }
        const qs = params.toString();
        const newUrl = qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    // Filtrar productos dinámicamente
    function filtrar() {
        const queryNorm = normalize(currentSearch);
        const terms = queryNorm.split(/\s+/).filter(Boolean);

        let visibleCount = 0;

        productCards.forEach(card => {
            const cardCatId = card.dataset.categoriaId;
            const matchesCat = (currentCategory === 'todas' || currentCategory === cardCatId);

            let matchesSearch = true;
            if (terms.length > 0) {
                const cardNombre = normalize(card.dataset.nombre || '');
                const cardDesc = normalize(card.dataset.descripcion || '');
                const cardCatNombre = normalize(card.dataset.categoriaNombre || '');

                matchesSearch = terms.every(t =>
                    cardNombre.includes(t) || cardDesc.includes(t) || cardCatNombre.includes(t)
                );
            }

            if (matchesCat && matchesSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Actualizar contador
        if (currentCategory === 'todas' && !queryNorm) {
            contadorProductos.textContent = `Mostrando ${visibleCount} productos`;
        } else {
            contadorProductos.textContent = `Mostrando ${visibleCount} de ${totalProductos} productos`;
        }

        // Estado sin resultados
        if (visibleCount === 0) {
            sinResultados.classList.remove('hidden');
            gridProductos.classList.add('hidden');

            const motivos = [];
            if (queryNorm) {
                motivos.push(`término "${currentSearch}"`);
            }
            if (currentCategory !== 'todas') {
                const activeBtn = document.querySelector(`.categoria-pill[data-categoria-id="${currentCategory}"] span:nth-child(2)`);
                const catName = activeBtn ? activeBtn.textContent.trim() : 'la categoría seleccionada';
                motivos.push(`categoría "${catName}"`);
            }
            sinResultadosDetalle.textContent = `No encontramos coincidencias para ${motivos.join(' con ')}. Probá con otros términos o seleccionando otra categoría.`;
        } else {
            sinResultados.classList.add('hidden');
            gridProductos.classList.remove('hidden');
        }

        // Actualizar badges
        if (currentCategory !== 'todas') {
            const activeBtn = document.querySelector(`.categoria-pill[data-categoria-id="${currentCategory}"] span:nth-child(2)`);
            textoCategoria.textContent = activeBtn ? activeBtn.textContent.trim() : 'Categoría';
            badgeCategoria.classList.remove('hidden');
        } else {
            badgeCategoria.classList.add('hidden');
        }

        if (queryNorm) {
            textoBusqueda.textContent = currentSearch;
            badgeBusqueda.classList.remove('hidden');
        } else {
            badgeBusqueda.classList.add('hidden');
        }

        updateUrl();
    }

    // Eventos de búsqueda instantánea al teclear (evento input)
    function onSearchInput(e) {
        currentSearch = e.target.value;
        syncInputs(currentSearch);
        filtrar();
    }

    if (desktopInput) {
        desktopInput.addEventListener('input', onSearchInput);
    }
    if (mobileInput) {
        mobileInput.addEventListener('input', onSearchInput);
    }

    // Botones de limpiar búsqueda en inputs
    if (desktopClear) {
        desktopClear.addEventListener('click', () => {
            currentSearch = '';
            syncInputs('');
            desktopInput.focus();
            filtrar();
        });
    }

    if (mobileClear) {
        mobileClear.addEventListener('click', () => {
            currentSearch = '';
            syncInputs('');
            mobileInput.focus();
            filtrar();
        });
    }

    // Selección de categorías
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const catId = btn.dataset.categoriaId;
            currentCategory = catId;
            updateActiveCategoryStyles(catId);
            filtrar();
            closeMobileDrawer();

            // Centrar pill en pantalla si está fuera de vista
            const matchingPill = document.querySelector(`.categoria-pill[data-categoria-id="${catId}"]`);
            if (matchingPill && typeof matchingPill.scrollIntoView === 'function') {
                matchingPill.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        });
    });

    // Remover filtro de categoría individual
    if (btnQuitarCat) {
        btnQuitarCat.addEventListener('click', () => {
            currentCategory = 'todas';
            updateActiveCategoryStyles('todas');
            filtrar();
        });
    }

    // Remover filtro de búsqueda individual
    if (btnQuitarBusqueda) {
        btnQuitarBusqueda.addEventListener('click', () => {
            currentSearch = '';
            syncInputs('');
            filtrar();
        });
    }

    // Limpiar todos los filtros
    if (btnLimpiarTodos) {
        btnLimpiarTodos.addEventListener('click', () => {
            currentCategory = 'todas';
            currentSearch = '';
            syncInputs('');
            updateActiveCategoryStyles('todas');
            filtrar();
        });
    }

    // Mobile Drawer Controls
    function openMobileDrawer() {
        if (!mobileDrawer || !mobileDrawerBackdrop) return;
        mobileDrawerBackdrop.classList.remove('hidden');
        requestAnimationFrame(() => {
            mobileDrawerBackdrop.classList.remove('opacity-0');
            mobileDrawer.classList.remove('-translate-x-full');
        });
        document.body.classList.add('overflow-hidden');
    }

    function closeMobileDrawer() {
        if (!mobileDrawer || !mobileDrawerBackdrop) return;
        mobileDrawer.classList.add('-translate-x-full');
        mobileDrawerBackdrop.classList.add('opacity-0');
        setTimeout(() => {
            mobileDrawerBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', openMobileDrawer);
    }
    if (mobileNavCategorias) {
        mobileNavCategorias.addEventListener('click', (e) => {
            e.preventDefault();
            openMobileDrawer();
        });
    }
    if (mobileDrawerClose) {
        mobileDrawerClose.addEventListener('click', closeMobileDrawer);
    }
    if (mobileDrawerBackdrop) {
        mobileDrawerBackdrop.addEventListener('click', closeMobileDrawer);
    }

    // Inicializar con parámetros URL si existen
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('categoria')) {
        currentCategory = urlParams.get('categoria');
        updateActiveCategoryStyles(currentCategory);
    }
    if (urlParams.has('buscar')) {
        currentSearch = urlParams.get('buscar');
        syncInputs(currentSearch);
    }

    if (urlParams.has('categoria') || urlParams.has('buscar')) {
        filtrar();
    }
});
</script>
</body>
</html>
