<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Palabras comunes que se ignoran al extraer términos de búsqueda de productos.
     *
     * @var array<int, string>
     */
    private const STOPWORDS = [
        'hola', 'buenas', 'buenos', 'dias', 'tardes', 'noches', 'quiero', 'quisiera',
        'saber', 'consultar', 'consulta', 'tenes', 'tienen', 'hay', 'precio', 'precios',
        'cuanto', 'cuesta', 'sale', 'salen', 'vale', 'stock', 'disponible', 'disponibles',
        'para', 'con', 'sin', 'por', 'los', 'las', 'una', 'uno', 'unos', 'unas', 'del',
        'que', 'como', 'donde', 'cuando', 'cual', 'cuales', 'quien', 'mas', 'menos',
        'comprar', 'venden', 'vende', 'necesito', 'busco', 'buscar', 'ver', 'mostrar',
        'producto', 'productos', 'articulo', 'articulos', 'catalogo', 'hyk', 'mayorista',
        'minorista', 'unidad', 'unidades', 'pack', 'packs', 'bulto', 'bultos', 'caja', 'cajas',
        'pedido', 'pedidos', 'estado', 'orden', 'compra', 'compras', 'mio', 'mis', 'favor',
    ];

    /**
     * Procesa el mensaje del usuario y retorna la respuesta junto con tarjetas interactivas.
     *
     * @param  array<int, array{role: string, text: string}>  $historial
     * @return array{
     *     reply: string,
     *     products: array<int, array<string, mixed>>,
     *     orders: array<int, array<string, mixed>>,
     *     suggestions: array<int, string>,
     *     source: string
     * }
     */
    public function responder(string $mensaje, ?User $user = null, array $historial = []): array
    {
        $mensajeLimpio = trim($mensaje);
        $isMayorista = $user?->tipo_cliente === 'mayorista';
        $intencion = $this->detectarIntencion($mensajeLimpio);

        $productosEncontrados = collect();
        $pedidosEncontrados = collect();

        // Si la intención involucra catálogo o es una consulta general, buscamos productos relevantes
        if (in_array($intencion, ['catalogo', 'general'], true)) {
            $productosEncontrados = $this->buscarProductosContexto($mensajeLimpio);
        }

        // Si el usuario consulta sobre pedidos y está autenticado, buscamos sus pedidos
        if ($intencion === 'pedidos' || $this->mencionaPedido($mensajeLimpio)) {
            $pedidosEncontrados = $this->buscarPedidosUsuario($user, $mensajeLimpio);
        }

        $tarjetasProductos = $this->formatearTarjetasProductos($productosEncontrados, $isMayorista);
        $tarjetasPedidos = $this->formatearTarjetasPedidos($pedidosEncontrados);

        $apiKey = (string) config('services.gemini.api_key', '');
        $respuestaTexto = null;
        $fuente = 'local';

        if ($apiKey !== '') {
            $respuestaTexto = $this->consultarGemini(
                $mensajeLimpio,
                $user,
                $isMayorista,
                $tarjetasProductos,
                $tarjetasPedidos,
                $historial
            );

            if ($respuestaTexto !== null) {
                $fuente = 'gemini';
            }
        }

        if ($respuestaTexto === null) {
            $respuestaTexto = $this->generarRespuestaLocal(
                $mensajeLimpio,
                $intencion,
                $user,
                $isMayorista,
                $tarjetasProductos,
                $tarjetasPedidos
            );
        }

        return [
            'reply' => $respuestaTexto,
            'products' => $tarjetasProductos,
            'orders' => $tarjetasPedidos,
            'suggestions' => $this->obtenerSugerencias($intencion, $user),
            'source' => $fuente,
        ];
    }

    /**
     * Detecta la intención principal del mensaje del cliente.
     */
    public function detectarIntencion(string $mensaje): string
    {
        $normalizado = $this->normalizarTexto($mensaje);

        if ($this->mencionaPedido($normalizado)) {
            return 'pedidos';
        }

        if (preg_match('/\b(direccion|ubicacion|sucursal|local|retiro|retirar|donde estan|horario|horarios|envio|envios|domicilio|calle|saavedra|formosa)\b/u', $normalizado)) {
            return 'sucursal';
        }

        if (preg_match('/\b(mercado ?pago|pagar|pago|pagos|tarjeta|transferencia|efectivo|comprobante|factura)\b/u', $normalizado)) {
            return 'pagos';
        }

        if (preg_match('/\b(mayorista|minorista|cuit|registr|cuenta|diferencia|requisito)\b/u', $normalizado) && ! preg_match('/\b(gaseosa|galletita|limpiador|chocolate|agua|bebida|alimento|limpieza|arcor|cola)\b/u', $normalizado)) {
            return 'mayorista_info';
        }

        if (preg_match('/\b(whatsapp|telefono|contacto|llamar|asesor|humano|atencion|vendedor)\b/u', $normalizado)) {
            return 'contacto';
        }

        if (preg_match('/^(hola|buenas|buen dia|buenos dias|buenas tardes|buenas noches|que tal|hey)\b/u', $normalizado) && str_word_count($normalizado) <= 4) {
            return 'saludo';
        }

        if (preg_match('/\b(receta|cocinar|politica|futbol|partido|clima|chiste|programar|python|javascript|poema|historia)\b/u', $normalizado)) {
            return 'fuera_contexto';
        }

        return 'catalogo';
    }

    /**
     * Determina si el mensaje hace referencia a un pedido o seguimiento de orden.
     */
    private function mencionaPedido(string $texto): bool
    {
        $normalizado = $this->normalizarTexto($texto);

        return (bool) preg_match('/\b(pedido|pedidos|orden|ordenes|seguimiento|estado de mi compra|mis compras)\b|#\s*\d+/u', $normalizado);
    }

    /**
     * Busca productos activos en el catálogo relacionados con la consulta del usuario.
     *
     * @return Collection<int, Producto>
     */
    public function buscarProductosContexto(string $mensaje): Collection
    {
        $normalizado = $this->normalizarTexto($mensaje);
        $palabras = $this->extraerPalabrasClave($normalizado);

        $queryBase = Producto::with(['categoria', 'presentaciones'])
            ->where('activo', true);

        // Verificar si menciona alguna categoría explícitamente
        $categorias = Categoria::all();
        $categoriaDetectada = null;
        foreach ($categorias as $cat) {
            $nombreCatNorm = $this->normalizarTexto($cat->nombre_categoria);
            $singularCat = rtrim($nombreCatNorm, 's');
            if ($singularCat !== '' && str_contains($normalizado, $singularCat)) {
                $categoriaDetectada = $cat;
                break;
            }
        }

        if (! empty($palabras)) {
            $productos = (clone $queryBase)
                ->where(function ($q) use ($palabras, $categoriaDetectada) {
                    foreach ($palabras as $palabra) {
                        $raiz = mb_strlen($palabra) > 4 ? rtrim($palabra, 'es') : $palabra;
                        $raiz = mb_strlen($raiz) > 3 ? rtrim($raiz, 's') : $palabra;

                        $q->orWhere('nombre_producto', 'like', "%{$palabra}%")
                            ->orWhere('descripcion_producto', 'like', "%{$palabra}%")
                            ->orWhere('nombre_producto', 'like', "%{$raiz}%")
                            ->orWhereHas('categoria', function ($cq) use ($raiz) {
                                $cq->where('nombre_categoria', 'like', "%{$raiz}%");
                            });
                    }

                    if ($categoriaDetectada) {
                        $q->orWhere('categoria_id', $categoriaDetectada->id);
                    }
                })
                ->orderByDesc('stock_actual')
                ->limit(4)
                ->get();

            if ($productos->isNotEmpty()) {
                return $productos;
            }
        }

        if ($categoriaDetectada) {
            return (clone $queryBase)
                ->where('categoria_id', $categoriaDetectada->id)
                ->orderByDesc('stock_actual')
                ->limit(4)
                ->get();
        }

        // Si pregunta en general qué vendemos, catálogo, ofertas o packs, devolver destacados con stock
        if (preg_match('/\b(catalogo|productos|que tenes|que tienen|que venden|oferta|ofertas|pack|packs|bulto|bultos|caja|cajas|precio|precios|stock|recomienda|recomendame)\b/u', $normalizado)) {
            return (clone $queryBase)
                ->where('stock_actual', '>', 0)
                ->orderByDesc('stock_actual')
                ->limit(4)
                ->get();
        }

        return collect();
    }

    /**
     * Busca pedidos pertenecientes exclusivamente al usuario autenticado.
     *
     * @return Collection<int, Pedido>
     */
    public function buscarPedidosUsuario(?User $user, string $mensaje): Collection
    {
        if (! $user) {
            return collect();
        }

        $query = Pedido::with(['detalles.presentacionProducto.producto', 'pago'])
            ->where('user_id', $user->id);

        // Si el usuario menciona un número específico (ej. #15 o pedido 15)
        if (preg_match('/(?:#|pedido\s+|orden\s+|nro\s+|numero\s+)(\d+)/iu', $mensaje, $matches)) {
            $pedidoId = (int) $matches[1];
            $especifico = (clone $query)->where('id', $pedidoId)->get();

            if ($especifico->isNotEmpty()) {
                return $especifico;
            }
        }

        return $query->orderByDesc('id')->limit(3)->get();
    }

    /**
     * Transforma los productos de Eloquent en un arreglo estructurado con precios según el tipo de cliente.
     *
     * @param  Collection<int, Producto>  $productos
     * @return array<int, array<string, mixed>>
     */
    private function formatearTarjetasProductos(Collection $productos, bool $isMayorista): array
    {
        $resultado = [];

        foreach ($productos as $producto) {
            $presentaciones = [];
            foreach ($producto->presentaciones as $pres) {
                $precioAplicado = $isMayorista
                    ? (float) $pres->precio_mayorista
                    : (float) $pres->precio_minorista;

                $stockMaxPres = $pres->cantidad_contenida > 0
                    ? intdiv((int) $producto->stock_actual, (int) $pres->cantidad_contenida)
                    : 0;

                $presentaciones[] = [
                    'id' => $pres->id,
                    'tipo' => ucfirst($pres->tipo),
                    'cantidad_contenida' => (int) $pres->cantidad_contenida,
                    'precio' => $precioAplicado,
                    'precio_formateado' => '$'.number_format($precioAplicado, 0, ',', '.'),
                    'precio_minorista' => (float) $pres->precio_minorista,
                    'precio_mayorista' => (float) $pres->precio_mayorista,
                    'stock_disponible' => $stockMaxPres,
                ];
            }

            if (empty($presentaciones)) {
                continue;
            }

            // Seleccionar presentación sugerida por defecto (pack/bulto/caja para mayorista, unidad para minorista)
            $presentacionDefault = $presentaciones[0];
            foreach ($presentaciones as $presItem) {
                if ($isMayorista && strtolower($presItem['tipo']) !== 'unidad') {
                    $presentacionDefault = $presItem;
                    break;
                }
                if (! $isMayorista && strtolower($presItem['tipo']) === 'unidad') {
                    $presentacionDefault = $presItem;
                    break;
                }
            }

            $resultado[] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre_producto,
                'descripcion' => $producto->descripcion_producto,
                'categoria' => $producto->categoria?->nombre_categoria ?? 'General',
                'imagen_url' => $producto->imagen_url,
                'stock_actual' => (int) $producto->stock_actual,
                'tipo_precio' => $isMayorista ? 'Mayorista' : 'Minorista',
                'presentacion_default' => $presentacionDefault,
                'presentaciones' => $presentaciones,
            ];
        }

        return $resultado;
    }

    /**
     * Transforma los pedidos del usuario en tarjetas con enlaces de acción.
     *
     * @param  Collection<int, Pedido>  $pedidos
     * @return array<int, array<string, mixed>>
     */
    private function formatearTarjetasPedidos(Collection $pedidos): array
    {
        $resultado = [];

        foreach ($pedidos as $pedido) {
            $cantidadItems = (int) $pedido->detalles->sum('cantidad');

            $resultado[] = [
                'id' => $pedido->id,
                'fecha' => $pedido->fecha_pedido
                    ? $pedido->fecha_pedido->format('d/m/Y')
                    : ($pedido->created_at ? $pedido->created_at->format('d/m/Y') : '-'),
                'estado' => (string) $pedido->estado,
                'total' => (float) $pedido->total,
                'total_formateado' => '$'.number_format((float) $pedido->total, 0, ',', '.'),
                'items_count' => $cantidadItems,
                'pagado' => $pedido->estado === 'PAGADO' || $pedido->pago !== null,
                'url_detalle' => route('pedidos.show', $pedido->id),
                'url_pago' => $pedido->estado === 'PENDIENTE' ? route('pedidos.pago', $pedido->id) : null,
            ];
        }

        return $resultado;
    }

    /**
     * Consulta la API de Google Gemini inyectando el contexto real del sistema.
     *
     * @param  array<int, array<string, mixed>>  $tarjetasProductos
     * @param  array<int, array<string, mixed>>  $tarjetasPedidos
     * @param  array<int, array{role: string, text: string}>  $historial
     */
    private function consultarGemini(
        string $mensaje,
        ?User $user,
        bool $isMayorista,
        array $tarjetasProductos,
        array $tarjetasPedidos,
        array $historial
    ): ?string {
        $apiKey = trim((string) config('services.gemini.api_key', ''));
        $primaryModel = trim((string) config('services.gemini.model', 'gemini-3.5-flash'));

        if ($apiKey === '') {
            return null;
        }

        $systemInstruction = $this->construirSystemPrompt($user, $isMayorista, $tarjetasProductos, $tarjetasPedidos);

        $contents = [];
        foreach (array_slice($historial, -8) as $msg) {
            $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $text = trim((string) ($msg['text'] ?? ''));
            if ($text !== '') {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]],
                ];
            }
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $mensaje]],
        ];

        $modelosCandidatos = array_values(array_unique([
            $primaryModel,
            'gemini-3.5-flash-lite',
            'gemini-3-flash-preview',
        ]));

        foreach ($modelosCandidatos as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            try {
                $httpClient = Http::acceptJson()->timeout(12);

                if (app()->environment('local', 'testing')) {
                    $httpClient = $httpClient->withoutVerifying();
                }

                $response = $httpClient->post($endpoint, [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 500,
                    ],
                ]);

                if ($response->successful()) {
                    $texto = data_get($response->json(), 'candidates.0.content.parts.0.text');
                    if (is_string($texto) && trim($texto) !== '') {
                        return trim($texto);
                    }
                } else {
                    Log::warning("Error de respuesta en Gemini API ({$model})", [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                }
            } catch (Exception $e) {
                Log::warning("Excepción al conectar con Gemini API ({$model})", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Construye el System Prompt con reglas de negocio de HyK Mayorista y datos en tiempo real.
     *
     * @param  array<int, array<string, mixed>>  $tarjetasProductos
     * @param  array<int, array<string, mixed>>  $tarjetasPedidos
     */
    private function construirSystemPrompt(
        ?User $user,
        bool $isMayorista,
        array $tarjetasProductos,
        array $tarjetasPedidos
    ): string {
        $direccion = config('services.hyk.direccion');
        $horarios = config('services.hyk.horarios');
        $perfilTexto = $user
            ? "Autenticado como {$user->name} (Condición comercial: ".strtoupper($user->tipo_cliente ?? 'minorista').')'
            : 'Invitado (no inició sesión aún; se le muestran precios MINORISTAS por defecto)';

        $contextoProductos = 'No se encontraron productos específicos para esta consulta puntual.';
        if (! empty($tarjetasProductos)) {
            $lineasProd = [];
            foreach ($tarjetasProductos as $p) {
                $presTexto = collect($p['presentaciones'])->map(function (array $pr) {
                    return "{$pr['tipo']} (x{$pr['cantidad_contenida']} u.): {$pr['precio_formateado']} (Minorista: \${$pr['precio_minorista']} / Mayorista: \${$pr['precio_mayorista']})";
                })->implode(' | ');

                $lineasProd[] = "- {$p['nombre']} [Categoría: {$p['categoria']}] | Stock físico total: {$p['stock_actual']} u. | Presentaciones: {$presTexto}";
            }
            $contextoProductos = implode("\n", $lineasProd);
        }

        $contextoPedidos = $user
            ? 'El usuario no registra pedidos recientes con ese criterio.'
            : 'El usuario NO ha iniciado sesión. Para ver el estado de sus pedidos debe ingresar con su cuenta en /acceso.';

        if (! empty($tarjetasPedidos)) {
            $lineasPed = [];
            foreach ($tarjetasPedidos as $ped) {
                $pagoStr = $ped['pagado'] ? 'Pago Acreditado (Mercado Pago)' : 'Pendiente de Pago';
                $lineasPed[] = "- Pedido #{$ped['id']} | Fecha: {$ped['fecha']} | Estado: {$ped['estado']} | Total: {$ped['total_formateado']} ({$ped['items_count']} artículos) | Pago: {$pagoStr}";
            }
            $contextoPedidos = implode("\n", $lineasPed);
        }

        return <<<PROMPT
Eres el Asistente Virtual Oficial de "HyK Mayorista", un portal de compras mayorista y minorista de alimentos, bebidas y artículos de limpieza en la Ciudad de Formosa.
Tu objetivo es ayudar a los clientes de forma clara, amable, concisa y directa en español rioplatense cordial.

DATOS DEL CLIENTE ACTUAL:
- {$perfilTexto}
- Lista de precios activa para este usuario: {$this->etiquetaPrecio($isMayorista)}

INFORMACIÓN OFICIAL DE HYK MAYORISTA:
- Sucursal de retiro: {$direccion}.
- Horarios de atención: {$horarios}.
- Modalidad de entrega: Retiro en sucursal (100% gratis, presentando DNI o CUIT una vez confirmado/pagado el pedido).
- Medios de pago: Mercado Pago (dinero en cuenta, tarjetas de débito y crédito) con acreditación instantánea y comprobante PDF oficial.
- Diferencia Mayorista vs Minorista: Los clientes registrados con CUIT y Razón Social como Mayoristas acceden a precios diferenciales más económicos, especialmente en presentaciones por pack, caja cerrada o bulto.

REGLAS DE CLASIFICACIÓN Y RESPUESTA:
1. CASO CATÁLOGO, PRECIOS Y STOCK:
   - Usa EXCLUSIVAMENTE los datos de "CONTEXTO DE PRODUCTOS EN BASE DE DATOS". NUNCA inventes productos ni precios.
   - Informa el precio que corresponde al perfil actual del usuario ({$this->etiquetaPrecio($isMayorista)}), mencionando que también tiene presentaciones por unidad, pack, caja o bulto.
   - Invita al usuario a usar el botón "+ Agregar" que aparece debajo de tu mensaje para sumarlo directamente al carrito.
2. CASO ESTADO DE PEDIDOS:
   - Si el usuario no inició sesión, indícale amablemente que debe ingresar a su cuenta para consultar sus pedidos.
   - Si inició sesión, usa "CONTEXTO DE PEDIDOS DEL CLIENTE" para informarle el estado exacto (#ID, estado, total y si está pagado o pendiente de pago).
3. CASO FUERA DE CONTEXTO:
   - Si preguntan sobre temas ajenos a HyK Mayorista (recetas, programación, política, deportes, etc.), responde con amabilidad que eres el asistente exclusivo de HyK Mayorista y ofrece ayuda con el catálogo, precios o estado de pedidos.
4. FORMATO:
   - Sé breve (máximo 2 o 3 párrafos cortos). No uses encabezados con '#' ni tablas complejas.

--- CONTEXTO DE PRODUCTOS EN BASE DE DATOS ---
{$contextoProductos}

--- CONTEXTO DE PEDIDOS DEL CLIENTE ---
{$contextoPedidos}
PROMPT;
    }

    /**
     * Motor determinístico local de respaldo cuando no hay GEMINI_API_KEY o falla la red.
     *
     * @param  array<int, array<string, mixed>>  $tarjetasProductos
     * @param  array<int, array<string, mixed>>  $tarjetasPedidos
     */
    private function generarRespuestaLocal(
        string $mensaje,
        string $intencion,
        ?User $user,
        bool $isMayorista,
        array $tarjetasProductos,
        array $tarjetasPedidos
    ): string {
        $nombreUsuario = $user ? explode(' ', trim($user->name))[0] : null;
        $saludo = $nombreUsuario ? "¡Hola {$nombreUsuario}!" : '¡Hola!';
        $tipoLista = $isMayorista ? 'Mayorista' : 'Minorista';

        if ($intencion === 'fuera_contexto') {
            return 'Soy el Asistente Virtual de HyK Mayorista. Solo puedo ayudarte con consultas sobre nuestro catálogo de productos, precios mayoristas/minoristas, pagos con Mercado Pago o el estado de tus pedidos. ¿En qué te puedo ayudar hoy?';
        }

        if ($intencion === 'saludo') {
            return "{$saludo} Soy el asistente virtual de HyK Mayorista. Tenés activa la lista de precios {$tipoLista}. Podés preguntarme por stock y precios de productos (por unidad, pack o bulto), consultar el estado de tus pedidos o información de retiro en nuestra sucursal.";
        }

        if ($intencion === 'pedidos') {
            if (! $user) {
                return 'Para consultar el estado de tus pedidos por razones de seguridad necesitás iniciar sesión en tu cuenta desde el botón "Ingresar / Registrarse". Una vez adentro, podés preguntarme por el número de tu pedido o verlo en la sección "Mis Pedidos".';
            }

            if (empty($tarjetasPedidos)) {
                return "{$saludo} Revisé tu cuenta y todavía no registrás pedidos realizados (o el número de pedido consultado no pertenece a tu cuenta). Podés armar tu carrito desde el catálogo y abonar con Mercado Pago.";
            }

            $ultimo = $tarjetasPedidos[0];
            $estadoPago = $ultimo['pagado'] ? 'con pago acreditado' : 'pendiente de pago';

            return "{$saludo} Encontré tu Pedido #{$ultimo['id']} del {$ultimo['fecha']} por un total de {$ultimo['total_formateado']}. Actualmente figura en estado {$ultimo['estado']} ({$estadoPago}). Abajo te dejo el acceso directo para ver el detalle o descargar tu comprobante.";
        }

        if ($intencion === 'sucursal') {
            $direccion = config('services.hyk.direccion');
            $horarios = config('services.hyk.horarios');

            return "Nuestra modalidad de entrega es Retiro en Sucursal sin costo. Una vez que tu pedido está pagado y preparado, podés retirarlo con tu DNI o CUIT en {$direccion}.\n\nNuestros horarios de atención son: {$horarios}.";
        }

        if ($intencion === 'pagos') {
            return 'En HyK Mayorista procesamos todos los pagos online de forma segura a través de Mercado Pago (dinero en cuenta, tarjeta de débito o crédito). Una vez confirmado el pago, el pedido pasa automáticamente a estado PAGADO y podés descargar tu comprobante oficial en PDF.';
        }

        if ($intencion === 'mayorista_info') {
            return 'En HyK Mayorista manejamos precios diferenciados: los clientes Minoristas compran con DNI principalmente por unidad, mientras que los clientes Mayoristas (registrados con CUIT y Razón Social) acceden a descuentos especiales en todas las presentaciones, especialmente en packs, cajas cerradas y bultos.';
        }

        if ($intencion === 'contacto') {
            return 'Podés comunicarte directamente con nuestro equipo de atención comercial por WhatsApp tocando el botón verde de "WhatsApp" arriba en este chat, o acercarte a nuestra sucursal en Calle Saavedra 4600, Formosa.';
        }

        if (! empty($tarjetasProductos)) {
            $lineas = [];
            foreach ($tarjetasProductos as $prod) {
                $pres = collect($prod['presentaciones'])->map(function (array $pr) {
                    return "{$pr['tipo']} x{$pr['cantidad_contenida']} a {$pr['precio_formateado']}";
                })->implode(' | ');
                $lineas[] = "• {$prod['nombre']} (Stock: {$prod['stock_actual']} u.) — {$pres}";
            }

            $listado = implode("\n", $lineas);

            return "{$saludo} Estos son los productos disponibles en catálogo con tus precios de lista {$tipoLista}:\n\n{$listado}\n\nPodés elegir la presentación y agregarlos directo a tu carrito desde las tarjetas de abajo.";
        }

        return 'No encontré un producto exacto con ese nombre en nuestro catálogo actual. Trabajamos las categorías de Alimentos, Bebidas y Limpieza (por unidad, pack, caja y bulto). ¿Querés que te muestre las opciones disponibles de alguna de esas categorías?';
    }

    /**
     * Devuelve sugerencias rápidas dinámicas según el contexto y el usuario.
     *
     * @return array<int, string>
     */
    public function obtenerSugerencias(string $intencion, ?User $user): array
    {
        if ($user) {
            return [
                '📦 Estado de mi último pedido',
                '🥤 Precios de Bebidas y Packs',
                '🍪 Alimentos por bulto o unidad',
                '🧹 Artículos de Limpieza',
                '📍 Dirección y horarios de retiro',
            ];
        }

        return [
            '🥤 Precios de Bebidas y Packs',
            '🍪 Alimentos por bulto o unidad',
            '🧹 Artículos de Limpieza',
            '💼 ¿Cómo funciona la cuenta Mayorista?',
            '📍 Dirección y horarios de retiro',
        ];
    }

    /**
     * Extrae palabras significativas del texto del usuario ignorando stopwords.
     *
     * @return array<int, string>
     */
    private function extraerPalabrasClave(string $textoNormalizado): array
    {
        $partes = preg_split('/[^a-z0-9ñ]+/u', $textoNormalizado) ?: [];
        $claves = [];

        foreach ($partes as $palabra) {
            $palabra = trim($palabra);
            if (mb_strlen($palabra) >= 3 && ! in_array($palabra, self::STOPWORDS, true)) {
                $claves[] = $palabra;
            }
        }

        return array_values(array_unique($claves));
    }

    /**
     * Normaliza el texto a minúsculas y sin tildes para facilitar comparaciones.
     */
    private function normalizarTexto(string $texto): string
    {
        $minusculas = mb_strtolower($texto, 'UTF-8');

        return strtr($minusculas, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
        ]);
    }

    /**
     * Retorna el nombre legible de la lista de precios aplicable.
     */
    private function etiquetaPrecio(bool $isMayorista): string
    {
        return $isMayorista ? 'MAYORISTA' : 'MINORISTA';
    }
}
