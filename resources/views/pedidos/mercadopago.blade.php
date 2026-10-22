<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pagar con Mercado Pago - HyK Mayorista</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "mp-blue": "#009ee3",
                        "mp-blue-dark": "#007eb5",
                        "mp-green": "#00a650",
                        "mp-green-dark": "#008740",
                        "primary": "#00317e",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f5f5f5] text-gray-800 font-sans min-h-screen flex flex-col antialiased">

<!-- Header Oficial Simulado de Mercado Pago -->
<header class="bg-[#009ee3] text-white shadow-md">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <!-- Logo Mercado Pago -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 font-bold text-xl tracking-tight text-white">
                <svg class="w-8 h-8 fill-current text-white" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
                <span>mercado<strong class="font-black">pago</strong></span>
            </div>
            <span class="hidden md:inline-block text-xs bg-white/20 text-white font-semibold px-2.5 py-1 rounded-full">
                Checkout Sandbox (Modo de Prueba)
            </span>
        </div>

        <!-- Información de Seguridad & Comercio -->
        <div class="flex items-center gap-4 text-xs">
            <div class="hidden sm:flex items-center gap-1.5 text-white/90">
                <span class="material-symbols-outlined text-base">lock</span>
                <span>Sitio seguro &bull; Cifrado 256 bits</span>
            </div>
            <span class="bg-white/20 px-3 py-1 rounded-full font-bold text-white">
                HyK Mayorista
            </span>
        </div>
    </div>
</header>

