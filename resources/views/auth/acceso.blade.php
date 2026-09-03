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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
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
<body class="bg-background text-on-surface font-body-md min-h-screen overflow-hidden antialiased">

<!-- Contenedor Principal Absoluto -->
<div class="relative w-full h-screen flex">

    <!-- Mensajes de Alerta -->
    @if(session('success'))
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-box absolute top-4 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MITAD IZQUIERDA: FORMULARIO DE REGISTRO    -->
    <!-- ========================================== -->
    <div class="w-full lg:w-1/2 h-full bg-surface flex flex-col items-center justify-center p-margin-mobile md:p-margin-desktop overflow-y-auto">
        <div class="w-full max-w-lg space-y-xl">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl font-bold text-on-surface mb-sm">Crea tu cuenta</h1>
                <p class="text-gray-500">Complete sus datos para comenzar a operar.</p>
            </div>
            
            <form class="space-y-4" method="POST" action="/register">
                @csrf
                <!-- Selector Mayorista / Minorista -->
                <label class="block font-semibold">Tipo de Cliente</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 has-[:checked]:border-primary has-[:checked]:bg-blue-50 transition-all">
                        <input {{ old('customer_type', 'retail') == 'retail' ? 'checked' : '' }} class="peer sr-only" name="customer_type" onclick="toggleDynamicFields('retail')" type="radio" value="retail"/>
                        <span class="flex flex-col flex-1">
                            <span class="block font-bold mb-1">Minorista</span>
                            <span class="block text-sm text-gray-500">Uso personal</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 has-[:checked]:border-primary has-[:checked]:bg-blue-50 transition-all">
                        <input {{ old('customer_type') == 'wholesale' ? 'checked' : '' }} class="peer sr-only" name="customer_type" onclick="toggleDynamicFields('wholesale')" type="radio" value="wholesale"/>
                        <span class="flex flex-col flex-1">
                            <span class="block font-bold mb-1">Mayorista</span>
                            <span class="block text-sm text-gray-500">Empresas</span>
                        </span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block font-semibold text-sm">Nombre y Apellido</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="name" type="text" value="{{ old('name') }}" required/>
                    </div>
                    <div>
                        <label class="block font-semibold text-sm">Teléfono</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="phone" type="tel" value="{{ old('phone') }}" required/>
                    </div>
                    <div>
                        <label class="block font-semibold text-sm">Correo Electrónico</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="email" type="email" value="{{ old('email') }}" required/>
                    </div>
                    <div class="col-span-2">
                        <label class="block font-semibold text-sm">Contraseña</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="password" type="password" required/>
                    </div>
                </div>

                <!-- Campos Dinámicos -->
                <div id="retail-fields" class="{{ old('customer_type', 'retail') == 'retail' ? 'block' : 'hidden' }} pt-4 border-t">
                    <label class="block font-semibold text-sm">DNI</label>
                    <input class="w-full rounded border px-4 py-2 mt-1" name="dni" value="{{ old('dni') }}" type="text"/>
                </div>
                <div id="wholesale-fields" class="{{ old('customer_type') == 'wholesale' ? 'grid grid-cols-2' : 'hidden' }} gap-4 pt-4 border-t">
                    <div>
                        <label class="block font-semibold text-sm">CUIT</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="cuit" value="{{ old('cuit') }}" type="text"/>
                    </div>
                    <div>
                        <label class="block font-semibold text-sm">Razón Social</label>
                        <input class="w-full rounded border px-4 py-2 mt-1" name="business_name" value="{{ old('business_name') }}" type="text"/>
                    </div>
                </div>

                <button class="w-full bg-secondary text-white font-bold py-3 rounded-lg hover:bg-green-700 transition mt-4" type="submit">
                    Registrarse
                </button>
            </form>
            <p class="text-center text-sm text-gray-500 lg:hidden mt-4">
                ¿Ya tienes cuenta? <button onclick="slidePanel('left')" class="text-primary font-bold hover:underline" type="button">Inicia sesión</button>
            </p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MITAD DERECHA: FORMULARIO DE LOGIN         -->
    <!-- ========================================== -->
    <div class="w-full lg:w-1/2 h-full bg-surface flex flex-col items-center justify-center p-margin-mobile md:p-margin-desktop overflow-y-auto">
        <div class="w-full max-w-sm space-y-xl">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl font-bold text-on-surface mb-sm">Bienvenido de nuevo</h1>
                <p class="text-gray-500">Inicia sesión en HyK Mayorista.</p>
            </div>
            
            <form class="space-y-4" method="POST" action="/login">
                @csrf
                <div>
                    <label class="block font-semibold text-sm">Correo Electrónico</label>
                    <input class="w-full rounded border px-4 py-2 mt-1" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" type="email" required/>
                </div>
                <div>
                    <label class="block font-semibold text-sm">Contraseña</label>
                    <input class="w-full rounded border px-4 py-2 mt-1" name="password" placeholder="Tu contraseña" type="password" required/>
                </div>
                
                <div class="flex items-center justify-between mt-2">
                    <label class="flex items-center space-x-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded text-primary">
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-primary hover:underline">¿Olvidaste tu contraseña?</a>
                </div>

                <button class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-blue-900 transition mt-6" type="submit">
                    Iniciar Sesión
                </button>
            </form>
            <p class="text-center text-sm text-gray-500 lg:hidden mt-4">
                ¿No tienes cuenta? <button onclick="slidePanel('right')" class="text-primary font-bold hover:underline" type="button">Regístrate</button>
            </p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PANEL DE IMAGEN ANIMADO (OVERLAY)          -->
    <!-- ========================================== -->
    <div id="sliding-overlay" class="hidden lg:flex absolute top-0 left-0 w-1/2 h-full bg-cover bg-center transition-transform duration-700 ease-in-out z-10 shadow-2xl" 
         style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCygL2ZE5I_RzjF7y1dZc0VRR7uR-uNlKRYK2Ys0DkqO2cyzebYqEQDq-CVU9VWfGLEeWkhcEDvqmUVm6ybrkiQqTNWv80p6xSSAZP7gR8bQ_-F2Awipp1e6-jxwOfo1A3p7KACGABs9v-577KuoESHsUtvnoXzbSiZhOGsMnwZtPrw9VsdKgviBVq7NFf63z3OhveXDDCIeUWYMS6KPC4m9OwFSmlAOUMwYOA8YD2UGRQWT-elX-bT')">
        
        <!-- Gradiente oscuro para leer el texto -->
        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary/20 mix-blend-multiply"></div>
        
        <div class="relative z-10 flex flex-col justify-center items-center h-full w-full text-center px-12">
            
            <!-- Contenido que se muestra cuando la imagen está tapando el Registro (O sea, estamos en LOGIN) -->
            <div id="overlay-text-login" class="transition-opacity duration-500">
                <h1 class="text-5xl font-bold text-white mb-4 tracking-tight">HyK Mayorista</h1>
                <p class="text-lg text-white/90 mb-8">Su socio estratégico en abastecimiento.</p>
                <p class="text-white mb-4">¿Aún no eres cliente?</p>
                <button onclick="slidePanel('right')" type="button" class="border-2 border-white text-white font-bold py-2 px-8 rounded-full hover:bg-white hover:text-primary transition">
                    Crear Cuenta
                </button>
            </div>

            <!-- Contenido que se muestra cuando la imagen tapa el Login (O sea, estamos en REGISTRO) -->
            <div id="overlay-text-register" class="absolute hidden opacity-0 transition-opacity duration-500">
                <h1 class="text-5xl font-bold text-white mb-4 tracking-tight">¡Bienvenido!</h1>
                <p class="text-lg text-white/90 mb-8">Acceda a precios exclusivos y gestione sus pedidos.</p>
                <p class="text-white mb-4">¿Ya tienes una cuenta?</p>
                <button onclick="slidePanel('left')" type="button" class="border-2 border-white text-white font-bold py-2 px-8 rounded-full hover:bg-white hover:text-primary transition">
                    Iniciar Sesión
                </button>
            </div>
            
        </div>
    </div>
</div>

<script>
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
