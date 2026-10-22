<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Comprobante de Pago #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }} - HyK Mayorista</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- html2pdf.js para exportación directa a archivo PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .comprobante-sheet {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800 antialiased flex flex-col py-0 sm:py-6">

<!-- Barra de Acciones Superior (No imprimible) -->
<header class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm mb-4">
    <div class="max-w-4xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('pedidos.show', $pedido->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span>Volver al Pedido</span>
            </a>
            <span class="hidden sm:inline-block text-gray-300">|</span>
            <div class="hidden sm:flex items-center gap-1 text-xs text-gray-500">
                <span>Comprobante Oficial de Pago</span>
                <span class="font-bold text-gray-800">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Botón Descargar PDF -->
            <button type="button" onclick="descargarPDF()" id="btn-descargar-pdf" class="inline-flex items-center gap-1.5 bg-[#009ee3] hover:bg-[#007eb5] text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer">
                <span class="material-symbols-outlined text-base">download</span>
                <span>Descargar PDF</span>
            </button>

            <!-- Botón Imprimir -->
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 bg-[#00317e] hover:bg-[#002258] text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Imprimir</span>
            </button>


        </div>
    </div>
</header>

<!-- Contenedor del Comprobante (Hoja Formato A4) -->
<main class="max-w-4xl w-full mx-auto px-2 sm:px-4 flex-1">
    <div id="comprobante-area" class="comprobante-sheet bg-white border border-gray-200 rounded-2xl p-6 sm:p-10 shadow-lg space-y-6">

        <!-- Encabezado del Comprobante -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 border-b border-gray-200 pb-6">
            <!-- Datos de HyK Mayorista -->
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 text-[#00317e] font-black text-2xl tracking-tight">
                    <span class="material-symbols-outlined text-3xl">storefront</span>
                    <span>HyK Mayorista</span>
                </div>
                <p class="text-xs font-semibold text-gray-700">HyK Mayorista S.R.L. &bull; CUIT 30-71489234-9</p>
                <p class="text-xs text-gray-500">IVA Responsable Inscripto</p>
                <p class="text-xs text-gray-500">Sucursal Central: Calle Saavedra 4600 (esq. Av. Circunvalación)</p>
                <p class="text-xs text-gray-500">Ciudad de Formosa, Formosa, Argentina &bull; Tel: +54 370 412-3456</p>
            </div>

            <!-- Datos del Comprobante / Factura de Pago -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:text-right min-w-[240px] space-y-1">
                <div class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Comprobante Oficial de Pago</div>
                <div class="text-lg font-black text-[#00317e] font-mono">
                    COMP-{{ str_pad($pedido->id, 8, '0', STR_PAD_LEFT) }}
                </div>
                <div class="text-xs text-gray-600">
                    Fecha de Emisión: <strong class="text-gray-800">{{ $pedido->created_at ? $pedido->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} hs</strong>
                </div>
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 border border-green-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wide">
                        <span class="material-symbols-outlined text-sm font-bold">verified</span>
                        Pago Acreditado
                    </span>
                </div>
            </div>
        </div>

        <!-- Bloque de 2 Columnas: Datos del Cliente & Datos de Mercado Pago -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Columna Cliente -->
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/50 space-y-2 text-xs">
                <h3 class="font-bold text-gray-900 uppercase tracking-wider text-[11px] pb-1.5 border-b border-gray-200 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-[#00317e]">person</span>
                    Información del Cliente
                </h3>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-500">Nombre / Razón Social:</span>
                    <span class="font-bold text-gray-900">{{ $pedido->user->name }}</span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-500">Correo Electrónico:</span>
                    <span class="font-medium text-gray-800">{{ $pedido->user->email }}</span>
                </div>
                @if(!empty($pedido->user->telefono))
                    <div class="flex justify-between py-0.5">
                        <span class="text-gray-500">Teléfono de Contacto:</span>
                        <span class="font-medium text-gray-800">{{ $pedido->user->telefono }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-500">Tipo de Cliente:</span>
                    <span class="font-bold text-gray-800 uppercase">{{ $pedido->user->tipo_cliente ?? 'Minorista' }}</span>
                </div>
            </div>

            <!-- Columna Mercado Pago -->
            <div class="border border-[#009ee3]/30 rounded-xl p-4 bg-[#009ee3]/5 space-y-2 text-xs">
                <h3 class="font-bold text-[#009ee3] uppercase tracking-wider text-[11px] pb-1.5 border-b border-[#009ee3]/20 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-[#009ee3]">account_balance_wallet</span>
                    Transacción Mercado Pago
                </h3>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-600">Pasarela de Cobro:</span>
                    <span class="font-bold text-[#009ee3]">Mercado Pago Oficial</span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-600">N° Transacción MP:</span>
                    <span class="font-mono font-bold text-gray-900 select-all">
                        {{ $pedido->pago?->id_transaccion_mp ?? ('MP-' . $pedido->id . '-TEST') }}
                    </span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-600">Fecha de Acreditación:</span>
                    <span class="font-semibold text-gray-800">
                        {{ $pedido->pago?->fecha_pago ? \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y') : now()->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="text-gray-600">Estado de la Operación:</span>
                    <span class="font-bold text-green-700">Aprobado / Acreditado</span>
                </div>
            </div>

        </div>

        <!-- Tabla de Artículos del Pedido -->
        <div class="space-y-2">
            <h3 class="font-bold text-gray-900 uppercase tracking-wider text-xs flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-[#00317e]">inventory_2</span>
                Detalle de Productos Facturados
            </h3>

            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold border-b border-gray-200 uppercase text-[10px] tracking-wider">
                            <th class="py-2.5 px-3">#</th>
                            <th class="py-2.5 px-3">Producto</th>
                            <th class="py-2.5 px-3">Presentación</th>
                            <th class="py-2.5 px-3 text-center">Cant.</th>
                            <th class="py-2.5 px-3 text-right">Precio Unit.</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pedido->detalles as $index => $detalle)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2.5 px-3 text-gray-400 font-mono">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">
                                    {{ $detalle->presentacionProducto?->producto?->nombre_producto ?? 'Producto' }}
                                    @if($detalle->presentacionProducto?->producto?->categoria)
                                        <span class="block text-[10px] text-gray-500 font-normal">
                                            Cat: {{ $detalle->presentacionProducto->producto->categoria->nombre_categoria }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-gray-600 uppercase text-[11px]">
                                    {{ $detalle->presentacionProducto?->tipo ?? 'Unidad' }}
                                    (x{{ $detalle->presentacionProducto?->cantidad_contenida ?? 1 }})
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-gray-800">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-medium text-gray-700">
                                    ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-bold text-gray-900">
                                    ${{ number_format($detalle->precio_unitario * $detalle->cantidad, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totales y Resumen Financiero -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-2">
            <!-- Información adicional izquierda -->
            <div class="text-xs text-gray-500 space-y-1">
                <p>Total de Bultos / Artículos: <strong class="text-gray-800 font-bold">{{ $pedido->detalles->sum('cantidad') }} unidades</strong></p>
                <p>Moneda: Pesos Argentinos (ARS) &bull; Condición de Pago: Contado / Débito Acreditado</p>
            </div>

            <!-- Desglose a la derecha -->
            <div class="w-full sm:w-72 bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal Artículos:</span>
                    <span class="font-bold text-gray-800">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Retiro en Sucursal:</span>
                    <span class="font-bold text-green-700">Gratis ($0,00)</span>
                </div>
                <div class="border-t border-gray-300 pt-2 flex justify-between items-baseline">
                    <span class="text-sm font-black text-gray-900 uppercase">Total Abonado:</span>
                    <span class="text-xl font-black text-[#00317e]">
                        ${{ number_format($pedido->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Cuadro Informativo: Retiro en Sucursal Central -->
        <div class="p-4 bg-blue-50/80 border border-blue-200 rounded-xl text-xs space-y-2">
            <div class="flex items-center gap-2 font-bold text-[#00317e] text-xs uppercase tracking-wider">
                <span class="material-symbols-outlined text-base">local_shipping</span>
                <span>Instrucciones para el Retiro de la Mercadería</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                <div>
                    <span class="font-semibold text-gray-900 block">Punto de Entrega:</span>
                    <span>Sucursal Central HyK &bull; Calle Saavedra 4600 (intersección Av. Circunvalación), Formosa.</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-900 block">Horarios de Atención:</span>
                    <span>Lunes a Viernes de 08:00 a 18:00 hs &bull; Sábados de 08:30 a 13:00 hs.</span>
                </div>
            </div>
            <p class="text-[11px] text-blue-900 pt-1 border-t border-blue-200/60 font-medium">
                &bull; Para retirar la compra en depósito deberás presentar tu DNI / CUIT y este comprobante (en formato impreso o en la pantalla de tu teléfono móvil).
            </p>
        </div>

        <!-- Pie del Comprobante: Código de Barras / QR Simulado y Firma -->
        <div class="border-t border-gray-200 pt-4 flex flex-col sm:flex-row items-center justify-between text-gray-400 text-[10px] gap-2">
            <div class="flex items-center gap-2 font-mono">
                <span class="material-symbols-outlined text-lg text-gray-400">qr_code_2</span>
                <span>VALIDACIÓN HYK: MP-AUT-{{ md5($pedido->id . $pedido->total) }}</span>
            </div>
            <span>Este documento constituye un comprobante formal de pago y retiro de mercadería &bull; HyK Mayorista</span>
        </div>

    </div>
</main>

<footer class="no-print py-6 text-center text-xs text-gray-400">
    HyK Mayorista &copy; {{ date('Y') }} &bull; Sistema de Gestión de Pedidos
</footer>

<script>
    function descargarPDF() {
        const btn = document.getElementById('btn-descargar-pdf');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">sync</span> Generando PDF...';
        btn.disabled = true;

        const elemento = document.getElementById('comprobante-area');
        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'Comprobante-Pedido-{{ str_pad($pedido->id, 6, "0", STR_PAD_LEFT) }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(elemento).save().then(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }).catch((err) => {
            console.error('Error al generar PDF:', err);
            // Fallback a impresión nativa
            window.print();
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    // Si se pasa ?print=1 en la URL, disparar automáticamente el diálogo de impresión
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            setTimeout(() => {
                window.print();
            }, 600);
        }
    });
</script>

</body>
</html>
