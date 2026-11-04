<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Verificar Código - HyK Mayorista</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Oswald:wght@400;600;700&display=swap" rel="stylesheet"/>
    <!-- Tailwind Config injected from Design System -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-fixed-dim": "#b2c5ff", "on-tertiary-fixed": "#141d23", "outline-variant": "#c3c6d5",
                        "on-primary-container": "#a5bdff", "surface-tint": "#2559bf", "primary-fixed": "#dae2ff",
                        "background": "#f8f9fa", "on-surface": "#191c1d", "secondary": "#006e25", "on-secondary": "#ffffff",
                        "surface-container-low": "#f3f4f5", "primary": "#00317e"
                    },
                    spacing: { "margin-mobile": "16px", "margin-desktop": "64px", "xl": "40px", "md": "16px", "sm": "8px", "base": "4px" },
                    fontFamily: {
                        "headline-lg": ["Oswald", "sans-serif"],
                        "headline-xl": ["Oswald", "sans-serif"],
                        "heading-now": ["'Heading Now'", "'Heading Now 71-78'", "'HeadingNow Trial'", "'Heading Now Trial'", "sans-serif"]
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
            unicode-range: U+0000-002F, U+003A-FFFF;
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
            unicode-range: U+0000-002F, U+003A-FFFF;
        }
        .font-heading-now {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
        }
        .form-card label {
            color: #1e293b !important;
            font-weight: 700 !important;
        }
        .form-card input,
        .form-card label,
        .form-card button,
        .form-card p,
        .form-card a,
        .form-card span:not(.material-symbols-outlined) {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
        }
        .form-card input::placeholder,
        .form-card input::-webkit-input-placeholder,
        .form-card input::-moz-placeholder,
        .form-card input:-ms-input-placeholder {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
        }
        #code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        }
        .font-digits, .font-sans {
            font-family: 'Inter', system-ui, sans-serif !important;
        }
        body {
            background: linear-gradient(135deg, #001a44 0%, #00317e 50%, #1565c0 100%) !important;
            min-height: 100vh;
        }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-weight: normal !important;
            font-style: normal !important;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none !important;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga' !important;
            font-feature-settings: 'liga' !important;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 antialiased font-heading-now">

    <!-- Mensajes de Alerta Flotantes -->
    @if(session('success'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-green-50 border border-green-300 text-green-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 max-w-md w-full font-heading-now" role="alert">
            <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 border border-red-300 text-red-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 max-w-md w-full font-heading-now" role="alert">
            <span class="material-symbols-outlined text-red-600 text-xl">error</span>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-blue-50 border border-blue-300 text-blue-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 max-w-md w-full font-heading-now" role="alert">
            <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
            <span class="text-sm font-semibold">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Tarjeta Principal de Verificación -->
    <div class="w-full max-w-md bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-white/20 p-8 sm:p-10 transition-all form-card font-heading-now">
        <!-- Encabezado con Marca e Icono -->
        <div class="text-center mb-6">
            <a href="{{ route('catalogo') }}" class="inline-block mb-3">
                <span class="text-2xl font-black text-primary tracking-tight font-heading-now">HyK Mayorista</span>
            </a>
            <div class="w-14 h-14 mx-auto mb-4 bg-blue-50 text-primary rounded-full flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">mark_email_read</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg uppercase text-slate-900 tracking-wide">Verifica tu código</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed font-heading-now">
                Hemos enviado un código numérico de <span class="font-bold text-slate-800 font-digits">6</span> dígitos a:
                <br>
                <span class="font-bold text-slate-800 break-all">{{ $email }}</span>
            </p>
        </div>

        <!-- Alerta de expiración -->
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs px-3.5 py-2.5 rounded-lg flex items-center gap-2 mb-6 font-heading-now">
            <span class="material-symbols-outlined text-amber-600 text-lg">schedule</span>
            <span>El código es válido durante <strong class="font-bold text-amber-950 font-digits">15</strong> minutos.</span>
        </div>

        <!-- Formulario para ingresar el código -->
        <form action="{{ route('password.verify_code') }}" method="POST" class="space-y-6 font-heading-now">
            @csrf

            <div>
                <label for="code" class="block text-center text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-2 font-heading-now">
                    Ingresa el código de <span class="font-digits">6</span> dígitos
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        placeholder="••••••"
                        required 
                        autofocus
                        class="w-full text-center text-3xl font-mono font-bold tracking-[0.5em] py-3.5 px-4 rounded-xl border {{ $errors->has('code') ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:border-primary focus:ring-primary' }} focus:ring-2 focus:ring-opacity-20 outline-none transition"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                    />
                </div>
                @error('code')
                    <p class="mt-2 text-center text-xs text-red-600 flex items-center justify-center gap-1 font-heading-now">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Botón de Envío -->
            <button 
                type="submit" 
                class="w-full bg-primary hover:bg-blue-900 text-white font-heading-now font-semibold tracking-widest uppercase text-sm py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
            >
                <span>Validar código y continuar</span>
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">check</span>
            </button>
        </form>

        <!-- Reenvío de código y cambio de correo -->
        <div class="mt-6 pt-5 border-t border-gray-100 flex flex-col items-center gap-3 font-heading-now">
            <form action="{{ route('password.resend_code') }}" method="POST">
                @csrf
                <p class="text-xs text-gray-500 text-center font-heading-now">
                    ¿No te llegó el correo o necesitas otro código?
                    <button type="submit" class="text-primary font-bold hover:underline cursor-pointer ml-1 font-heading-now">
                        Reenviar código
                    </button>
                </p>
            </form>

            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-gray-600 hover:text-primary hover:underline font-heading-now">
                ¿El correo es incorrecto? Cambiar correo
            </a>
        </div>

        <!-- Enlace a Login -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-primary transition font-heading-now">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Volver al inicio de sesión</span>
            </a>
        </div>
    </div>

</body>
</html>