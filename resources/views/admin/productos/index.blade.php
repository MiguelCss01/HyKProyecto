@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
<header class="bg-surface-container-lowest border-b border-outline-variant px-margin-desktop py-lg sticky top-0 z-40 flex flex-col gap-lg shadow-[0px_4px_12px_rgba(0,0,0,0.02)]">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Control de Stock</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Gestiona el inventario de productos en almacén.</p>
        </div>
        <div class="flex items-center gap-md">
            <button class="flex items-center gap-2 bg-surface border border-outline-variant text-on-surface-variant font-label-md py-2 px-4 rounded-lg hover:bg-surface-container-low transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[20px]" data-icon="download">download</span>
                Exportar
            </button>
            <button class="flex items-center gap-2 bg-primary-container text-on-primary-container font-label-md py-2 px-4 rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                <span class="material-symbols-outlined text-[20px]" data-icon="add">add</span>
                Nuevo Producto
            </button>
        </div>
    </div>
    
    <!-- Toolbar: Search & Filters -->
    <div class="flex flex-col md:flex-row gap-md items-start md:items-center justify-between bg-surface-bright p-4 rounded-xl border border-outline-variant/50">
        <!-- Search -->
        <div class="relative w-full md:w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" data-icon="search">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder:text-on-surface-variant/70 shadow-sm" placeholder="Buscar producto, SKU o código..." type="text"/>
        </div>
        
        <!-- Filters -->
        <div class="flex items-center gap-sm overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-hide">
            <span class="font-label-md text-label-md text-on-surface-variant whitespace-nowrap mr-2">Categorías:</span>
            <button class="px-4 py-1.5 rounded-full bg-primary-container text-on-primary-container font-label-md text-sm whitespace-nowrap border border-transparent shadow-sm">
                Todos
            </button>
            <button class="px-4 py-1.5 rounded-full bg-surface-container-lowest text-on-surface-variant font-label-md text-sm whitespace-nowrap border border-outline-variant hover:bg-surface-container-low transition-colors">
                Alimentos
            </button>
            <button class="px-4 py-1.5 rounded-full bg-surface-container-lowest text-on-surface-variant font-label-md text-sm whitespace-nowrap border border-outline-variant hover:bg-surface-container-low transition-colors">
                Bebidas
            </button>
        </div>
    </div>
</header>

<!-- Data Table Content -->
<div class="flex-1 overflow-auto p-margin-desktop custom-scrollbar">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-[0px_4px_12px_rgba(0,0,0,0.02)] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-bright border-b border-outline-variant sticky top-0 z-10">
                <tr>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[80px]">Img</th>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Producto</th>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[120px]">Categoría</th>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[220px]">Stock Actual</th>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[150px]">Estado</th>
                    <th class="py-4 px-6 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-[100px] text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/50">
                @forelse($productos as $producto)
                <tr class="hover:bg-surface-container-lowest/50 transition-colors group {{ !$producto->activo ? 'bg-error-container/5' : '' }}">
                    <td class="py-4 px-6 {{ !$producto->activo ? 'opacity-60' : '' }}">
                        <img alt="{{ $producto->nombre_producto }}" class="w-12 h-12 object-contain rounded border border-outline-variant/30 bg-white {{ !$producto->activo ? 'grayscale' : '' }}" src="{{ $producto->imagen_url ?? 'https://via.placeholder.com/150' }}"/>
                    </td>
                    <td class="py-4 px-6 {{ !$producto->activo ? 'opacity-60' : '' }}">
                        <p class="font-label-md text-label-md text-on-surface truncate">{{ $producto->nombre_producto }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">{{ $producto->presentaciones->count() }} presentaciones</p>
                    </td>
                    <td class="py-4 px-6 font-body-sm text-body-sm text-on-surface-variant {{ !$producto->activo ? 'opacity-60' : '' }}">
                        {{ $producto->categoria->nombre_categoria ?? 'Sin Categoría' }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2 bg-surface-bright border border-outline-variant rounded-lg p-1 w-fit">
                            <button aria-label="Decrease stock" class="w-8 h-8 flex items-center justify-center rounded text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors" {{ $producto->stock_actual <= 0 ? 'disabled' : '' }}>
                                <span class="material-symbols-outlined text-[18px]" data-icon="remove">remove</span>
                            </button>
                            <span class="font-price-display text-price-display {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-error' : 'text-on-surface' }} w-12 text-center">
                                {{ $producto->stock_actual }}
                            </span>
                            <button aria-label="Increase stock" class="w-8 h-8 flex items-center justify-center rounded text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined text-[18px]" data-icon="add">add</span>
                            </button>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @if(!$producto->activo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container/30 text-error border border-error-container font-label-md text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Inactivo
                            </span>
                        @elseif($producto->stock_actual <= 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container/30 text-error border border-error-container font-label-md text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Sin Stock
                            </span>
                        @elseif($producto->stock_actual <= $producto->stock_minimo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FFF3CD] text-[#856404] border border-[#FFEEBA] font-label-md text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#856404]"></span> Stock Bajo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-container/20 text-secondary border border-secondary-container font-label-md text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> En Stock
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary-fixed/20 rounded-full transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                            <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-on-surface-variant">
                        No hay productos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
@endsection
