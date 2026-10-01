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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
                    fontFamily: { "headline-lg": ["Inter"], "body-md": ["Inter"], "label-md": ["Inter"], "headline-xl": ["Inter"] }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen flex items-center justify-center p-4 antialiased">

    <!-- Mensajes de Alerta Flotantes -->
    @if(session('success'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-md w-full" role="alert">
            <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-md w-full" role="alert">
            <span class="material-symbols-outlined text-red-600 text-xl">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-blue-100 border border-blue-400 text-blue-800 px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-md w-full" role="alert">
            <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
            <span class="text-sm font-medium">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Tarjeta Principal de Recuperación -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10 transition-all">
        <!-- Encabezado con Marca e Icono -->
        <div class="text-center mb-8">
            <a href="{{ route('catalogo') }}" class="inline-block mb-3">
                <span class="text-2xl font-black text-primary tracking-tight">HyK Mayorista</span>
            </a>
            <div class="w-14 h-14 mx-auto mb-4 bg-blue-50 text-primary rounded-full flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">lock_reset</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">¿Olvidaste tu contraseña?</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Ingresa el correo electrónico asociado a tu cuenta. Te enviaremos un código de seguridad de 6 dígitos válido por 15 minutos.
            </p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
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
                        class="w-full pl-11 pr-4 py-2.5 rounded-lg border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:border-primary focus:ring-primary' }} text-sm focus:ring-2 focus:ring-opacity-20 outline-none transition"
                    />
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Botón de Envío -->
            <button 
                type="submit" 
                class="w-full bg-primary hover:bg-blue-900 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
            >
                <span>Enviar código de verificación</span>
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
            </button>
        </form>

        <!-- Enlaces Inferiores -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-blue-900 hover:underline transition">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                <span>Volver al inicio de sesión</span>
            </a>
        </div>
    </div>

</body>
</html>
