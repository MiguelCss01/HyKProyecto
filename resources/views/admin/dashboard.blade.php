@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<header class="bg-surface-container-lowest border-b border-outline-variant px-margin-desktop py-lg sticky top-0 z-40 flex flex-col gap-lg shadow-[0px_4px_12px_rgba(0,0,0,0.02)]">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Panel de Administración</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Resumen general del negocio.</p>
        </div>
        <div class="flex items-center gap-md">
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full"></span>
            </button>
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors">
                <span class="material-symbols-outlined">settings</span>
            </button>
        </div>
    </div>
</header>

<div class="flex-1 overflow-auto p-margin-desktop custom-scrollbar">
    <div class="max-w-[1440px] mx-auto space-y-lg">
        <!-- Metrics Section -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
            <!-- Low Stock Alert -->
            <div class="{{ $bajoStock > 0 ? 'bg-error-container border-error/20 text-on-error-container' : 'bg-surface-container-lowest border-outline-variant text-on-surface' }} border rounded-xl p-md shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md uppercase tracking-wider mb-1 {{ $bajoStock > 0 ? 'text-on-error-container' : 'text-on-surface-variant' }}">Productos con bajo stock</h3>
                        <p class="font-headline-lg text-headline-lg {{ $bajoStock > 0 ? 'text-error' : 'text-primary' }}">{{ $bajoStock }}</p>
                    </div>
                    <div class="p-2 {{ $bajoStock > 0 ? 'bg-error/10' : 'bg-surface-variant' }} rounded-full">
                        <span class="material-symbols-outlined {{ $bajoStock > 0 ? 'text-error' : 'text-on-surface-variant' }}">{{ $bajoStock > 0 ? 'warning' : 'inventory_2' }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.productos.index') }}" class="font-label-md text-label-md hover:underline flex items-center gap-1 mt-auto {{ $bajoStock > 0 ? 'text-error' : 'text-primary' }}">
                    Revisar inventario <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Pending Orders -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1">Pedidos pendientes</h3>
                        <p class="font-headline-lg text-headline-lg text-on-surface">{{ $pedidosPendientes }}</p>
                    </div>
                    <div class="p-2 bg-primary-fixed-dim/20 rounded-full">
                        <span class="material-symbols-outlined text-primary">local_shipping</span>
                    </div>
                </div>
                <a href="#" class="font-label-md text-label-md text-primary hover:underline flex items-center gap-1 mt-auto">
                    Ver todos los pedidos <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Today's Revenue -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1">Ingresos Hoy</h3>
                        <p class="font-price-display text-2xl font-bold text-on-surface">${{ number_format($ingresosHoy, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-2 bg-secondary-container rounded-full">
                        <span class="material-symbols-outlined text-on-secondary-container">payments</span>
                    </div>
                </div>
                <div class="w-full bg-surface-container-high rounded-full h-2 mt-auto">
                    <div class="bg-primary h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-gutter mt-8">
            <!-- Últimos Pedidos -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                    <h3 class="font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span> Últimos Pedidos
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-outline-variant/50">
                            @forelse($ultimosPedidos as $pedido)
                                <tr class="hover:bg-surface-container-lowest transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-on-surface">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs text-on-surface-variant">{{ $pedido->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-on-surface-variant">
                                        {{ $pedido->user->name ?? 'Cliente Eliminado' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex bg-primary-fixed text-on-primary-fixed px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $pedido->estado }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-primary">
                                        ${{ number_format($pedido->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-6 text-center text-on-surface-variant text-sm">No hay pedidos recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nuevos Clientes -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 border-b border-outline-variant bg-surface-container-low flex justify-between items-center">
                    <h3 class="font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">group_add</span> Nuevos Clientes
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-outline-variant/50">
                            @forelse($nuevosClientes as $cliente)
                                <tr class="hover:bg-surface-container-lowest transition-colors">
                                    <td class="py-3 px-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-surface-variant flex items-center justify-center text-on-surface-variant font-bold text-xs uppercase shrink-0">
                                            {{ substr($cliente->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-on-surface">{{ $cliente->name }}</div>
                                            <div class="text-xs text-on-surface-variant">{{ $cliente->email }}</div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex bg-surface-container-high text-on-surface px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $cliente->tipo_cliente }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right text-xs text-on-surface-variant">
                                        {{ $cliente->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="p-6 text-center text-on-surface-variant text-sm">No hay clientes nuevos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
