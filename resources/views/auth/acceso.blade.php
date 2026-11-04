<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Acceso - HyK Mayorista</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Oswald:wght@400;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
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
                        "body-md": ["DM Sans", "sans-serif"],
                        "label-md": ["DM Sans", "sans-serif"],
                        "headline-xl": ["Oswald", "sans-serif"],
                        "poppins": ["DM Sans", "sans-serif"],
                        "dm": ["DM Sans", "sans-serif"],
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
        .register-section label,
        .login-section label {
            color: #1e293b !important;
            font-weight: 700 !important;
        }
        .alert-box,
        .alert-box span:not(.material-symbols-outlined),
        .alert-box p,
        .alert-box li,
        .register-section input,
        .register-section label,
        .register-section button,
        .register-section p,
        .register-section span:not(.material-symbols-outlined),
        .login-section input,
        .login-section label,
        .login-section button,
        .login-section p,
        .login-section a,
        .login-section span:not(.material-symbols-outlined),
        #sliding-overlay h1,
        #sliding-overlay p,
        #sliding-overlay button,
        #sliding-overlay span {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
        }
        .register-section input::placeholder,
        .register-section input::-webkit-input-placeholder,
        .register-section input::-moz-placeholder,
        .register-section input:-ms-input-placeholder,
        .login-section input::placeholder,
        .login-section input::-webkit-input-placeholder,
        .login-section input::-moz-placeholder,
        .login-section input:-ms-input-placeholder {
            font-family: 'Heading Now', 'Inter', system-ui, sans-serif !important;
        }
        .font-digits, .font-sans {
            font-family: 'Inter', system-ui, sans-serif !important;
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
<body class="bg-background text-on-surface font-body-md min-h-screen overflow-hidden antialiased">

<!-- Contenedor Principal Absoluto -->
<div class="relative w-full h-screen flex">

    <!-- Mensajes de Alerta -->
    @if(session('success'))
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-green-50 border border-green-300 text-green-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5 max-w-lg w-auto font-heading-now" role="alert">
            <span class="material-symbols-outlined text-green-600 text-xl shrink-0">check_circle</span>
            <span class="block sm:inline font-semibold text-sm font-heading-now">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-blue-50 border border-blue-300 text-blue-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5 max-w-lg w-auto font-heading-now" role="alert">
            <span class="material-symbols-outlined text-blue-600 text-xl shrink-0">info</span>
            <span class="block sm:inline font-semibold text-sm font-heading-now">{{ session('info') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-amber-50 border border-amber-300 text-amber-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5 max-w-lg w-auto font-heading-now" role="alert">
            <span class="material-symbols-outlined text-amber-600 text-xl shrink-0">warning</span>
            <span class="block sm:inline font-semibold text-sm font-heading-now">{{ session('warning') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 border border-red-300 text-red-900 px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5 max-w-lg w-auto font-heading-now" role="alert">
            <span class="material-symbols-outlined text-red-600 text-xl shrink-0">error</span>
            <span class="block sm:inline font-semibold text-sm font-heading-now">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 border border-red-300 text-red-900 px-5 py-3 rounded-xl shadow-lg font-heading-now max-w-lg w-auto" role="alert">
            <ul class="list-disc pl-5 text-sm font-heading-now space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MITAD IZQUIERDA: FORMULARIO DE REGISTRO    -->
    <!-- ========================================== -->
    <div class="w-full lg:w-1/2 h-full bg-surface flex flex-col items-center justify-center p-margin-mobile md:p-margin-desktop overflow-y-auto font-heading-now register-section">
        <div class="w-full max-w-lg space-y-xl">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl font-bold font-headline-lg uppercase text-on-surface mb-sm tracking-wide">Crea tu cuenta</h1>
                <p class="text-gray-500 font-heading-now font-normal tracking-wide">Complete sus datos para comenzar a operar.</p>
            </div>
            
            <form class="space-y-4 font-heading-now" method="POST" action="/register">
                @csrf
                <!-- Selector Mayorista / Minorista -->
                <label class="block font-heading-now font-bold text-xs sm:text-sm uppercase tracking-wider text-slate-800 mb-2">Tipo de Cliente</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 hover:bg-gray-50 has-[:checked]:border-primary has-[:checked]:ring-1 has-[:checked]:ring-primary has-[:checked]:bg-blue-50/70 transition-all font-heading-now shadow-sm">
                        <input {{ old('customer_type', 'retail') == 'retail' ? 'checked' : '' }} class="peer sr-only" name="customer_type" onclick="toggleDynamicFields('retail')" type="radio" value="retail"/>
                        <span class="flex flex-col flex-1">
                            <span class="block font-heading-now font-bold text-slate-800 tracking-wide mb-0.5">Minorista</span>
                            <span class="block text-xs text-gray-500 font-normal font-heading-now">Uso personal</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 hover:bg-gray-50 has-[:checked]:border-primary has-[:checked]:ring-1 has-[:checked]:ring-primary has-[:checked]:bg-blue-50/70 transition-all font-heading-now shadow-sm">
                        <input {{ old('customer_type') == 'wholesale' ? 'checked' : '' }} class="peer sr-only" name="customer_type" onclick="toggleDynamicFields('wholesale')" type="radio" value="wholesale"/>
                        <span class="flex flex-col flex-1">
                            <span class="block font-heading-now font-bold text-slate-800 tracking-wide mb-0.5">Mayorista</span>
                            <span class="block text-xs text-gray-500 font-normal font-heading-now">Empresas</span>
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Nombre y Apellido</label>
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="name" type="text" value="{{ old('name') }}" required/>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Teléfono</label>
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="phone" type="tel" value="{{ old('phone') }}" required/>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Correo Electrónico</label>
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="email" type="email" value="{{ old('email') }}" required/>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Contraseña</label>
                        <div class="relative mt-0.5">
                            <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-11 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" id="register_password" name="password" type="password" required/>
                            <button type="button" onclick="togglePasswordVisibility('register_password', 'eye-register')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer" aria-label="Mostrar u ocultar contraseña">
                                <span id="eye-register" class="material-symbols-outlined text-xl select-none">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Campos Dinámicos -->
                <div id="retail-fields" class="{{ old('customer_type', 'retail') == 'retail' ? 'block' : 'hidden' }} pt-4 border-t font-heading-now">
                    <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">DNI</label>
                    <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="dni" value="{{ old('dni') }}" type="text"/>
                </div>
                <div id="wholesale-fields" class="{{ old('customer_type') == 'wholesale' ? 'grid grid-cols-2' : 'hidden' }} gap-4 pt-4 border-t font-heading-now">
                    <div>
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">CUIT</label>
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="cuit" value="{{ old('cuit') }}" type="text"/>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Razón Social</label>
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="business_name" value="{{ old('business_name') }}" type="text"/>
                    </div>
                </div>

                <button class="w-full bg-secondary text-white font-heading-now font-semibold tracking-widest uppercase text-sm py-3 rounded-lg hover:bg-green-700 transition mt-4" type="submit">
                    Registrarse
                </button>
            </form>
            <p class="text-center text-sm text-gray-400 font-heading-now lg:hidden mt-4">
                ¿Ya tienes cuenta? <button onclick="slidePanel('left')" class="text-primary font-semibold hover:underline font-heading-now" type="button">Inicia sesión</button>
            </p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MITAD DERECHA: FORMULARIO DE LOGIN         -->
    <!-- ========================================== -->
    <div class="w-full lg:w-1/2 h-full bg-surface flex flex-col items-center justify-center p-margin-mobile md:p-margin-desktop overflow-y-auto font-heading-now login-section">
        <div class="w-full max-w-sm space-y-xl">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl font-bold font-headline-lg uppercase text-on-surface mb-sm tracking-wide">Bienvenido de nuevo</h1>
                <p class="text-gray-500 font-heading-now font-normal tracking-wide">Inicia sesión en HyK Mayorista.</p>
            </div>
            
            <form class="space-y-4 font-heading-now" method="POST" action="/login">
                @csrf
                <div>
                    <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Correo Electrónico</label>
                    <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 mt-0.5 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" type="email" required/>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 mb-1.5 font-heading-now">Contraseña</label>
                    <div class="relative mt-0.5">
                        <input class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-11 font-heading-now text-sm text-slate-900 shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" id="login_password" name="password" placeholder="Tu contraseña" type="password" required/>
                        <button type="button" onclick="togglePasswordVisibility('login_password', 'eye-login')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer" aria-label="Mostrar u ocultar contraseña">
                            <span id="eye-login" class="material-symbols-outlined text-xl select-none">visibility</span>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-2 font-heading-now">
                    <label class="flex items-center space-x-2 text-sm text-gray-600 font-heading-now cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary">
                        <span class="font-heading-now text-xs uppercase tracking-wider text-gray-600">Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs uppercase tracking-wider text-primary hover:underline font-heading-now font-semibold">¿Olvidaste tu contraseña?</a>
                </div>

                <button class="w-full bg-primary text-white font-heading-now font-semibold tracking-widest uppercase text-sm py-3 rounded-lg hover:bg-blue-900 transition mt-6" type="submit">
                    Iniciar Sesión
                </button>
            </form>
            <p class="text-center text-sm text-gray-400 font-heading-now lg:hidden mt-4">
                ¿No tienes cuenta? <button onclick="slidePanel('right')" class="text-primary font-semibold hover:underline font-heading-now" type="button">Regístrate</button>
            </p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PANEL DE IMAGEN ANIMADO (OVERLAY)          -->
    <!-- ========================================== -->
    <div id="sliding-overlay" class="hidden lg:flex absolute top-0 left-0 w-1/2 h-full bg-cover bg-center transition-transform duration-700 ease-in-out z-10 shadow-2xl font-heading-now" 
         style="background-image: url('{{ asset('images/fondo-login.jpeg') }}')">
        
        <!-- Gradiente oscuro para leer el texto -->
        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary/20 mix-blend-multiply"></div>
        
        <div class="relative z-10 flex flex-col justify-center items-center h-full w-full text-center px-12 font-heading-now">
            
            <!-- Contenido que se muestra cuando la imagen está tapando el Registro (O sea, estamos en LOGIN) -->
            <div id="overlay-text-login" class="transition-opacity duration-500 font-heading-now">
                <h1 class="text-5xl font-bold text-white mb-4 tracking-tight font-heading-now">HyK Mayorista</h1>
                <p class="text-lg text-white/90 mb-8 font-heading-now">Su socio estratégico en abastecimiento.</p>
                <p class="text-white mb-4 font-heading-now">¿Aún no eres cliente?</p>
                <button onclick="slidePanel('right')" type="button" class="border-2 border-white text-white font-semibold tracking-wider uppercase text-sm py-2.5 px-8 rounded-full hover:bg-white hover:text-primary transition font-heading-now cursor-pointer">
                    Crear Cuenta
                </button>
            </div>

            <!-- Contenido que se muestra cuando la imagen tapa el Login (O sea, estamos en REGISTRO) -->
            <div id="overlay-text-register" class="absolute hidden opacity-0 transition-opacity duration-500 font-heading-now">
                <h1 class="text-5xl font-bold text-white mb-4 tracking-tight font-heading-now">¡Bienvenido!</h1>
                <p class="text-lg text-white/90 mb-8 font-heading-now">Acceda a precios exclusivos y gestione sus pedidos.</p>
                <p class="text-white mb-4 font-heading-now">¿Ya tienes una cuenta?</p>
                <button onclick="slidePanel('left')" type="button" class="border-2 border-white text-white font-semibold tracking-wider uppercase text-sm py-2.5 px-8 rounded-full hover:bg-white hover:text-primary transition font-heading-now cursor-pointer">
                    Iniciar Sesión
                </button>
            </div>
            
        </div>
    </div>
</div>

<script>
    // Lógica para mostrar/ocultar contraseñas
    function togglePasswordVisibility(inputId, iconId) {
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

    // Lógica para cambiar entre DNI/CUIT (Registro)
    function toggleDynamicFields(type) {
        const retail = document.getElementById('retail-fields');
        const wholesale = document.getElementById('wholesale-fields');
        if (type === 'retail') {
            retail.classList.remove('hidden'); retail.classList.add('block');
            wholesale.classList.remove('grid'); wholesale.classList.remove('grid-cols-2'); wholesale.classList.add('hidden');
        } else {
            retail.classList.remove('block'); retail.classList.add('hidden');
            wholesale.classList.remove('hidden'); wholesale.classList.add('grid'); wholesale.classList.add('grid-cols-2');
        }
    }

    // Lógica de Animación del Panel (Login vs Registro)
    function slidePanel(direction) {
        const panel = document.getElementById('sliding-overlay');
        const textLogin = document.getElementById('overlay-text-login');
        const textRegister = document.getElementById('overlay-text-register');

        if (direction === 'right') {
            // Mover panel a la derecha (Tapa el login, destapa el registro)
            panel.classList.add('translate-x-full');
            
            // Ocultar texto del login, mostrar el de registro
            textLogin.classList.add('opacity-0');
            setTimeout(() => {
                textLogin.classList.add('hidden');
                textRegister.classList.remove('hidden');
                setTimeout(() => textRegister.classList.remove('opacity-0'), 50);
            }, 300);
            
        } else {
            // Mover panel a la izquierda (Tapa el registro, destapa el login)
            panel.classList.remove('translate-x-full');
            
            // Ocultar texto del registro, mostrar el de login
            textRegister.classList.add('opacity-0');
            setTimeout(() => {
                textRegister.classList.add('hidden');
                textLogin.classList.remove('hidden');
                setTimeout(() => textLogin.classList.remove('opacity-0'), 50);
            }, 300);
        }
    }

    // Funciones que se ejecutan al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Desaparecer carteles de error/exito a los 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-box');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500); // remover del DOM luego del fade out
            });
        }, 5000);

        // Si recarga la página por un error en el registro (sabemos porque "name" fue enviado)
        // Movemos el panel automáticamente a la derecha para mostrarle el registro al usuario
        @if(old('name'))
            slidePanel('right');
            // Quitamos la transición para que no se vea el deslizamiento al recargar la página (que aparezca de golpe)
            const panel = document.getElementById('sliding-overlay');
            panel.classList.remove('duration-700');
            panel.classList.add('duration-0');
            setTimeout(() => {
                panel.classList.remove('duration-0');
                panel.classList.add('duration-700');
            }, 50);
        @endif
    });
</script>
</body>
</html>
