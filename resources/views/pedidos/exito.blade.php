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
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-600 hover:underline">Salir</button>
            </form>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-3xl w-full mx-auto p-4 md:p-8 my-auto">
    <div class="bg-surface-bright border border-outline-variant rounded-2xl p-6 md:p-10 shadow-sm text-center">
        <!-- Ícono de Éxito -->
        <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-on-surface mb-2">¡Pedido Confirmado con Éxito!</h1>
        <p class="text-sm text-on-surface-variant max-w-lg mx-auto mb-6">
            Tu pedido ha sido registrado en nuestro sistema bajo el número <strong class="text-primary font-bold">#{{ $pedido->id }}</strong>. El stock ha sido reservado correctamente.
        </p>

        <!-- Información del Pedido -->
        <div class="bg-surface-variant/40 rounded-xl p-5 mb-6 text-left text-sm space-y-3">
            <div class="flex justify-between border-b border-outline-variant/60 pb-2">
                <span class="text-on-surface-variant">Número de Pedido:</span>
                <span class="font-bold text-primary">#{{ $pedido->id }}</span>
            </div>
            <div class="flex justify-between border-b border-outline-variant/60 pb-2">
                <span class="text-on-surface-variant">Estado del Pedido:</span>
                <span class="inline-flex items-center text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                    {{ $pedido->estado }}
                </span>
            </div>
            <div class="flex justify-between border-b border-outline-variant/60 pb-2">
                <span class="text-on-surface-variant">Cliente:</span>
                <span class="font-semibold text-on-surface">{{ $pedido->user->name }}</span>
            </div>
            <div class="flex justify-between items-baseline pt-1">
                <span class="font-bold text-on-surface">Total Liquidado:</span>
                <span class="text-xl font-bold text-primary">${{ number_format($pedido->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
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

</body>
</html>
