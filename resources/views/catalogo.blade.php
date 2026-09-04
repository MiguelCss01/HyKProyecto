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
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased">
<!-- TopNavBar -->
<header class="h-16 bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-tertiary flex items-center justify-between px-margin-mobile md:px-margin-desktop sticky top-0 z-50">
    <div class="flex items-center gap-sm">
        <button class="md:hidden p-2 text-on-surface-variant hover:bg-surface-container-low rounded-full">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="/" class="font-headline-lg font-bold text-primary dark:text-primary-fixed-dim tracking-tight text-xl">HyK Mayorista</a>
    </div>
    <div class="hidden md:flex flex-1 max-w-2xl mx-lg relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
        <input class="w-full pl-10 pr-4 py-2 bg-surface-bright border border-outline-variant rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-body-md" placeholder="Buscar productos, marcas o códigos..." type="text"/>
    </div>
    <div class="flex items-center gap-sm">
        @auth
            <span class="hidden md:block mr-4 text-sm font-bold text-primary">Hola, {{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-sm font-bold text-red-600 hover:underline mr-4" type="submit">Salir</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-sm font-bold text-primary hover:underline mr-4">Ingresar / Registrarse</a>
        @endauth

        <button class="p-2 text-primary dark:text-primary-fixed-dim hover:bg-surface-container-low transition-colors rounded-full relative">
            <span class="material-symbols-outlined">shopping_cart</span>
            <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-error text-on-error text-[10px] font-bold leading-none">0</span>
        </button>
    </div>
</header>

<!-- SideNavBar (Desktop) -->
<nav class="hidden md:flex flex-col fixed left-0 top-16 h-[calc(100vh-64px)] w-64 z-40 bg-surface-bright dark:bg-surface-dim border-r border-outline-variant font-body-md">
    <div class="p-lg border-b border-outline-variant flex items-center gap-sm">
        <div class="w-10 h-10 bg-primary-container rounded-full flex items-center justify-center text-on-primary-container font-bold">
            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'B' }}
        </div>
        <div>
            <p class="font-label-md font-bold text-on-surface">{{ auth()->check() ? auth()->user()->name : 'Bienvenido' }}</p>
            <p class="text-sm text-on-surface-variant">{{ auth()->check() ? (auth()->user()->tipo_cliente == 'mayorista' ? 'Cliente Mayorista' : 'Cliente Minorista') : 'Inicie sesión' }}</p>
        </div>
    </div>
    <ul class="flex flex-col py-sm flex-1 overflow-y-auto">
        @foreach($categorias as $cat)
            <li class="px-sm py-xs">
                <a class="flex items-center gap-sm px-4 py-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-all" href="#">
                    <span class="material-symbols-outlined">category</span>
                    <span>{{ $cat->nombre_categoria }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>

<!-- Main Content Canvas -->
<main class="md:ml-64 flex-1 h-full w-full max-w-[1440px] mx-auto pb-20 md:pb-0">
    <!-- Mobile Search Bar -->
    <div class="md:hidden px-margin-mobile py-sm bg-surface sticky top-16 z-30 shadow-sm border-b border-outline-variant">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-surface-bright border border-outline-variant rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Buscar..." type="text"/>
        </div>
    </div>

    <!-- Product Grid -->
    <section class="px-margin-mobile md:px-lg py-lg">
        <div class="flex items-center justify-between mb-lg">
            <h1 class="text-2xl font-bold text-on-surface">Catálogo de Productos</h1>
            <span class="text-sm text-on-surface-variant">Mostrando {{ $productos->count() }} productos</span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-gutter">
            @foreach($productos as $prod)
                @php
                    $presMinorista = $prod->presentaciones->where('tipo', 'unidad')->first();
                    $presMayorista = $prod->presentaciones->where('tipo', '!=', 'unidad')->first();
                    
                    // Si el usuario es mayorista y existe presentación mayorista, usar esos precios, sino minorista.
                    // Si no está logueado, mostramos ambos como referencia.
                    $isMayorista = auth()->check() && auth()->user()->tipo_cliente == 'mayorista';
                @endphp

                <article class="bg-surface-container-lowest rounded shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border border-outline-variant flex flex-col hover:shadow-lg transition-shadow duration-200">
                    <div class="relative h-48 w-full bg-surface-bright rounded-t flex items-center justify-center p-md">
                        <img class="object-contain h-full w-full mix-blend-multiply" src="{{ $prod->imagen_url }}" alt="{{ $prod->nombre_producto }}">
                        @if($prod->stock_actual > 0)
                            <span class="absolute top-2 left-2 bg-secondary-container text-on-secondary-container font-bold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider">En Stock</span>
                        @else
                            <span class="absolute top-2 left-2 bg-error-container text-on-error-container font-bold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider">Sin Stock</span>
                        @endif
                    </div>
                    
                    <div class="p-md flex-1 flex flex-col">
                        <span class="text-xs text-on-surface-variant mb-1 font-bold">{{ explode(' - ', $prod->nombre_producto)[0] ?? 'Marca' }}</span>
                        <h2 class="text-sm font-bold text-on-surface mb-2 line-clamp-2 min-h-[40px]">{{ $prod->nombre_producto }}</h2>
                        
                        <div class="mt-auto pt-sm flex flex-col gap-1">
                            @if($presMayorista)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-on-surface-variant line-through">Minorista: ${{ number_format($presMinorista->precio_minorista, 0, ',', '.') }}</span>
                                    <span class="bg-surface-variant text-on-surface-variant px-1.5 py-0.5 rounded text-[10px] font-bold uppercase">{{ $presMayorista->tipo }} x{{ $presMayorista->cantidad_contenida }}</span>
                                </div>
                                <div class="font-bold text-xl text-primary">
                                    ${{ number_format($isMayorista ? $presMayorista->precio_mayorista : $presMayorista->precio_minorista, 0, ',', '.') }} 
                                    <span class="text-xs font-normal text-on-surface-variant">/total</span>
                                </div>
                            @else
                                <div class="font-bold text-xl text-primary mt-4">
                                    ${{ number_format($presMinorista->precio_minorista ?? 0, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 flex items-center gap-2">
                            <div class="flex items-center border border-outline-variant rounded bg-surface h-10 w-24">
                                <button class="px-2 text-on-surface-variant hover:text-primary transition-colors flex-1 flex items-center justify-center">
                                    <span class="material-symbols-outlined">remove</span>
                                </button>
                                <input class="w-8 text-center text-sm font-bold text-on-surface bg-transparent border-none p-0 focus:ring-0" min="1" type="number" value="1"/>
                                <button class="px-2 text-on-surface-variant hover:text-primary transition-colors flex-1 flex items-center justify-center">
                                    <span class="material-symbols-outlined">add</span>
                                </button>
                            </div>
                            <button class="flex-1 bg-secondary text-on-secondary h-10 rounded text-sm font-bold hover:bg-[#00531a] transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                Agregar
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</main>

<!-- BottomNavBar (Mobile Only) -->
<nav class="fixed bottom-0 w-full z-50 flex justify-around items-center py-2 px-4 md:hidden bg-surface-container-lowest dark:bg-inverse-surface shadow-[0_-4px_12px_rgba(0,0,0,0.1)] border-t border-outline-variant">
    <a class="flex flex-col items-center justify-center text-primary px-4 py-1" href="#">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home</span>
        <span class="text-[10px] mt-0.5 font-bold">Inicio</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1" href="#">
        <span class="material-symbols-outlined">category</span>
        <span class="text-[10px] mt-0.5">Categorías</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-1" href="#">
        <span class="material-symbols-outlined">receipt_long</span>
        <span class="text-[10px] mt-0.5">Pedidos</span>
    </a>
</nav>

<!-- Floating Action Button (FAB) -->
<button class="fixed bottom-20 md:bottom-lg right-margin-mobile md:right-lg z-50 w-14 h-14 bg-secondary text-on-secondary rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all duration-200">
    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">chat_spark</span>
</button>

</body>
</html>