<!-- Contenedor Principal del Checkout -->
<main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 md:p-8">

    <!-- Banner Modo Ficticio / Sandbox -->
    <div class="mb-6 p-4 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 text-sm shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-600 text-2xl flex-shrink-0 mt-0.5">science</span>
            <div>
                <strong class="font-bold block text-amber-950">Entorno de Pagos Mercado Pago Sandbox</strong>
                <span class="text-xs text-amber-800">
                    Estás operando con los usuarios y credenciales de prueba generados en <span class="font-mono bg-amber-100 px-1 py-0.5 rounded">MercadoPago/Credenciales.txt</span>. No se efectuará ningún cargo real.
                </span>
            </div>
        </div>
        <div class="flex-shrink-0 self-start md:self-auto">
            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider bg-amber-200 text-amber-900 px-2.5 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                Sandbox Activo
            </span>
        </div>
    </div>

    @if(isset($errorMp) && $errorMp)
        <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-xl text-red-900 text-xs shadow-sm flex items-start gap-3">
            <span class="material-symbols-outlined text-red-600 text-xl flex-shrink-0">warning</span>
            <div>
                <strong class="font-bold block">Aviso sobre la API de Mercado Pago:</strong>
                <span>{{ $errorMp }}</span>
                <p class="mt-1 text-gray-600">Puedes usar la opción de simulación local más abajo para continuar con las pruebas.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Columna Izquierda: Resumen del Pedido y Credenciales de Test -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Resumen de Compra -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold block">Vendedor</span>
                        <h2 class="font-bold text-gray-800 text-base">HyK Mayorista</h2>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 block">Pedido</span>
                        <span class="text-sm font-bold text-mp-blue">#{{ $pedido->id }}</span>
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1 divide-y divide-gray-100 text-sm">
                    @foreach($pedido->detalles as $detalle)
                        <div class="pt-2 first:pt-0 flex justify-between items-center text-xs">
                            <div class="min-w-0 flex-1 pr-2">
                                <span class="font-semibold text-gray-800 block truncate">
                                    {{ $detalle->presentacionProducto?->producto?->nombre_producto ?? 'Producto' }}
                                </span>
                                <span class="text-gray-500">
                                    {{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                                    ({{ $detalle->presentacionProducto?->tipo ?? 'unidad' }})
                                </span>
                            </div>
                            <span class="font-bold text-gray-800 whitespace-nowrap">
                                ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- Desglose Final -->
                <div class="border-t border-gray-200 mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600 text-xs">
                        <span>Retiro en sucursal:</span>
                        <span class="text-mp-green font-bold">Gratis</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-dashed border-gray-200">
                        <span class="text-base font-bold text-gray-900">Total a pagar:</span>
                        <span class="text-2xl font-black text-mp-blue">
                            ${{ number_format($pedido->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Datos de las Cuentas de Prueba con Botones de Copiar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-xs text-gray-700">
                <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-1.5 uppercase tracking-wider text-[11px]">
                    <span class="material-symbols-outlined text-mp-blue text-sm">vpn_key</span>
                    Credenciales Test (Sandbox)
                </h3>
                <p class="text-[11px] text-gray-500 mb-3">
                    Copia estos datos para iniciar sesión en el Checkout oficial de Mercado Pago:
                </p>
                <div class="space-y-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-500 text-[11px] font-medium">Comprador Prueba:</span>
                            <button type="button" onclick="copiarTexto('test-buyer-user', this)" class="text-[10px] bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-2 py-0.5 rounded transition-colors flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-xs">content_copy</span>
                                <span>Copiar</span>
                            </button>
                        </div>
                        <div id="test-buyer-user" class="font-mono text-gray-900 font-bold select-all bg-white px-2 py-1 rounded border border-gray-200 truncate">
                            {{ $credenciales['comprador_usuario'] }}
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-500 text-[11px] font-medium">Contraseña Test:</span>
                            <button type="button" onclick="copiarTexto('test-buyer-pass', this)" class="text-[10px] bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-2 py-0.5 rounded transition-colors flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-xs">content_copy</span>
                                <span>Copiar</span>
                            </button>
                        </div>
                        <div id="test-buyer-pass" class="font-mono text-gray-900 font-bold select-all bg-white px-2 py-1 rounded border border-gray-200">
                            {{ $credenciales['comprador_clave'] }}
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-2">
                        <span class="text-gray-500 text-[11px] block mb-1">Vendedor Prueba (Receptor):</span>
                        <div class="font-mono text-gray-600 text-[11px] truncate select-all">
                            {{ $credenciales['vendedor_usuario'] }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Columna Derecha: Redirección al Checkout Oficial de Mercado Pago y Alternativas -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 space-y-6">

                @if(!empty($initPoint))
                    <!-- Sección Checkout Oficial de Mercado Pago (Recomendada) -->
                    <div>
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                            <div class="w-12 h-12 rounded-xl bg-[#009ee3]/10 text-mp-blue flex items-center justify-center shrink-0">
                                <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Checkout Oficial de Mercado Pago</h2>
                                <p class="text-xs text-gray-500">Conectado directamente a la API oficial (Entorno Sandbox).</p>
                            </div>
                        </div>

                        <!-- Indicaciones -->
                        <div class="mt-4 p-4 bg-blue-50/70 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-2">
                            <div class="flex items-center gap-2 font-bold text-blue-950">
                                <span class="material-symbols-outlined text-mp-blue text-base">info</span>
                                <span>Pasos para efectuar el pago:</span>
                            </div>
                            <ol class="list-decimal list-inside space-y-1 text-blue-800 pl-1">
                                <li>Haz clic en el botón azul para abrir la pasarela oficial de Mercado Pago.</li>
                                <li>Inicia sesión con el <strong>Comprador Prueba</strong> (datos copiables a la izquierda).</li>
                                <li>Confirma el pago en Mercado Pago; al completarlo serás devuelto automáticamente aquí.</li>
                            </ol>
                        </div>

                        <!-- Botón Principal hacia Mercado Pago -->
                        <div class="mt-6 space-y-3">
                            <a href="{{ $initPoint }}" id="btn-mercadopago-oficial" class="w-full bg-[#009ee3] hover:bg-mp-blue-dark active:scale-[0.99] text-white font-bold py-4 px-6 rounded-xl text-base transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3 cursor-pointer">
                                <svg class="w-6 h-6 fill-current text-white shrink-0" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                                <span>Pagar ${{ number_format($pedido->total, 0, ',', '.') }} en Mercado Pago Oficial</span>
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>

                            <!-- Barra con contador de redirección automática -->
                            <div id="countdown-container" class="flex items-center justify-between bg-gray-50 border border-gray-200 px-4 py-2.5 rounded-lg text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-mp-blue animate-spin text-sm">sync</span>
                                    <span>Redirigiendo automáticamente en <strong id="countdown-seconds" class="text-mp-blue font-bold">5</strong>s...</span>
                                </div>
                                <button type="button" onclick="cancelarAutoRedirect()" id="btn-cancel-redirect" class="text-xs font-semibold text-gray-500 hover:text-gray-800 underline cursor-pointer">
                                    Pausar redirección
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Acordeón / Opción secundaria: Simulación Local Offline -->
                    <div class="pt-6 border-t border-gray-200">
                        <button type="button" onclick="toggleSimulacionLocal()" class="text-xs text-gray-500 hover:text-mp-blue flex items-center justify-between w-full font-medium cursor-pointer">
                            <span>¿Deseas simular la acreditación instantánea localmente sin salir?</span>
                            <span class="material-symbols-outlined text-base transition-transform" id="icon-sim-toggle">expand_more</span>
                        </button>

                        <div id="panel-simulacion-local" class="hidden mt-4 pt-4 border-t border-dashed border-gray-200">
                            <p class="text-xs text-gray-500 mb-3">
                                Simula el resultado aprobado de forma inmediata sin conexión con Mercado Pago:
                            </p>
                            <form action="{{ route('pedidos.procesar-pago', $pedido->id) }}" method="POST" id="form-pago-simulado">
                                @csrf
                                <input type="hidden" name="id_transaccion_mp" value="MP-{{ mt_rand(1000000000, 9999999999) }}"/>
                                <input type="hidden" name="metodo_pago" value="cuenta_mp"/>
                                <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-4 rounded-lg text-xs transition-colors flex items-center justify-center gap-2 border border-gray-300 cursor-pointer">
                                    <span class="material-symbols-outlined text-base text-green-600">check_circle</span>
                                    <span>Simular Acreditación Inmediata (Local)</span>
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <!-- Fallback cuando no hay initPoint disponible (API offline o sin token) -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-mp-blue">payments</span>
                            ¿Cómo quieres pagar?
                        </h2>

                        <form id="form-pago-mp" action="{{ route('pedidos.procesar-pago', $pedido->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_transaccion_mp" id="id_transaccion_mp" value="MP-{{ mt_rand(1000000000, 9999999999) }}"/>

                            <div class="space-y-3 mb-6">
                                <label class="block cursor-pointer">
                                    <input type="radio" name="metodo_pago" value="cuenta_mp" checked class="peer sr-only"/>
                                    <div class="border-2 border-gray-200 peer-checked:border-mp-blue peer-checked:bg-blue-50/40 rounded-xl p-4 transition-all">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 text-mp-blue flex items-center justify-center font-bold">
                                                    <span class="material-symbols-outlined">account_balance_wallet</span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-900 text-sm">Dinero disponible en Mercado Pago</span>
                                                        <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Simulado</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        Usuario: <strong class="text-gray-700">{{ $credenciales['comprador_usuario'] }}</strong>
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-bold text-mp-green">$ 500.000,00</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" id="btn-submit-pago" class="w-full bg-[#009ee3] hover:bg-mp-blue-dark text-white font-bold py-4 px-6 rounded-xl text-base transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined">lock</span>
                                <span>Pagar ${{ number_format($pedido->total, 0, ',', '.') }}</span>
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Cancelar y volver -->
                <div class="pt-2 text-center">
                    <a href="{{ route('pedidos.checkout') }}" class="text-xs text-gray-500 hover:text-mp-blue font-semibold transition-colors">
                        Cancelar y volver a HyK Mayorista
                    </a>
                </div>

            </div>
        </div>

    </div>
</main>

<footer class="mt-auto py-6 border-t border-gray-200 bg-white text-center text-xs text-gray-500">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <span>Pasarela de Pagos &bull; Mercado Pago Sandbox</span>
        <span class="text-gray-400">HyK Mayorista &copy; {{ date('Y') }}</span>
    </div>
</footer>

<script>
    let countdown = 5;
    let timer = null;

    function updateCountdown() {
        const secSpan = document.getElementById('countdown-seconds');
        if (!secSpan) return;
        secSpan.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(timer);
            const link = document.getElementById('btn-mercadopago-oficial');
            if (link && link.href) {
                window.location.href = link.href;
            }
        } else {
            countdown--;
        }
    }

    if (document.getElementById('countdown-seconds')) {
        timer = setInterval(updateCountdown, 1000);
    }

    function cancelarAutoRedirect() {
        if (timer) clearInterval(timer);
        const container = document.getElementById('countdown-container');
        if (container) {
            container.innerHTML = '<span class="text-xs text-amber-700 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-sm">pause_circle</span> Redirección automática pausada. Haz clic en el botón azul para ir a Mercado Pago.</span>';
        }
    }

    function copiarTexto(elementId, btn) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-xs text-green-600">check</span><span class="text-green-700 font-bold">¡Copiado!</span>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
            }, 2000);
        });
    }

    function toggleSimulacionLocal() {
        const panel = document.getElementById('panel-simulacion-local');
        const icon = document.getElementById('icon-sim-toggle');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>

</body>
</html>