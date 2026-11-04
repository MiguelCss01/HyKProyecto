<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Nueva Contraseña - HyK Mayorista</title>
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
            unicode-range: U+0000-002F, U+003A-2021, U+2023-FFFF;
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
            unicode-range: U+0000-002F, U+003A-2021, U+2023-FFFF;
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

        /* Contraseñas: Bolitas proporcionales, legibles y bien espaciadas */
        .form-card input[type="password"],
        .password-input[type="password"] {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-size: 1.05rem !important;
            letter-spacing: 0.2em !important;
        }

        .form-card input[type="password"]::placeholder,
        .password-input[type="password"]::placeholder {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif !important;
            font-size: 1.15rem !important;
            letter-spacing: 0.24em !important;
            color: #94a3b8 !important;
        }

        /* Cuando se muestra la contraseña (type="text" tras presionar el botón de visibilidad) */
        .form-card input.password-input[type="text"],
        input#password[type="text"],
        input#password_confirmation[type="text"] {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
            font-size: 0.875rem !important;
            letter-spacing: normal !important;
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

    @if($errors->any())
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 border border-red-300 text-red-900 px-5 py-3 rounded-xl shadow-lg max-w-md w-full font-heading-now" role="alert">
            <ul class="list-disc pl-5 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tarjeta Principal de Restablecimiento -->
    <div class="w-full max-w-md bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-white/20 p-8 sm:p-10 transition-all form-card font-heading-now">
        <!-- Encabezado con Marca e Icono -->
        <div class="text-center mb-8">
            <a href="{{ route('catalogo') }}" class="inline-block mb-3">
                <span class="text-2xl font-black text-primary tracking-tight font-heading-now">HyK Mayorista</span>
            </a>
            <div class="w-14 h-14 mx-auto mb-4 bg-green-50 text-secondary rounded-full flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">key</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-headline-lg uppercase text-slate-900 tracking-wide">Crea tu nueva contraseña</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed font-heading-now">
                Ingresa una contraseña segura para tu cuenta asociada a <strong class="text-slate-800">{{ $email }}</strong>.
            </p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('password.update') }}" method="POST" class="space-y-5 font-heading-now">
            @csrf

            <!-- Campos Ocultos -->
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- Nueva Contraseña -->
            <div>
                <label for="password" class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">
                    Nueva Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autofocus
                        placeholder="••••••••"
                        class="w-full pl-11 pr-11 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary' }} bg-white text-sm text-slate-900 font-heading-now shadow-sm outline-none transition password-input"
                    />
                    <button 
                        type="button" 
                        onclick="toggleVisibility('password', 'eye-icon-1')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                    >
                        <span id="eye-icon-1" class="material-symbols-outlined text-xl">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-heading-now">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirmación de Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">
                    Confirmar Nueva Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-xl">lock_clock</span>
                    </div>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-11 pr-11 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-primary focus:ring-1 focus:ring-primary text-sm text-slate-900 font-heading-now shadow-sm outline-none transition password-input"
                    />
                    <button 
                        type="button" 
                        onclick="toggleVisibility('password_confirmation', 'eye-icon-2')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer"
                    >
                        <span id="eye-icon-2" class="material-symbols-outlined text-xl">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Requisitos de Seguridad -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 text-xs text-gray-500 space-y-1 font-heading-now">
                <p class="font-bold text-slate-800">Tu contraseña debe contener:</p>
                <ul class="list-disc pl-4 space-y-0.5 text-gray-600">
                    <li>Al menos <span class="font-bold text-slate-800 font-digits">8</span> caracteres</li>
                    <li>Ambas contraseñas deben coincidir exactamente</li>
                </ul>
            </div>

            <!-- Botón de Envío -->
            <button 
                type="submit" 
                class="w-full bg-secondary hover:bg-green-700 text-white font-heading-now font-semibold tracking-widest uppercase text-sm py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
            >
                <span>Restablecer e iniciar sesión</span>
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">check_circle</span>
            </button>
        </form>

        <!-- Enlaces Inferiores -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-blue-900 hover:underline transition font-heading-now">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                <span>Cancelar y volver al inicio de sesión</span>
            </a>
        </div>
    </div>

    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>

</body>
</html>