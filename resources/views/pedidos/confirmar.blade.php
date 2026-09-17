<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Confirmación de Pedido - HyK Mayorista</title>
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
                        "secondary": "#006e25",
                        "on-secondary": "#ffffff",
                        "background": "#f8f9fa",
                        "surface": "#f8f9fa",
                        "surface-bright": "#ffffff",
                        "surface-variant": "#e1e3e4",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#434653",
                        "outline-variant": "#c3c6d5",
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
        <a href="{{ route('carrito.index') }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al Carrito
        </a>

        @auth
            <span class="hidden md:inline text-sm font-bold text-on-surface-variant">
                Hola, {{ auth()->user()->name }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">Salir</button>
            </form>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-5xl w-full mx-auto p-4 md:p-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-2">
            <a href="{{ route('catalogo') }}" class="hover:underline">Catálogo</a>
            <span>&rsaquo;</span>
            <a href="{{ route('carrito.index') }}" class="hover:underline">Carrito</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-primary">Confirmar Pedido</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-3xl text-secondary">fact_check</span>
            Confirmación y Cierre de Pedido
        </h1>
        <p class="text-sm text-on-surface-variant mt-1">
            Por favor, revisa el detalle de los productos y tus datos de contacto antes de confirmar la compra.
        </p>
    </div>

    <!-- Alertas Flash -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Columna Izquierda: Datos del Comprador y Artículos -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Bloque Datos del Comprador -->
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-6 shadow-sm">
                <h2 class="text-base font-bold text-on-surface mb-3 flex items-center gap-2 border-b border-outline-variant pb-2">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    Datos del Comprador Autenticado
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-on-surface-variant block">Nombre Completo:</span>
                        <span class="font-semibold text-on-surface">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Correo Electrónico:</span>
                        <span class="font-semibold text-on-surface">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Teléfono:</span>
                        <span class="font-semibold text-on-surface">{{ $user->telefono ?? 'No especificado' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Condición Comercial:</span>
                        @if($user->tipo_cliente === 'mayorista')
                            <span class="inline-flex items-center text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded">
                                Mayorista ({{ $user->razon_social ?? 'Razón Social: ' . $user->cuit }})
                            </span>
                        @else
                            <span class="inline-flex items-center text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                Minorista (DNI: {{ $user->dni ?? 'S/D' }})
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bloque Resumen de Productos -->
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-6 shadow-sm">
                <h2 class="text-base font-bold text-on-surface mb-3 flex items-center gap-2 border-b border-outline-variant pb-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Artículos en el Pedido ({{ $cart['total_items'] }})
                </h2>
                <div class="divide-y divide-outline-variant">
                    @foreach($cart['items'] as $item)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-on-surface truncate">{{ $item['producto']->nombre_producto }}</h3>
                                <p class="text-xs text-on-surface-variant">
                                    {{ $item['presentacion']->tipo }} (x{{ $item['presentacion']->cantidad_contenida }}) &bull; Cantidad: <strong class="text-on-surface">{{ $item['cantidad'] }}</strong>
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs text-on-surface-variant block">
                                    ${{ number_format($item['precio_unitario'], 0, ',', '.') }} c/u
                                </span>
                                <span class="text-sm font-bold text-primary">
                                    ${{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Confirmación Final -->
        <div class="lg:col-span-5">
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-6 shadow-sm sticky top-24">
                <h2 class="text-base font-bold text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">shopping_bag</span>
                    Total a Pagar
                </h2>

                <div class="space-y-2 py-2 text-sm text-on-surface-variant">
                    <div class="flex justify-between">
                        <span>Total de unidades:</span>
                        <span class="font-bold text-on-surface">{{ $cart['total_items'] }} unidades</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Modalidad de entrega:</span>
                        <span class="font-bold text-green-700">Retiro en local (Gratis)</span>
                    </div>
                </div>

                <div class="border-t border-outline-variant my-4 pt-4 flex justify-between items-baseline">
                    <span class="text-base font-bold text-on-surface">Total General:</span>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-primary">
                            ${{ number_format($cart['total'], 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-gray-500 block">IVA incluido</span>
                    </div>
                </div>

                <!-- Formulario de Confirmación Definitiva -->
                <form action="{{ route('pedidos.confirmar') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-secondary text-on-secondary font-bold py-3.5 px-4 rounded-lg hover:bg-[#00531a] transition-colors flex items-center justify-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                        Confirmar Pedido Definitivo
                    </button>
                </form>

                <a href="{{ route('carrito.index') }}" class="w-full mt-3 text-center text-xs font-bold text-on-surface-variant hover:text-primary hover:underline block">
                    &larr; Volver y modificar el carrito
                </a>
            </div>
        </div>
    </div>
</main>

<footer class="mt-auto border-t border-outline-variant py-4 bg-surface text-center text-xs text-on-surface-variant">
    Plataforma HyK Mayorista &copy; {{ date('Y') }} - Seminario de Integración
</footer>

</body>
</html>
