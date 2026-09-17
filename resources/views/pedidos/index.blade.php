<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mis Pedidos - HyK Mayorista</title>
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
        <a href="{{ route('catalogo') }}" class="flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">storefront</span>
            Catálogo
        </a>

        @php
            $conteoCarrito = app(\App\Services\CartService::class)->conteoTotal();
        @endphp
        <a href="{{ route('carrito.index') }}" class="relative p-2 text-primary hover:bg-surface-variant/40 rounded-full transition-colors flex items-center" title="Mi Carrito">
            <span class="material-symbols-outlined">shopping_cart</span>
            @if($conteoCarrito > 0)
                <span class="absolute top-0 right-0 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-600 text-white text-[10px] font-bold">
                    {{ $conteoCarrito }}
                </span>
            @endif
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
<main class="flex-1 max-w-6xl w-full mx-auto p-4 md:p-8">
    <!-- Breadcrumb & Título -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-2">
            <a href="{{ route('catalogo') }}" class="hover:underline">Catálogo</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-primary">Mis Pedidos</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-3xl text-primary">receipt_long</span>
                    Historial de Mis Pedidos
                </h1>
                <p class="text-sm text-on-surface-variant mt-1">
                    Consulta el estado, detalle y seguimiento de todas tus compras realizadas.
                </p>
            </div>
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-1.5 bg-primary text-on-primary px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary-container transition-colors shadow-sm self-start sm:self-auto">
                <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                Nuevo Pedido
            </a>
        </div>
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

    @if($pedidos->isEmpty())
        <!-- Estado Vacío -->
        <div class="bg-surface-bright border border-outline-variant rounded-2xl p-12 text-center max-w-md mx-auto shadow-sm my-10">
            <div class="w-20 h-20 bg-surface-variant/50 rounded-full flex items-center justify-center mx-auto mb-4 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl text-primary">receipt_long</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Aún no tienes pedidos</h2>
            <p class="text-sm text-on-surface-variant mb-6">
                Cuando realices una compra en nuestro catálogo mayorista y minorista, podrás consultar aquí su estado y detalle.
            </p>
            <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-primary-container transition-colors shadow">
                <span class="material-symbols-outlined text-lg">storefront</span>
                Explorar Catálogo
            </a>
        </div>
    @else
        <!-- Lista de Pedidos (Responsive) -->
        <div class="bg-surface-bright border border-outline-variant rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-surface-variant/40 border-b border-outline-variant text-xs uppercase font-bold text-on-surface-variant">
                            <th class="py-3.5 px-4 md:px-6">N° Pedido</th>
                            <th class="py-3.5 px-4 md:px-6">Fecha</th>
                            <th class="py-3.5 px-4 md:px-6">Artículos</th>
                            <th class="py-3.5 px-4 md:px-6">Total</th>
                            <th class="py-3.5 px-4 md:px-6">Estado</th>
                            <th class="py-3.5 px-4 md:px-6 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @foreach($pedidos as $pedido)
                            <tr class="hover:bg-surface transition-colors">
                                <!-- ID -->
                                <td class="py-4 px-4 md:px-6 font-bold text-primary whitespace-nowrap">
                                    #{{ $pedido->id }}
                                </td>

                                <!-- Fecha -->
                                <td class="py-4 px-4 md:px-6 text-on-surface-variant whitespace-nowrap">
                                    {{ $pedido->fecha_pedido ? \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y') : $pedido->created_at->format('d/m/Y') }}
                                </td>

                                <!-- Artículos -->
                                <td class="py-4 px-4 md:px-6 text-on-surface whitespace-nowrap">
                                    <span class="font-medium">
                                        {{ $pedido->detalles->sum('cantidad') }} unidad(es)
                                    </span>
                                    <span class="text-xs text-on-surface-variant block">
                                        ({{ $pedido->detalles->count() }} producto(s))
                                    </span>
                                </td>

                                <!-- Total -->
                                <td class="py-4 px-4 md:px-6 font-bold text-on-surface whitespace-nowrap">
                                    ${{ number_format($pedido->total, 0, ',', '.') }}
                                </td>

                                <!-- Estado Badge -->
                                <td class="py-4 px-4 md:px-6 whitespace-nowrap">
                                    @if($pedido->estado === 'PENDIENTE')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-100 border border-amber-200 px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pendiente
                                        </span>
                                    @elseif($pedido->estado === 'CONFIRMADO')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-800 bg-blue-100 border border-blue-200 px-3 py-1 rounded-full">
                                            <span class="material-symbols-outlined text-xs">done</span>
                                            Confirmado
                                        </span>
                                    @elseif($pedido->estado === 'PAGADO')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-green-800 bg-green-100 border border-green-200 px-3 py-1 rounded-full">
                                            <span class="material-symbols-outlined text-xs">payments</span>
                                            Pagado
                                        </span>
                                    @elseif($pedido->estado === 'ENTREGADO')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-200 px-3 py-1 rounded-full">
                                            <span class="material-symbols-outlined text-xs">local_shipping</span>
                                            Entregado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-800 bg-red-100 border border-red-200 px-3 py-1 rounded-full">
                                            {{ $pedido->estado }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Botón Detalle -->
                                <td class="py-4 px-4 md:px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('pedidos.show', $pedido->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-container border border-primary/30 rounded-lg px-3 py-1.5 hover:bg-primary/5 transition-colors">
                                        <span>Ver Detalle</span>
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($pedidos->hasPages())
                <div class="p-4 border-t border-outline-variant bg-surface">
                    {{ $pedidos->links() }}
                </div>
            @endif
        </div>
    @endif
</main>

<footer class="mt-auto border-t border-outline-variant py-4 bg-surface text-center text-xs text-on-surface-variant">
    Plataforma HyK Mayorista &copy; {{ date('Y') }} - Seminario de Integración
</footer>

</body>
</html>
