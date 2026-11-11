<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mis Notificaciones - HyK Mayorista</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
    <link href="https://fonts.cdnfonts.com/css/headingnow-trial" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet"/>

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
                        "brand-red": "#E31E24",
                        "brand-redHover": "#C6161B",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        display: ["Oswald", "sans-serif"],
                    }
                }
            }
        }
    </script>
    <style>
      @font-face {
        font-family: 'Heading Now';
        src: local('Heading Now 64 Regular'),
             local('Heading Now Regular'),
             local('HeadingNow Trial 64 Regular'),
             local('Heading Now 71-78'),
             url('https://fonts.cdnfonts.com/s/103092/HeadingNowTrial64Regular.woff') format('woff');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
      }
      @font-face {
        font-family: 'Heading Now';
        src: local('Heading Now 66 Bold'),
         local('Heading Now 76 Bold'),
         local('Heading Now Bold'),
         local('HeadingNow Trial 66 Bold'),
         url('https://fonts.cdnfonts.com/s/103092/HeadingNowTrial66Bold.woff') format('woff');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
      }
      .font-heading-now {
        font-family: 'Heading Now', 'Heading Now 71-78', 'HeadingNow Trial', 'Heading Now Trial', sans-serif !important;
      }
    </style>
</head>
<body class="bg-background text-on-surface font-sans min-h-screen flex flex-col antialiased">

