<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mi Perfil - HyK Mayorista</title>
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
                        "primary": "#00317e",
                        "primary-container": "#0046ad",
                        "on-primary": "#ffffff",
                        "secondary": "#006e25",
                        "secondary-container": "#80f98b",
                        "on-secondary-container": "#007327",
                        "on-secondary": "#ffffff",
                        "background": "#f8f9fa",
                        "surface": "#f8f9fa",
                        "surface-bright": "#ffffff",
                        "surface-variant": "#e1e3e4",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#434653",
                        "outline-variant": "#c3c6d5",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
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
<header class="h-16 bg-surface-bright border-b border-outline-variant flex items-center justify-between px-4 md:px-8 sticky top-0 z-40">
    <div class="flex items-center gap-3">
        <a href="{{ route('catalogo') }}" class="flex items-center gap-2 text-primary font-bold text-xl tracking-tight">
            <span class="material-symbols-outlined text-2xl">storefront</span>
            HyK Mayorista
        </a>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">storefront</span>
            <span class="hidden sm:inline">Catálogo</span>
        </a>

        <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">receipt_long</span>
            <span class="hidden sm:inline">Mis Pedidos</span>
        </a>

        @php
            $conteoCarrito = app(\App\Services\CartService::class)->conteoTotal();
        @endphp
        <a href="{{ route('carrito.index') }}" class="relative p-2 text-primary hover:bg-surface-variant/40 rounded-full transition-colors flex items-center" title="Mi Carrito">
            <span class="material-symbols-outlined text-xl">shopping_cart</span>
            @if($conteoCarrito > 0)
                <span class="absolute top-0 right-0 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-error text-white text-[10px] font-bold leading-none">
                    {{ $conteoCarrito }}
                </span>
            @endif
        </a>

        <div class="h-5 w-px bg-outline-variant hidden sm:block"></div>

        <button type="button" onclick="openLogoutModal()" class="text-sm font-bold text-red-600 hover:underline cursor-pointer">
            Salir
        </button>
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-5xl w-full mx-auto px-4 py-8 pb-20">

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
            <div class="text-sm font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6 md:p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-primary text-white flex items-center justify-center text-2xl font-bold shadow-md">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-on-surface">{{ $user->name }}</h1>
                        @if($user->tipo_cliente === 'mayorista')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                                Cliente Mayorista
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                Cliente Minorista
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-on-surface-variant flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">mail</span>
                        {{ $user->email }}
                    </p>
                    <p class="text-xs text-on-surface-variant/80 mt-1">
                        Cliente desde {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'el registro' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @if($isVerified)
                    <a href="{{ route('perfil.edit') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-secondary text-white font-bold rounded-xl text-sm shadow hover:bg-[#00531a] transition-all">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Modificar Datos y Contraseña
                    </a>
                @else
                    <button type="button" onclick="openSecurityModal()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl text-sm shadow-md hover:bg-[#002560] transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-lg">lock</span>
                        Modificar Datos y Contraseña
                    </button>
                @endif
            </div>
        </div>

        @if($isVerified)
            <div class="mt-6 pt-4 border-t border-outline-variant/60 flex items-center gap-2 text-xs font-semibold text-secondary">
                <span class="material-symbols-outlined text-base">verified_user</span>
                <span>Sesión de seguridad verificada. Tienes autorización para modificar tus datos y contraseña.</span>
            </div>
        @else
            <div class="mt-6 pt-4 border-t border-outline-variant/60 flex items-center gap-2 text-xs text-on-surface-variant">
                <span class="material-symbols-outlined text-base text-primary">shield</span>
                <span>Por tu seguridad, antes de editar tus datos o clave se requerirá una confirmación de identidad (contraseña o código 2FA).</span>
            </div>
        @endif
    </div>

    <!-- Details Sections Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- Card 1: Datos Personales -->
        <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-outline-variant/60">
                <div class="flex items-center gap-2 font-bold text-on-surface text-base">
                    <span class="material-symbols-outlined text-primary">person</span>
                    Información Personal
                </div>
            </div>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs text-on-surface-variant font-medium">Nombre y Apellido</dt>
                    <dd class="text-base font-semibold text-on-surface mt-0.5">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-on-surface-variant font-medium">Teléfono de Contacto</dt>
                    <dd class="text-base font-semibold text-on-surface mt-0.5 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-on-surface-variant">phone</span>
                        {{ $user->telefono ?? 'No registrado' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-on-surface-variant font-medium">Correo Electrónico</dt>
                    <dd class="text-base font-semibold text-on-surface mt-0.5 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-on-surface-variant">mail</span>
                        {{ $user->email }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Card 2: Datos Comerciales / Identificación -->
        <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-outline-variant/60">
                <div class="flex items-center gap-2 font-bold text-on-surface text-base">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    Identificación Fiscal
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-surface-variant text-on-surface-variant">
                    {{ ucfirst($user->tipo_cliente) }}
                </span>
            </div>

            <dl class="space-y-4 text-sm">
                @if($user->tipo_cliente === 'mayorista')
                    <div>
                        <dt class="text-xs text-on-surface-variant font-medium">Razón Social</dt>
                        <dd class="text-base font-semibold text-on-surface mt-0.5">{{ $user->razon_social ?? 'No registrada' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-on-surface-variant font-medium">CUIT Comercial</dt>
                        <dd class="text-base font-semibold text-on-surface mt-0.5 font-mono">{{ $user->cuit ?? 'No registrado' }}</dd>
                    </div>
                @else
                    <div>
                        <dt class="text-xs text-on-surface-variant font-medium">Documento Nacional de Identidad (DNI)</dt>
                        <dd class="text-base font-semibold text-on-surface mt-0.5 font-mono">{{ $user->dni ?? 'No registrado' }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs text-on-surface-variant font-medium">Tipo de Cuenta</dt>
                    <dd class="text-sm font-semibold text-on-surface mt-0.5">
                        {{ $user->tipo_cliente === 'mayorista' ? 'Mayorista (Precios y compras por bulto/pack)' : 'Minorista (Compras por unidad)' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Card 3: Seguridad de la Cuenta -->
        <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6 md:col-span-2">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-outline-variant/60">
                <div class="flex items-center gap-2 font-bold text-on-surface text-base">
                    <span class="material-symbols-outlined text-primary">lock_clock</span>
                    Seguridad y Acceso
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                <div>
                    <div class="text-xs text-on-surface-variant font-medium">Contraseña</div>
                    <div class="text-base font-semibold text-on-surface mt-1 tracking-widest">••••••••••••</div>
                    <p class="text-xs text-on-surface-variant mt-1">Cifrada con algoritmo seguro bcrypt.</p>
                </div>
                <div>
                    <div class="text-xs text-on-surface-variant font-medium">Método de Verificación</div>
                    <div class="text-sm font-semibold text-on-surface mt-1">Contraseña o Código 2FA por Email</div>
                    <p class="text-xs text-on-surface-variant mt-1">Código de 6 dígitos enviado a {{ $maskedEmail }}</p>
                </div>
                <div class="flex items-center sm:justify-end">
                    <button type="button" onclick="openSecurityModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-outline-variant rounded-xl text-xs font-bold text-primary hover:bg-surface-variant/40 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-base">password</span>
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Acciones Rápidas -->
    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-outline-variant/60">
        <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al Catálogo
        </a>
        <a href="{{ route('pedidos.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">receipt_long</span>
            Ver mis Pedidos Realizados &rarr;
        </a>
    </div>

</main>

<!-- Modal de Verificación de Seguridad -->
<div id="security-modal-backdrop" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity duration-300 opacity-0 backdrop-blur-xs flex items-center justify-center p-4">
    <div id="security-modal-card" class="bg-surface-bright rounded-2xl border border-outline-variant shadow-2xl max-w-lg w-full overflow-hidden transform scale-95 transition-transform duration-300 ease-out">
        
        <!-- Header Modal -->
        <div class="bg-surface p-5 border-b border-outline-variant flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">shield</span>
                </div>
                <div>
                    <h3 class="font-bold text-base text-on-surface">Verificación de Seguridad</h3>
                    <p class="text-xs text-on-surface-variant">Confirma tu identidad para modificar tu perfil</p>
                </div>
            </div>
            <button type="button" onclick="closeSecurityModal()" class="p-1.5 text-on-surface-variant hover:bg-surface-variant/60 rounded-full transition-colors cursor-pointer" aria-label="Cerrar">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <!-- Tabs de Métodos de Seguridad -->
        <div class="flex border-b border-outline-variant bg-surface-bright text-sm font-semibold">
            <button type="button" id="tab-btn-password" onclick="switchSecurityTab('password')" class="flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-primary text-primary bg-primary/5 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-base">key</span>
                <span>Contraseña Actual</span>
            </button>
            <button type="button" id="tab-btn-code" onclick="switchSecurityTab('code')" class="flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-transparent text-on-surface-variant hover:text-on-surface transition-all cursor-pointer">
                <span class="material-symbols-outlined text-base">mail_lock</span>
                <span>Código al Correo</span>
            </button>
        </div>

        <!-- Contenido Modal -->
        <div class="p-6">
            
            <!-- Tab 1: Contraseña Actual -->
            <div id="tab-content-password" class="space-y-4">
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Ingresa tu contraseña actual para autorizar la edición de tu cuenta y el cambio de contraseña.
                </p>

                <form action="{{ route('perfil.verify.password') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="password_actual" class="block text-xs font-bold text-on-surface-variant mb-1">
                            Contraseña actual:
                        </label>
                        <div class="relative">
                            <input type="password" id="password_actual" name="password_actual" required autofocus
                                class="w-full pl-3 pr-10 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                placeholder="••••••••"/>
                            <button type="button" onclick="togglePasswordVisibility('password_actual', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                        @error('password_actual')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="closeSecurityModal()" class="px-4 py-2 border border-outline-variant rounded-xl text-xs font-bold text-on-surface-variant hover:bg-surface-variant/40 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-bold shadow hover:bg-[#002560] transition-colors flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-base">verified</span>
                            Verificar y Modificar
                        </button>
                    </div>
                </form>

                <div class="pt-2 text-center">
                    <button type="button" onclick="switchSecurityTab('code')" class="text-xs text-primary hover:underline font-semibold cursor-pointer">
                        ¿No recuerdas tu contraseña? Verifícate con código a tu correo &rarr;
                    </button>
                </div>
            </div>

            <!-- Tab 2: Código por Correo (2FA) -->
            <div id="tab-content-code" class="hidden space-y-4">
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Te enviaremos un código de seguridad de 6 dígitos a tu correo registrado (<strong>{{ $maskedEmail }}</strong>).
                </p>

                <!-- Si el código aún no se ha enviado -->
                <div id="step-send-code" class="{{ session('code_sent') ? 'hidden' : '' }} space-y-4">
                    <form action="{{ route('perfil.send.code') }}" method="POST">
                        @csrf
                        <div class="p-4 bg-primary/5 rounded-xl border border-primary/10 flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">mark_email_read</span>
                            <div class="text-xs text-on-surface-variant">
                                Al hacer clic en <strong>"Enviar Código"</strong>, generaremos una clave temporal de 6 dígitos válida durante 15 minutos.
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" onclick="closeSecurityModal()" class="px-4 py-2 border border-outline-variant rounded-xl text-xs font-bold text-on-surface-variant hover:bg-surface-variant/40 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-bold shadow hover:bg-[#002560] transition-colors flex items-center gap-1.5 cursor-pointer">
                                <span class="material-symbols-outlined text-base">send</span>
                                Enviar Código de Seguridad
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Si el código ya fue enviado -->
                <div id="step-verify-code" class="{{ session('code_sent') ? '' : 'hidden' }} space-y-4">
                    <form action="{{ route('perfil.verify.code') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="code" class="block text-xs font-bold text-on-surface-variant mb-1">
                                Código de 6 dígitos recibido:
                            </label>
                            <input type="text" id="code" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required
                                class="w-full px-4 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-center text-xl font-mono font-bold tracking-[6px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                placeholder="000000"/>
                            @error('code')
                                <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <form action="{{ route('perfil.send.code') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-primary hover:underline cursor-pointer">
                                    Reenviar código
                                </button>
                            </form>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="closeSecurityModal()" class="px-3.5 py-2 border border-outline-variant rounded-xl text-xs font-bold text-on-surface-variant hover:bg-surface-variant/40 transition-colors cursor-pointer">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-xs font-bold shadow hover:bg-[#002560] transition-colors flex items-center gap-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">check</span>
                                    Validar Código
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>

@auth
    <x-modal-logout />
@endauth

<script>
    // Manejo de Modal de Seguridad
    const modalBackdrop = document.getElementById('security-modal-backdrop');
    const modalCard = document.getElementById('security-modal-card');

    function openSecurityModal() {
        if (!modalBackdrop || !modalCard) return;
        modalBackdrop.classList.remove('hidden');
        requestAnimationFrame(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalCard.classList.remove('scale-95');
            modalCard.classList.add('scale-100');
        });
        document.body.classList.add('overflow-hidden');
    }

    function closeSecurityModal() {
        if (!modalBackdrop || !modalCard) return;
        modalCard.classList.remove('scale-100');
        modalCard.classList.add('scale-95');
        modalBackdrop.classList.add('opacity-0');
        setTimeout(() => {
            modalBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    // Tabs de Seguridad
    function switchSecurityTab(tab) {
        const tabBtnPassword = document.getElementById('tab-btn-password');
        const tabBtnCode = document.getElementById('tab-btn-code');
        const contentPassword = document.getElementById('tab-content-password');
        const contentCode = document.getElementById('tab-content-code');

        if (tab === 'password') {
            tabBtnPassword.classList.add('border-primary', 'text-primary', 'bg-primary/5');
            tabBtnPassword.classList.remove('border-transparent', 'text-on-surface-variant');

            tabBtnCode.classList.remove('border-primary', 'text-primary', 'bg-primary/5');
            tabBtnCode.classList.add('border-transparent', 'text-on-surface-variant');

            contentPassword.classList.remove('hidden');
            contentCode.classList.add('hidden');
        } else {
            tabBtnCode.classList.add('border-primary', 'text-primary', 'bg-primary/5');
            tabBtnCode.classList.remove('border-transparent', 'text-on-surface-variant');

            tabBtnPassword.classList.remove('border-primary', 'text-primary', 'bg-primary/5');
            tabBtnPassword.classList.add('border-transparent', 'text-on-surface-variant');

            contentCode.classList.remove('hidden');
            contentPassword.classList.add('hidden');
        }
    }

    // Toggle de visibilidad de contraseña
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    // Auto-apertura del modal si hubo error de validación o solicitud
    @if(session('open_security_modal') || $errors->has('password_actual') || $errors->has('code'))
        document.addEventListener('DOMContentLoaded', () => {
            openSecurityModal();
            @if(session('security_tab') === 'code' || $errors->has('code'))
                switchSecurityTab('code');
            @else
                switchSecurityTab('password');
            @endif
        });
    @endif
</script>
</body>
</html>
