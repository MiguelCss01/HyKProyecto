<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Carrito de Compras - HyK Mayorista</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00317e",
                        "primary-container": "#0046ad",
                        "on-primary": "#ffffff",
                        "on-primary-container": "#a5bdff",
                        "secondary": "#006e25",
                        "secondary-container": "#80f98b",
                        "on-secondary": "#ffffff",
                        "on-secondary-container": "#007327",
                        "background": "#f8f9fa",
                        "surface": "#f8f9fa",
                        "surface-bright": "#ffffff",
                        "surface-variant": "#e1e3e4",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#434653",
                        "outline-variant": "#c3c6d5",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error": "#ffffff",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">

<!-- TopNavBar -->
<header class="h-16 bg-surface-bright border-b border-outline-variant flex items-center justify-between px-4 md:px-8 sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <a href="{{ route('catalogo') }}" class="flex items-center gap-2 text-primary font-bold text-xl tracking-tight">
            <span class="material-symbols-outlined text-2xl">storefront</span>
            HyK Mayorista
        </a>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('catalogo') }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al Catálogo
        </a>

        @auth
            <span class="hidden md:inline text-sm font-bold text-on-surface-variant">
                Hola, {{ auth()->user()->name }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">Salir</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-sm font-bold text-primary hover:underline">
                Ingresar / Registrarse
            </a>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-6xl w-full mx-auto p-4 md:p-8">
    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-primary">shopping_cart</span>
                Mi Carrito de Compras
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">
                @if($cart['total_items'] > 0)
                    Tienes <span class="font-bold text-primary">{{ $cart['total_items'] }}</span> artículo(s) seleccionado(s).
                @else
                    Tu carrito se encuentra actualmente vacío.
                @endif
            </p>
        </div>

        @if(!empty($cart['items']))
            <form action="{{ route('carrito.vaciar') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas vaciar todo el carrito?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 flex items-center gap-1 border border-red-200 rounded px-3 py-1.5 hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span>
                    Vaciar Carrito
                </button>
            </form>
        @endif
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if(empty($cart['items']))
        <!-- Carrito Vacío -->
        <div class="bg-surface-bright border border-outline-variant rounded-xl p-12 text-center max-w-lg mx-auto shadow-sm my-8">
            <div class="w-20 h-20 bg-surface-variant rounded-full flex items-center justify-center mx-auto mb-4 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl">shopping_cart_checkout</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Tu carrito está vacío</h2>
            <p class="text-sm text-on-surface-variant mb-6">
                Aún no has agregado ningún producto a tu lista de compra. Explora nuestro catálogo de productos mayoristas y minoristas.
            </p>
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-primary-container transition-colors shadow">
                <span class="material-symbols-outlined text-lg">storefront</span>
                Ir al Catálogo de Productos
            </a>
        </div>
    @else
        <!-- Contenido del Carrito (2 Columnas) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Columna Izquierda: Lista de Productos -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                @foreach($cart['items'] as $presentacionId => $item)
                    <article class="bg-surface-bright border border-outline-variant rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-center gap-4 shadow-sm hover:shadow transition-shadow">
                        <!-- Imagen -->
                        <div class="w-24 h-24 sm:w-28 sm:h-28 bg-background rounded-lg border border-outline-variant flex-shrink-0 flex items-center justify-center p-2">
                            <img class="object-contain max-h-full max-w-full mix-blend-multiply" src="{{ $item['producto']->imagen_url }}" alt="{{ $item['producto']->nombre_producto }}">
                        </div>

                        <!-- Detalles del Producto -->
                        <div class="flex-1 min-w-0 text-center sm:text-left">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider block">
                                {{ $item['producto']->categoria?->nombre_categoria ?? 'General' }}
                            </span>
                            <h2 class="text-base font-bold text-on-surface truncate">
                                {{ $item['producto']->nombre_producto }}
                            </h2>

                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-1">
                                <span class="bg-surface-variant text-on-surface-variant text-[11px] font-bold px-2 py-0.5 rounded uppercase">
                                    {{ $item['presentacion']->tipo }} (x{{ $item['presentacion']->cantidad_contenida }})
                                </span>
                                <span class="text-xs text-on-surface-variant">
                                    Stock disponible: <strong class="text-on-surface">{{ $item['stock_max_presentacion'] }}</strong>
                                </span>
                            </div>

                            <div class="mt-2 text-sm text-on-surface">
                                Precio unitario:
                                <span class="font-bold text-primary">
                                    ${{ number_format($item['precio_unitario'], 0, ',', '.') }}
                                </span>
                                @if($cart['is_mayorista'])
                                    <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded ml-1">Mayorista</span>
                                @else
                                    <span class="text-[10px] font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded ml-1">Minorista</span>
                                @endif
                            </div>
                        </div>

                        <!-- Controles de Cantidad y Subtotal -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 border-t sm:border-t-0 pt-3 sm:pt-0">
                            <!-- Modificador de Cantidad -->
                            <form action="{{ route('carrito.actualizar', $presentacionId) }}" method="POST" class="flex items-center border border-outline-variant rounded-lg bg-surface h-9">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="cantidad" value="{{ $item['cantidad'] - 1 }}" class="px-2 h-full text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" title="Disminuir una unidad">
                                    <span class="material-symbols-outlined text-sm">remove</span>
                                </button>
                                <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="{{ max(1, $item['stock_max_presentacion']) }}" onchange="this.form.submit()" class="w-12 text-center text-sm font-bold text-on-surface bg-transparent border-none p-0 focus:ring-0" title="Editar cantidad"/>
                                <button type="submit" name="cantidad" value="{{ $item['cantidad'] + 1 }}" class="px-2 h-full text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" title="Aumentar una unidad">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </form>

                            <!-- Subtotal -->
                            <div class="text-right">
                                <span class="text-[11px] text-on-surface-variant block uppercase">Subtotal</span>
                                <span class="text-lg font-bold text-primary">
                                    ${{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Botón Eliminar Ítem -->
                            <form action="{{ route('carrito.eliminar', $presentacionId) }}" method="POST" onsubmit="return confirm('¿Quitar este artículo del carrito?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-on-surface-variant hover:text-red-600 transition-colors p-1" title="Eliminar del carrito">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Columna Derecha: Resumen de Compra -->
            <div class="lg:col-span-4">
                <div class="bg-surface-bright border border-outline-variant rounded-xl p-6 shadow-sm sticky top-24">
                    <h2 class="text-lg font-bold text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        Resumen del Pedido
                    </h2>

                    <!-- Información de Tipo de Cliente / Precios -->
                    @if($cart['is_mayorista'])
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-start gap-2">
                            <span class="material-symbols-outlined text-green-600 text-lg flex-shrink-0 mt-0.5">verified</span>
                            <div class="text-xs text-green-900">
                                <strong>Condición Mayorista Activa:</strong> Estás accediendo a la lista de precios mayoristas con descuento por volumen.
                            </div>
                        </div>
                    @else
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-lg flex-shrink-0 mt-0.5">info</span>
                            <div class="text-xs text-blue-900">
                                @auth
                                    <strong>Cliente Minorista:</strong> Precios calculados según la lista minorista oficial.
                                @else
                                    <strong>Comprando como Invitado:</strong>
                                    ¿Tienes cuenta mayorista? <a href="{{ route('login') }}" class="font-bold underline hover:text-blue-950">Inicia sesión</a> para aplicar precios mayoristas automáticamente.
                                @endauth
                            </div>
                        </div>
                    @endif

                    <!-- Detalle Financiero -->
                    <div class="space-y-2 py-2 text-sm text-on-surface-variant">
                        <div class="flex justify-between">
                            <span>Artículos totales:</span>
                            <span class="font-bold text-on-surface">{{ $cart['total_items'] }} unidades</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal estimado:</span>
                            <span class="font-bold text-on-surface">${{ number_format($cart['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Retiro en local:</span>
                            <span class="font-bold text-green-600 uppercase">Sin costo adicional</span>
                        </div>
                    </div>

                    <div class="border-t border-outline-variant my-4 pt-4 flex justify-between items-baseline">
                        <span class="text-base font-bold text-on-surface">Total General</span>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-primary">
                                ${{ number_format($cart['total'], 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] text-gray-500 block">IVA incluido</span>
                        </div>
                    </div>

                    <!-- Botón de Continuar Compra (RF 3.2) -->
                    <button type="button" onclick="alert('Módulo de confirmación de pedido (RF 3.2 / Checkout) listo para conectarse.');" class="w-full bg-secondary text-on-secondary font-bold py-3 px-4 rounded-lg hover:bg-[#00531a] transition-colors flex items-center justify-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                        Iniciar Pedido / Confirmar
                    </button>

                    <a href="{{ route('catalogo') }}" class="w-full mt-3 text-center text-xs font-bold text-primary hover:underline block">
                        &larr; Seguir sumando productos
                    </a>
                </div>
            </div>

        </div>
    @endif
</main>

<footer class="mt-auto border-t border-outline-variant py-4 bg-surface text-center text-xs text-on-surface-variant">
    Plataforma HyK Mayorista &copy; {{ date('Y') }} - Seminario de Integración
</footer>

</body>
</html>
