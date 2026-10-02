@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')

@section('content')
<div class="flex-1 overflow-auto p-margin-desktop custom-scrollbar">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="mb-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-headline-md font-bold text-on-surface">Pedidos Entrantes</h2>
                <p class="text-on-surface-variant text-sm mt-1">Administración y seguimiento de los pedidos realizados por los clientes.</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant p-4 mb-6 flex flex-wrap gap-4 items-center">
            <span class="text-sm font-bold text-on-surface-variant">Filtrar por estado:</span>
            <div class="flex rounded-md shadow-sm overflow-hidden" role="group">
                <a href="{{ route('admin.pedidos.index') }}" class="px-4 py-2 text-sm font-medium border border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ !request('estado') ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-white text-gray-900' }}">
                    Todos
                </a>
                <a href="{{ route('admin.pedidos.index', ['estado' => 'pendiente']) }}" class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ request('estado') == 'pendiente' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-white text-gray-900' }}">
                    Pendientes
                </a>
                <a href="{{ route('admin.pedidos.index', ['estado' => 'aprobado']) }}" class="px-4 py-2 text-sm font-medium border-t border-b border-r border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ request('estado') == 'aprobado' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-white text-gray-900' }}">
                    Aprobados / Pagados
                </a>
                <a href="{{ route('admin.pedidos.index', ['estado' => 'entregado']) }}" class="px-4 py-2 text-sm font-medium border-t border-b border-r border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ request('estado') == 'entregado' ? 'bg-primary-container text-on-primary-container font-bold' : 'bg-white text-gray-900' }}">
                    Completados
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-on-surface">
                    <thead class="bg-surface-container-low text-on-surface-variant font-label-md border-b border-outline-variant">
                        <tr>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">ID Pedido</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Fecha</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Cliente</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Total</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Estado</th>
                            <th scope="col" class="py-4 px-6 text-right font-bold uppercase text-[11px] tracking-wider">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        @forelse($pedidos as $pedido)
                            <tr class="hover:bg-surface-container-lowest transition-colors group">
                                <td class="py-4 px-6 font-bold text-on-surface">
                                    #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-4 px-6 text-on-surface-variant text-xs">
                                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-6 text-on-surface-variant font-medium">
                                    {{ $pedido->user->name ?? 'Cliente Eliminado' }}
                                    <div class="text-[11px] text-outline opacity-80">{{ $pedido->user->email ?? '' }}</div>
                                </td>
                                <td class="py-4 px-6 font-bold text-primary">
                                    ${{ number_format($pedido->total, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $color = 'bg-surface-variant text-on-surface-variant';
                                        if ($pedido->estado == 'pendiente') $color = 'bg-error-container text-on-error-container';
                                        if (in_array($pedido->estado, ['aprobado', 'en_preparacion', 'listo_retiro'])) $color = 'bg-primary-fixed text-on-primary-fixed';
                                        if ($pedido->estado == 'entregado') $color = 'bg-secondary-container text-on-secondary-container';
                                        if (in_array($pedido->estado, ['rechazado', 'cancelado'])) $color = 'bg-error text-on-error';
                                    @endphp
                                    <span class="inline-flex {{ $color }} px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                        {{ str_replace('_', ' ', $pedido->estado) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-container text-on-primary-container rounded font-bold text-xs hover:bg-primary hover:text-white transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-6 text-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-4xl mb-3 opacity-50 block">receipt_long</span>
                                    <p class="font-bold">No se encontraron pedidos.</p>
                                    <p class="text-sm mt-1">Intente cambiar los filtros de búsqueda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($pedidos->hasPages())
            <div class="p-4 border-t border-outline-variant bg-surface-container-lowest">
                {{ $pedidos->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
