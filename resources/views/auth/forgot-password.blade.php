<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Recuperar Contraseña - HyK Mayorista</title>
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

    <!-- Tarjeta Principal de Recuperación -->
    <div class="w-full max-w-md bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-white/20 p-8 sm:p-10 transition-all form-card font-heading-now">
        <!-- Encabezado con Marca e Icono -->
        <div class="text-center mb-8">
            <a href="{{ route('catalogo') }}" class="inline-block mb-3">
                <span class="text-2xl font-black text-primary tracking-tight font-heading-now">HyK Mayorista</span>
            </a>
            <div class="w-14 h-14 mx-auto mb-4 bg-blue-50 text-primary rounded-full flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">lock_reset</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg uppercase text-slate-900 tracking-wide">¿Olvidaste tu contraseña?</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed font-heading-now">
                Ingresa el correo electrónico asociado a tu cuenta. Te enviaremos un código de seguridad de <span class="font-bold text-slate-800 font-digits">6</span> dígitos válido por <span class="font-bold text-slate-800 font-digits">15</span> minutos.
            </p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5 font-heading-now">
            @csrf

            <div>
                <label for="email" class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-xl">mail</span>
                    </div>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', session('password_reset_email')) }}" 
                        placeholder="tu-correo@ejemplo.com"
                        required 
                        autofocus
                        class="w-full pl-11 pr-4 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary' }} bg-white text-sm text-slate-900 font-heading-now shadow-sm outline-none transition"
                    />
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-heading-now">
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
                <span>Enviar código de verificación</span>
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
            </button>
        </form>

        <!-- Enlaces Inferiores -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-blue-900 hover:underline transition font-heading-now">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                <span>Volver al inicio de sesión</span>
            </a>
        </div>
    </div>

</body>
</html>