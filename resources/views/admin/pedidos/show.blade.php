@extends('layouts.admin')

@section('title', 'Detalle del Pedido #' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="flex-1 overflow-auto p-margin-desktop custom-scrollbar">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header con botón Volver -->
        <div class="mb-lg flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-headline-md font-bold text-on-surface">
                    Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="text-on-surface-variant text-sm mt-1">Registrado el {{ $pedido->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
            </div>
            <a href="{{ route('admin.pedidos.index') }}" class="text-primary hover:text-primary-container flex items-center gap-1 font-bold text-sm">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Volver
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Tarjeta Cliente -->
            <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant p-6">
                <h3 class="font-label-md uppercase text-on-surface-variant tracking-wider text-xs font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">person</span> Datos del Cliente
                </h3>
                @if($pedido->user)
                    <p class="font-bold text-on-surface text-lg">{{ $pedido->user->name }}</p>
                    <p class="text-on-surface-variant text-sm mt-1">{{ $pedido->user->email }}</p>
                    <div class="mt-4">
                        <span class="inline-flex bg-surface-container-high text-on-surface px-2 py-0.5 rounded text-[11px] font-bold uppercase">{{ $pedido->user->tipo_cliente }}</span>
                    </div>
                @else
                    <p class="text-error italic">Usuario eliminado del sistema.</p>
                @endif
            </div>

            <!-- Tarjeta Estado (Formulario) -->
            <div class="md:col-span-2 bg-surface-bright rounded-xl shadow-sm border border-outline-variant p-6">
                <h3 class="font-label-md uppercase text-on-surface-variant tracking-wider text-xs font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">update</span> Actualizar Estado
                </h3>
                
                <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST" class="flex flex-col sm:flex-row items-end gap-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="w-full">
                        <label class="block text-xs text-on-surface-variant font-bold mb-2">Estado actual del pedido</label>
                        <select name="estado" class="w-full border-outline-variant rounded-md shadow-sm focus:border-primary focus:ring-primary text-sm font-medium">
                            <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente (Esperando Pago)</option>
                            <option value="aprobado" {{ $pedido->estado == 'aprobado' ? 'selected' : '' }}>Aprobado / Pagado</option>
                            <option value="rechazado" {{ $pedido->estado == 'rechazado' ? 'selected' : '' }}>Pago Rechazado</option>
                            <option value="en_preparacion" {{ $pedido->estado == 'en_preparacion' ? 'selected' : '' }}>En Preparación</option>
                            <option value="listo_retiro" {{ $pedido->estado == 'listo_retiro' ? 'selected' : '' }}>Listo para Retirar</option>
                            <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>Completado / Entregado</option>
                            <option value="cancelado" {{ $pedido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-primary text-on-primary font-bold py-2 px-6 rounded-md shadow-sm hover:bg-primary-container hover:text-on-primary-container transition-colors shrink-0">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>

        <!-- Tarjeta de Detalles del Pedido -->
        <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="p-6 border-b border-outline-variant bg-surface-container-low">
                <h3 class="font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">shopping_bag</span> Productos Solicitados
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-on-surface">
                    <thead class="bg-surface-container-lowest text-on-surface-variant border-b border-outline-variant/50">
                        <tr>
                            <th scope="col" class="py-3 px-6 font-bold uppercase text-[11px] tracking-wider">Producto</th>
                            <th scope="col" class="py-3 px-6 font-bold uppercase text-[11px] tracking-wider">Presentación</th>
                            <th scope="col" class="py-3 px-6 font-bold uppercase text-[11px] tracking-wider text-center">Cant.</th>
                            <th scope="col" class="py-3 px-6 font-bold uppercase text-[11px] tracking-wider text-right">Precio Unit.</th>
                            <th scope="col" class="py-3 px-6 font-bold uppercase text-[11px] tracking-wider text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @foreach($pedido->detalles as $detalle)
                            <tr class="hover:bg-surface-container-lowest transition-colors">
                                <td class="py-4 px-6 font-medium text-on-surface">
                                    {{ $detalle->presentacionProducto->producto->nombre_producto ?? 'Producto Eliminado' }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex bg-surface-variant text-on-surface-variant px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        {{ $detalle->presentacionProducto->tipo ?? 'N/A' }} 
                                        (x{{ $detalle->presentacionProducto->cantidad_contenida ?? '?' }})
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-bold">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="py-4 px-6 text-right text-on-surface-variant">
                                    ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-primary">
                                    ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-surface-container-lowest border-t border-outline-variant">
                        <tr>
                            <td colspan="4" class="py-4 px-6 text-right font-bold text-on-surface uppercase text-xs tracking-wider">
                                Total del Pedido:
                            </td>
                            <td class="py-4 px-6 text-right font-price-display font-bold text-xl text-primary">
                                ${{ number_format($pedido->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
