<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pedido Confirmado - HyK Mayorista</title>
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
        @auth
            <span class="hidden md:inline text-sm font-bold text-on-surface-variant">
                Hola, {{ auth()->user()->name }}
            </span>
            <button type="button" onclick="openLogoutModal()" class="text-xs font-bold text-red-600 hover:underline cursor-pointer">Salir</button>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-3xl w-full mx-auto p-4 md:p-8 my-auto">
    <div class="bg-surface-bright border border-outline-variant rounded-2xl p-6 md:p-10 shadow-sm text-center">
        <!-- Ícono de Éxito -->
        <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-on-surface mb-2">¡Pedido Confirmado y Pagado con Éxito!</h1>
        <p class="text-sm text-on-surface-variant max-w-lg mx-auto mb-6">
            Tu compra ha sido abonada correctamente a través de <strong class="text-[#009ee3] font-bold">Mercado Pago</strong> y registrada bajo el número <strong class="text-primary font-bold">#{{ $pedido->id }}</strong>.
        </p>

        <!-- AVISO DESTACADO DE RETIRO EN SUCURSAL -->
        <div class="mb-6 p-5 bg-blue-50/90 border border-blue-200 rounded-2xl text-left shadow-sm">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">storefront</span>
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs uppercase tracking-wider font-bold text-primary">Modalidad: Retiro en Sucursal</span>
                        <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Sin Costo de Envío</span>
                    </div>
                    <p class="text-base font-bold text-gray-900">
                        ¡Te avisaremos cuando tu pedido esté listo para pasar a buscar a la sucursal!
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Nuestro depósito ha comenzado la preparación y control de stock de tus productos. En cuanto el pedido esté empaquetado y listo para retirar, recibirás una notificación y podrás pasar a retirarlo presentando tu DNI / CUIT y el número de pedido.
                    </p>
                    <div class="pt-2 mt-2 border-t border-blue-200/60 flex flex-wrap gap-x-6 gap-y-2 text-xs text-gray-700 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            <span>Sucursal Central: Calle Saavedra 4600 (en intersección con Av. Circunvalación), Ciudad de Formosa</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-primary">schedule</span>
                            <span>Lun a Vie: 08:00 - 18:00 hs | Sáb: 08:30 - 13:00 hs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Pago y Pedido -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-left">
            <!-- Bloque Pedido -->
            <div class="bg-surface-variant/40 rounded-xl p-5 text-sm space-y-2.5">
                <h3 class="font-bold text-xs uppercase tracking-wider text-on-surface-variant flex items-center gap-1.5 border-b border-outline-variant/60 pb-2">
                    <span class="material-symbols-outlined text-sm text-primary">receipt</span>
                    Datos de la Orden
                </h3>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant text-xs">Número de Pedido:</span>
                    <span class="font-bold text-primary">#{{ $pedido->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant text-xs">Estado del Pedido:</span>
                    <span class="inline-flex items-center text-xs font-bold text-green-800 bg-green-100 px-2 py-0.5 rounded">
                        {{ $pedido->estado }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant text-xs">Cliente:</span>
                    <span class="font-semibold text-on-surface">{{ $pedido->user->name }}</span>
                </div>
                <div class="flex justify-between items-baseline pt-1 border-t border-outline-variant/40">
                    <span class="font-bold text-on-surface text-xs">Total Liquidado:</span>
                    <span class="text-base font-bold text-primary">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Bloque Mercado Pago -->
            <div class="bg-[#009ee3]/5 border border-[#009ee3]/30 rounded-xl p-5 text-sm space-y-2.5">
                <h3 class="font-bold text-xs uppercase tracking-wider text-[#009ee3] flex items-center gap-1.5 border-b border-[#009ee3]/20 pb-2">
                    <span class="material-symbols-outlined text-sm text-[#009ee3]">account_balance_wallet</span>
                    Comprobante de Mercado Pago
                </h3>
                <div class="flex justify-between">
                    <span class="text-gray-600 text-xs">Medio de Pago:</span>
                    <span class="font-bold text-[#009ee3]">Mercado Pago</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 text-xs">Estado del Pago:</span>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded">
                        <span class="material-symbols-outlined text-xs">done</span> Aprobado
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 text-xs">N° de Transacción MP:</span>
                    <span class="font-mono text-xs font-bold text-gray-800 select-all">
                        {{ $pedido->pago?->id_transaccion_mp ?? ('MP-' . $pedido->id . '-TEST') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 text-xs">Fecha de Acreditación:</span>
                    <span class="font-medium text-gray-800 text-xs">
                        {{ $pedido->pago?->fecha_pago ? \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y') : now()->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Resumen de Artículos Comprados -->
        @if($pedido->detalles && $pedido->detalles->count() > 0)
            <div class="bg-surface-bright border border-outline-variant rounded-xl p-4 mb-6 text-left">
                <h4 class="text-xs uppercase tracking-wider font-bold text-on-surface-variant mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-primary">inventory_2</span>
                    Artículos en este Pedido ({{ $pedido->detalles->sum('cantidad') }})
                </h4>
                <div class="divide-y divide-outline-variant/60 text-xs">
                    @foreach($pedido->detalles as $detalle)
                        <div class="py-2 flex justify-between items-center">
                            <div>
                                <span class="font-bold text-on-surface">
                                    {{ $detalle->presentacionProducto?->producto?->nombre_producto ?? 'Producto' }}
                                </span>
                                <span class="text-on-surface-variant block">
                                    {{ $detalle->presentacionProducto?->tipo }} &bull; Cantidad: {{ $detalle->cantidad }}
                                </span>
                            </div>
                            <span class="font-bold text-primary">
                                ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('pedidos.comprobante', $pedido->id) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#009ee3] hover:bg-[#007eb5] text-white px-6 py-3 rounded-lg text-sm font-bold transition-all shadow hover:shadow-md cursor-pointer">
                <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                Ver Comprobante (PDF)
            </a>
            @if(isset($pedido->pago) && is_numeric($pedido->pago->id_transaccion_mp))
                <a href="https://www.mercadopago.com.ar/activities/payment/{{ $pedido->pago->id_transaccion_mp }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-[#009ee3] border border-[#009ee3] px-6 py-3 rounded-lg text-sm font-bold transition-colors shadow">
                    <span class="material-symbols-outlined text-lg">open_in_new</span>
                    Ver recibo en Mercado Pago
                </a>
            @endif
            <a href="{{ route('pedidos.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-secondary text-on-secondary px-6 py-3 rounded-lg text-sm font-bold hover:bg-[#00531a] transition-colors shadow">
                <span class="material-symbols-outlined text-lg">receipt_long</span>
                Ver en Mis Pedidos
            </a>
            <a href="{{ route('catalogo') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-surface-variant text-on-surface hover:bg-surface-variant/80 border border-outline-variant px-6 py-3 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-lg">storefront</span>
                Seguir Comprando
            </a>
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
