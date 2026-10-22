<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Detalle del Pedido #{{ $pedido->id }} - HyK Mayorista</title>
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
        <a href="{{ route('pedidos.index') }}" class="flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver a Mis Pedidos
        </a>

        @auth
            <span class="hidden md:inline text-sm font-bold text-on-surface-variant">
                Hola, {{ auth()->user()->name }}
            </span>
            <button type="button" onclick="openLogoutModal()" class="text-xs font-bold text-red-600 hover:underline cursor-pointer">Salir</button>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-5xl w-full mx-auto p-4 md:p-8">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-2">
            <a href="{{ route('catalogo') }}" class="hover:underline">Catálogo</a>
            <span>&rsaquo;</span>
            <a href="{{ route('pedidos.index') }}" class="hover:underline">Mis Pedidos</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-primary">Pedido #{{ $pedido->id }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-3xl text-primary">description</span>
                    Detalle del Pedido #{{ $pedido->id }}
                </h1>
                <p class="text-sm text-on-surface-variant mt-1">
                    Emitido el {{ $pedido->fecha_pedido ? \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y') : $pedido->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <!-- Badge de Estado -->
            <div>
                @if($pedido->estado === 'PENDIENTE')
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-amber-800 bg-amber-100 border border-amber-300 px-4 py-1.5 rounded-full shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Estado: Pendiente
                    </span>
                @elseif($pedido->estado === 'CONFIRMADO')
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-800 bg-blue-100 border border-blue-300 px-4 py-1.5 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-base">done</span>
                        Estado: Confirmado
                    </span>
                @elseif($pedido->estado === 'PAGADO')
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-green-800 bg-green-100 border border-green-300 px-4 py-1.5 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-base">payments</span>
                        Estado: Pagado
                    </span>
                @elseif($pedido->estado === 'ENTREGADO')
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-4 py-1.5 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-base">local_shipping</span>
                        Estado: Entregado
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-red-800 bg-red-100 border border-red-300 px-4 py-1.5 rounded-full shadow-sm">
                        Estado: {{ $pedido->estado }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Aviso Informativo según Estado -->
    @if($pedido->estado === 'PAGADO' || $pedido->pago)
        <div class="mb-6 p-5 bg-blue-50/90 border border-blue-200 rounded-2xl flex items-start gap-3.5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-2xl">storefront</span>
            </div>
            <div class="text-sm flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs uppercase tracking-wider font-bold text-primary">Retiro en Sucursal</span>
                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Pago Acreditado</span>
                </div>
                <strong class="font-bold text-gray-900 block text-base mb-1">
                    ¡Te avisaremos cuando tu pedido esté listo para pasar a buscar a la sucursal!
                </strong>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Tu compra fue abonada a través de Mercado Pago. El depósito está preparando los artículos. En cuanto el pedido se encuentre empaquetado y listo para entrega, te avisaremos para que puedas acercarte a retirarlo con tu DNI / CUIT.
                </p>
                <div class="pt-2 mt-2 border-t border-blue-200/60 flex flex-wrap gap-4 text-xs text-gray-600">
                    <span class="flex items-center gap-1 font-medium text-gray-700">
                        <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                        Calle Saavedra 4600 (en intersección con Av. Circunvalación), Ciudad de Formosa
                    </span>
                    <span class="flex items-center gap-1 font-medium text-gray-700">
                        <span class="material-symbols-outlined text-sm text-primary">schedule</span>
                        Lun a Vie: 08:00 - 18:00 hs | Sáb: 08:30 - 13:00 hs
                    </span>
                </div>
            </div>
        </div>
    @elseif($pedido->estado === 'PENDIENTE')
        <div class="mb-6 p-5 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 text-2xl flex-shrink-0 mt-0.5">schedule</span>
                <div class="text-sm">
                    <strong class="font-bold block mb-0.5">Pedido Pendiente de Pago</strong>
                    <span>Tu compra está reservada en el sistema. Puedes completar el pago a través de Mercado Pago para procesar la entrega.</span>
                </div>
            </div>
            <a href="{{ route('pedidos.pago', $pedido->id) }}" class="inline-flex items-center gap-2 bg-[#009ee3] hover:bg-[#0082ba] text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-colors shadow-sm shrink-0">
                <span class="material-symbols-outlined text-base">payments</span>
                Pagar con Mercado Pago
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Columna Izquierda: Información y Productos -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Datos del Comprador y Entrega -->
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-5 shadow-sm">
                <h2 class="text-sm font-bold text-on-surface uppercase tracking-wider mb-3 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">person</span>
                    Información de la Compra y Cliente
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-on-surface-variant block">Cliente:</span>
                        <span class="font-semibold text-on-surface">{{ $pedido->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Correo Electrónico:</span>
                        <span class="font-semibold text-on-surface">{{ $pedido->user->email }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Teléfono de Contacto:</span>
                        <span class="font-semibold text-on-surface">{{ $pedido->user->telefono ?? 'No informado' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-on-surface-variant block">Modalidad de Entrega:</span>
                        <span class="font-bold text-green-700">Retiro en local (Gratis)</span>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos Comprados -->
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-5 shadow-sm">
                <h2 class="text-sm font-bold text-on-surface uppercase tracking-wider mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">inventory_2</span>
                    Artículos en el Pedido ({{ $pedido->detalles->sum('cantidad') }})
                </h2>
                <div class="divide-y divide-outline-variant">
                    @foreach($pedido->detalles as $detalle)
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-on-surface truncate">
                                    {{ $detalle->presentacionProducto?->producto?->nombre_producto ?? 'Producto' }}
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    Presentación: <span class="uppercase font-semibold">{{ $detalle->presentacionProducto?->tipo ?? 'Estándar' }}</span>
                                    (x{{ $detalle->presentacionProducto?->cantidad_contenida ?? 1 }}) &bull; Cantidad: <strong class="text-on-surface">{{ $detalle->cantidad }}</strong>
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs text-on-surface-variant block">
                                    ${{ number_format($detalle->precio_unitario, 0, ',', '.') }} c/u
                                </span>
                                <span class="text-base font-bold text-primary">
                                    ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Resumen Financiero y Acciones -->
        <div class="lg:col-span-4 space-y-6">
            @if($pedido->pago)
                <div class="bg-surface-bright border border-[#009ee3]/40 rounded-xl p-5 shadow-sm bg-[#009ee3]/5">
                    <h3 class="text-xs font-bold text-[#009ee3] uppercase tracking-wider mb-3 pb-2 border-b border-[#009ee3]/20 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">account_balance_wallet</span>
                        Comprobante Mercado Pago
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-on-surface-variant">Estado:</span>
                            <span class="inline-flex items-center gap-1 font-bold text-green-800 bg-green-100 px-2 py-0.5 rounded-full text-[11px]">
                                <span class="material-symbols-outlined text-xs">done</span> Acreditado
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Transacción MP:</span>
                            <span class="font-mono font-bold text-on-surface select-all">{{ $pedido->pago->id_transaccion_mp }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Fecha:</span>
                            <span class="font-semibold text-on-surface">{{ \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-1 border-t border-outline-variant/30">
                            <span class="text-on-surface-variant">Abonado:</span>
                            <span class="font-bold text-base text-primary">${{ number_format($pedido->pago->monto, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 mt-1 border-t border-[#009ee3]/20 space-y-2">
                            <a href="{{ route('pedidos.comprobante', $pedido->id) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 bg-[#009ee3] hover:bg-[#007eb5] text-white py-2 px-3 rounded-lg text-xs font-bold transition-colors shadow-xs">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                <span>Ver Comprobante Oficial (PDF)</span>
                            </a>

                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-surface-bright border border-outline-variant rounded-xl p-6 shadow-sm sticky top-24">
                <h2 class="text-base font-bold text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt</span>
                    Resumen Financiero
                </h2>

                <div class="space-y-2 py-2 text-sm text-on-surface-variant">
                    <div class="flex justify-between">
                        <span>Total de artículos:</span>
                        <span class="font-bold text-on-surface">{{ $pedido->detalles->sum('cantidad') }} unidades</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Costo de envío:</span>
                        <span class="font-bold text-green-700">Sin costo (Local)</span>
                    </div>
                </div>

                <div class="border-t border-outline-variant my-4 pt-4 flex justify-between items-baseline">
                    <span class="text-base font-bold text-on-surface">Total Liquidado</span>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-primary">
                            ${{ number_format($pedido->total, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-gray-500 block">IVA incluido</span>
                    </div>
                </div>

                <div class="space-y-3 mt-6">
                    <a href="{{ route('pedidos.index') }}" class="w-full bg-surface-variant hover:bg-surface-variant/80 text-on-surface font-bold py-2.5 px-4 rounded-lg text-sm text-center block transition-colors border border-outline-variant">
                        &larr; Volver al Listado de Pedidos
                    </a>

                    <a href="{{ route('catalogo') }}" class="w-full bg-primary hover:bg-primary-container text-on-primary font-bold py-2.5 px-4 rounded-lg text-sm text-center block transition-colors shadow">
                        Ir al Catálogo de Productos
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="mt-auto border-t border-outline-variant py-4 bg-surface text-center text-xs text-on-surface-variant">
    Plataforma HyK Mayorista &copy; {{ date('Y') }} - Seminario de Integración
</footer>

@auth
    <x-modal-logout />
@endauth
</body>
</html>