<!-- TopNavBar -->
<header class="h-16 bg-surface-bright border-b border-outline-variant flex items-center justify-between px-4 md:px-8 sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <a href="{{ route('catalogo') }}" class="flex items-center shrink-0 transition-transform hover:scale-[1.01] py-1" title="HyK Mayorista">
            <img src="{{ asset('images/logo-hyk-1.png') }}" alt="HyK Mayorista" class="h-9 sm:h-10 md:h-11 lg:h-12 w-auto max-w-[190px] sm:max-w-[260px] md:max-w-[340px] lg:max-w-[390px] object-contain select-none mix-blend-multiply">
        </a>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('catalogo') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-slate-700 hover:text-brand-red transition-colors">
            <span class="material-symbols-outlined text-base text-slate-500">storefront</span>
            <span class="font-heading-now font-semibold">Catálogo</span>
        </a>

        <a href="{{ route('pedidos.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-slate-700 hover:text-brand-red transition-colors" title="Ver mis pedidos">
            <span class="material-symbols-outlined text-base text-slate-500">receipt_long</span>
            <span class="font-heading-now font-semibold">Mis Pedidos</span>
        </a>

        <a href="{{ route('perfil.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-slate-700 hover:text-brand-red transition-colors" title="Ver mi perfil">
            <span class="material-symbols-outlined text-base text-slate-500">person</span>
            <span class="font-heading-now font-semibold">Mi Perfil</span>
        </a>

        @php
            $conteoCarrito = app(\App\Services\CartService::class)->conteoTotal();
        @endphp
        <a href="{{ route('carrito.index') }}" class="relative p-2 text-slate-700 hover:text-brand-red transition-colors flex items-center" title="Mi Carrito">
            <span class="material-symbols-outlined text-xl">shopping_cart</span>
            @if($conteoCarrito > 0)
                <span class="absolute top-0 right-0 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-brand-red text-white text-[10px] font-bold">
                    {{ $conteoCarrito }}
                </span>
            @endif
        </a>

        @auth
            <div class="h-5 w-px bg-outline-variant hidden sm:block"></div>
            <span class="hidden md:inline text-sm font-bold text-slate-800 font-heading-now">
                Hola, {{ auth()->user()->name }}
            </span>
            <button type="button" onclick="openLogoutModal()" class="text-sm font-bold text-red-600 hover:underline cursor-pointer font-heading-now">Salir</button>
        @else
            <a href="{{ route('login') }}" class="text-sm font-bold text-[#00317e] hover:text-[#002255] hover:underline font-heading-now" style="color: #00317e;">
                Ingresar / Registrarse
            </a>
        @endauth
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-5xl w-full mx-auto p-4 md:p-8">

    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-2 font-heading-now">
            <a href="{{ route('catalogo') }}" class="hover:underline">Catálogo</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-primary">Notificaciones</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display uppercase tracking-wide text-2xl md:text-3xl font-bold text-on-surface flex items-center gap-2.5 leading-tight">
                    <span class="material-symbols-outlined text-3xl text-primary">notifications_active</span>
                    Mis Notificaciones y Avisos
                </h1>
                <p class="text-sm text-on-surface-variant mt-1 font-heading-now font-normal">
                    Avisos en tiempo real sobre la preparación, retiro en sucursal y entrega de tus pedidos.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(isset($notificacionesNoLeidas) && $notificacionesNoLeidas > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-red text-white flex items-center gap-1.5 shadow-sm font-heading-now">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        {{ $notificacionesNoLeidas }} pendiente{{ $notificacionesNoLeidas > 1 ? 's' : '' }}
                    </span>

                    <form action="{{ route('notificaciones.marcar-todas') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-outline-variant rounded-xl text-xs font-bold text-primary hover:bg-primary/5 transition-colors shadow-sm cursor-pointer font-heading-now">
                            <span class="material-symbols-outlined text-sm">done_all</span>
                            Marcar todas leídas
                        </button>
                    </form>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 flex items-center gap-1 font-heading-now">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Todas leídas
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-xl flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-xl flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Contenido Principal de Notificaciones -->
    <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6 md:p-8">
        @if(!isset($notificaciones) || $notificaciones->isEmpty())
            <div class="py-16 text-center max-w-md mx-auto">
                <div class="w-20 h-20 bg-surface-variant/70 text-on-surface-variant rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-4xl">notifications_paused</span>
                </div>
                <h2 class="font-display uppercase tracking-wide text-lg font-bold text-on-surface mb-2">No tienes notificaciones pendientes</h2>
                <p class="text-sm text-on-surface-variant mb-6 leading-relaxed">
                    Cuando el administrador actualice el estado de tus compras (por ejemplo, cuando esté <strong>en preparación</strong> o <strong>listo para retirar en el local</strong>), verás las notificaciones reflejadas aquí.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-xl text-xs font-bold hover:bg-primary-container transition-colors shadow">
                        <span class="material-symbols-outlined text-base">receipt_long</span>
                        Ver Mis Pedidos
                    </a>
                    <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-outline-variant rounded-xl text-xs font-bold text-slate-700 hover:bg-surface-variant/40 transition-colors">
                        <span class="material-symbols-outlined text-base">storefront</span>
                        Ir al Catálogo
                    </a>
                </div>
            </div>
        @else
            <div class="divide-y divide-outline-variant/60">
                @foreach($notificaciones as $notificacion)
                    @php
                        $data = $notificacion->data;
                        $isUnread = is_null($notificacion->read_at);
                        $estadoNuevo = strtoupper($data['estado_nuevo'] ?? '');

                        $styleConfig = match($estadoNuevo) {
                            'EN_PREPARACION' => [
                                'badge' => 'En Preparación',
                                'badge_class' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'icon' => $data['icono'] ?? 'inventory_2',
                                'icon_box' => 'bg-amber-500/10 text-amber-600 border-amber-200',
                            ],
                            'LISTO_RETIRO' => [
                                'badge' => 'Listo para Retirar',
                                'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'icon' => $data['icono'] ?? 'storefront',
                                'icon_box' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200',
                            ],
                            'ENTREGADO' => [
                                'badge' => 'Entregado / Completado',
                                'badge_class' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'icon' => $data['icono'] ?? 'task_alt',
                                'icon_box' => 'bg-blue-500/10 text-blue-600 border-blue-200',
                            ],
                            'CANCELADO' => [
                                'badge' => 'Cancelado',
                                'badge_class' => 'bg-red-100 text-red-800 border-red-200',
                                'icon' => $data['icono'] ?? 'cancel',
                                'icon_box' => 'bg-red-500/10 text-red-600 border-red-200',
                            ],
                            'PAGADO' => [
                                'badge' => 'Pago Acreditado',
                                'badge_class' => 'bg-green-100 text-green-800 border-green-200',
                                'icon' => $data['icono'] ?? 'paid',
                                'icon_box' => 'bg-green-500/10 text-green-600 border-green-200',
                            ],
                            default => [
                                'badge' => $data['estado_nuevo'] ?? 'Aviso',
                                'badge_class' => 'bg-slate-100 text-slate-800 border-slate-200',
                                'icon' => 'notifications',
                                'icon_box' => 'bg-slate-500/10 text-slate-600 border-slate-200',
                            ],
                        };
                    @endphp

                    <article class="py-5 first:pt-2 last:pb-2 flex flex-col md:flex-row md:items-start justify-between gap-4 {{ $isUnread ? 'bg-primary/[0.03] -mx-4 md:-mx-6 px-4 md:px-6 rounded-xl' : '' }} transition-colors">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Ícono Representativo -->
                            <div class="w-12 h-12 rounded-xl border flex items-center justify-center shrink-0 {{ $styleConfig['icon_box'] }} shadow-xs">
                                <span class="material-symbols-outlined text-2xl">{{ $styleConfig['icon'] }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border uppercase tracking-wide {{ $styleConfig['badge_class'] }}">
                                        {{ $styleConfig['badge'] }}
                                    </span>

                                    <h3 class="text-sm md:text-base font-bold text-on-surface">
                                        {{ $data['titulo'] ?? 'Aviso sobre pedido' }}
                                    </h3>

                                    @if($isUnread)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-red text-white uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            Nueva
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed mb-3">
                                    {{ $data['mensaje'] ?? '' }}
                                </p>

                                <div class="flex flex-wrap items-center gap-3 text-xs text-on-surface-variant/80">
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        {{ $notificacion->created_at->diffForHumans() }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>{{ $notificacion->created_at->format('d/m/Y H:i') }}</span>

                                    @if(isset($data['pedido_id']))
                                        <span>&bull;</span>
                                        <a href="{{ route('pedidos.show', $data['pedido_id']) }}" class="font-bold text-primary hover:underline inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">receipt_long</span>
                                            <span>Ver Pedido #{{ $data['pedido_id'] }}</span>
                                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Acciones Rápidas del Ítem -->
                        <div class="flex md:flex-col items-center md:items-end gap-2 shrink-0 self-end md:self-start pt-2 md:pt-0">
                            @if($isUnread)
                                <form action="{{ route('notificaciones.leer', $notificacion->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-white border border-outline-variant hover:border-primary text-xs font-semibold text-primary rounded-lg hover:bg-primary/5 transition-all inline-flex items-center gap-1 shadow-xs cursor-pointer" title="Marcar como leída">
                                        <span class="material-symbols-outlined text-sm">mark_email_read</span>
                                        <span>Marcar leída</span>
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('notificaciones.destroy', $notificacion->id) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta notificación de tu historial?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-on-surface-variant hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors inline-flex items-center gap-0.5 cursor-pointer" title="Eliminar notificación">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($notificaciones->hasPages())
                <div class="mt-6 pt-4 border-t border-outline-variant/60">
                    {{ $notificaciones->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Navegación Inferior -->
    <div class="flex flex-wrap items-center justify-between gap-4 pt-6 mt-6 border-t border-outline-variant/60">
        <a href="{{ route('perfil.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            <span class="font-heading-now font-semibold">Mi Perfil</span>
        </a>

        <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="font-heading-now font-semibold">Mis Pedidos</span>
            <span class="material-symbols-outlined text-lg">arrow_forward</span>
        </a>
    </div>
</main>

<footer class="mt-auto border-t border-outline-variant py-4 bg-surface text-center text-xs text-on-surface-variant">
    Plataforma HyK Mayorista &copy; {{ date('Y') }} - Seminario de Integración
</footer>

@auth
    <x-modal-logout />
@endauth
<x-chatbot-widget />
</body>
</html>
