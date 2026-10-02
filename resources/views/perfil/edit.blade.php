<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Modificar Perfil y Contraseña - HyK Mayorista</title>
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
        <a href="{{ route('perfil.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            <span>Volver a Mi Perfil</span>
        </a>

        <div class="h-5 w-px bg-outline-variant hidden sm:block"></div>

        <button type="button" onclick="openLogoutModal()" class="text-sm font-bold text-red-600 hover:underline cursor-pointer">
            Salir
        </button>
    </div>
</header>

<!-- Main Container -->
<main class="flex-1 max-w-3xl w-full mx-auto px-4 py-8 pb-20">

    <!-- Banner de Sesión Verificada -->
    <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-2xl flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600 text-2xl">verified_user</span>
            <div>
                <p class="text-sm font-bold">Identidad verificada</p>
                <p class="text-xs text-green-700">Tienes autorización activa para modificar tus datos personales y actualizar tu contraseña.</p>
            </div>
        </div>
        <span class="hidden sm:inline-flex items-center gap-1 text-xs bg-green-200/70 text-green-900 font-semibold px-2.5 py-1 rounded-full">
            <span class="material-symbols-outlined text-xs">timer</span> 15 min
        </span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm mb-1 text-red-900">
                <span class="material-symbols-outlined text-base">error</span>
                Por favor corrige los siguientes errores:
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario de Edición -->
    <div class="bg-surface-bright rounded-2xl border border-outline-variant shadow-sm p-6 md:p-8">
        
        <div class="pb-5 mb-6 border-b border-outline-variant/60 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface">Modificar Perfil y Seguridad</h1>
                <p class="text-xs text-on-surface-variant mt-0.5">Actualiza tu información de contacto, identificación o establece una nueva contraseña.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                {{ $user->tipo_cliente === 'mayorista' ? 'Mayorista' : 'Minorista' }}
            </span>
        </div>

        <form action="{{ route('perfil.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Sección 1: Datos Personales -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">person</span>
                    Información Personal y Contacto
                </h2>

                <div>
                    <label for="name" class="block text-xs font-bold text-on-surface-variant mb-1">
                        Nombre y Apellido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                    @error('name')
                        <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="block text-xs font-bold text-on-surface-variant mb-1">
                            Teléfono de Contacto <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" required
                            placeholder="Ej: 11 2345-6789"
                            class="w-full px-3.5 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                        @error('telefono')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">
                            Correo Electrónico (No modificable)
                        </label>
                        <div class="relative">
                            <input type="email" disabled value="{{ $user->email }}"
                                class="w-full px-3.5 py-2.5 bg-surface-variant/40 border border-outline-variant rounded-xl text-sm text-on-surface-variant cursor-not-allowed"/>
                            <span class="material-symbols-outlined text-base absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant">lock</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant/70 mt-1">El email identifica tu cuenta.</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-outline-variant/60 my-6"></div>

            <!-- Sección 2: Identificación Fiscal según Tipo de Cliente -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">badge</span>
                    Identificación Fiscal ({{ ucfirst($user->tipo_cliente) }})
                </h2>

                @if($user->tipo_cliente === 'mayorista')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="razon_social" class="block text-xs font-bold text-on-surface-variant mb-1">
                                Razón Social <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="razon_social" name="razon_social" value="{{ old('razon_social', $user->razon_social) }}" required
                                placeholder="Nombre de la empresa o negocio"
                                class="w-full px-3.5 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                            @error('razon_social')
                                <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cuit" class="block text-xs font-bold text-on-surface-variant mb-1">
                                CUIT Comercial <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cuit" name="cuit" value="{{ old('cuit', $user->cuit) }}" required
                                placeholder="Ej: 20-12345678-9"
                                class="w-full px-3.5 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm font-mono focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                            @error('cuit')
                                <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <div>
                        <label for="dni" class="block text-xs font-bold text-on-surface-variant mb-1">
                            Documento Nacional de Identidad (DNI) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="dni" name="dni" value="{{ old('dni', $user->dni) }}" required maxlength="8"
                            placeholder="Ej: 38123456 (7 u 8 dígitos)"
                            class="w-full sm:w-1/2 px-3.5 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm font-mono focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                        @error('dni')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div class="border-t border-outline-variant/60 my-6"></div>

            <!-- Sección 3: Cambio de Contraseña (Opcional) -->
            <div class="space-y-4 bg-surface p-5 rounded-2xl border border-outline-variant/60">
                <div class="flex items-center gap-2 text-primary font-bold text-sm">
                    <span class="material-symbols-outlined text-base">password</span>
                    Cambiar Contraseña (Opcional)
                </div>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Si deseas conservar tu contraseña actual, deja estos campos en blanco. Si deseas cambiarla, ingresa la nueva clave (mínimo 8 caracteres).
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="nueva_password" class="block text-xs font-bold text-on-surface-variant mb-1">
                            Nueva Contraseña:
                        </label>
                        <div class="relative">
                            <input type="password" id="nueva_password" name="nueva_password" minlength="8"
                                placeholder="••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                            <button type="button" onclick="togglePasswordVisibility('nueva_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                        @error('nueva_password')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nueva_password_confirmation" class="block text-xs font-bold text-on-surface-variant mb-1">
                            Confirmar Nueva Contraseña:
                        </label>
                        <div class="relative">
                            <input type="password" id="nueva_password_confirmation" name="nueva_password_confirmation" minlength="8"
                                placeholder="••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 bg-surface-bright border border-outline-variant rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"/>
                            <button type="button" onclick="togglePasswordVisibility('nueva_password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('perfil.index') }}" class="px-5 py-2.5 border border-outline-variant rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-variant/40 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold shadow-md hover:bg-[#002560] transition-all flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Guardar Cambios
                </button>
            </div>

        </form>

    </div>

</main>

@auth
    <x-modal-logout />
@endauth

<script>
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
</script>
</body>
</html>
