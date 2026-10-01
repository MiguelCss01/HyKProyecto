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

    @if($errors->any())
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-5 py-3 rounded-lg shadow-lg max-w-md w-full" role="alert">
            <ul class="list-disc pl-5 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tarjeta Principal de Restablecimiento -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10 transition-all">
        <!-- Encabezado con Marca e Icono -->
        <div class="text-center mb-8">
            <a href="{{ route('catalogo') }}" class="inline-block mb-3">
                <span class="text-2xl font-black text-primary tracking-tight">HyK Mayorista</span>
            </a>
            <div class="w-14 h-14 mx-auto mb-4 bg-green-50 text-secondary rounded-full flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">key</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Crea tu nueva contraseña</h1>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Ingresa una contraseña segura para tu cuenta asociada a <strong>{{ $email }}</strong>.
            </p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Campos Ocultos -->
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- Nueva Contraseña -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
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
                        placeholder="Mínimo 8 caracteres"
                        class="w-full pl-11 pr-11 py-2.5 rounded-lg border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:border-primary focus:ring-primary' }} text-sm focus:ring-2 focus:ring-opacity-20 outline-none transition"
                    />
                    <button 
                        type="button" 
                        onclick="toggleVisibility('password', 'eye-icon-1')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <span id="eye-icon-1" class="material-symbols-outlined text-xl">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirmación de Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
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
                        placeholder="Repite la nueva contraseña"
                        class="w-full pl-11 pr-11 py-2.5 rounded-lg border border-gray-300 focus:border-primary focus:ring-primary text-sm focus:ring-2 focus:ring-opacity-20 outline-none transition"
                    />
                    <button 
                        type="button" 
                        onclick="toggleVisibility('password_confirmation', 'eye-icon-2')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <span id="eye-icon-2" class="material-symbols-outlined text-xl">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Requisitos de Seguridad -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 text-xs text-gray-500 space-y-1">
                <p class="font-semibold text-gray-700">Tu contraseña debe contener:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    <li>Al menos 8 caracteres</li>
                    <li>Ambas contraseñas deben coincidir exactamente</li>
                </ul>
            </div>

            <!-- Botón de Envío -->
            <button 
                type="submit" 
                class="w-full bg-secondary hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
            >
                <span>Restablecer e iniciar sesión</span>
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">check_circle</span>
            </button>
        </form>

        <!-- Enlaces Inferiores -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-blue-900 hover:underline transition">
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
