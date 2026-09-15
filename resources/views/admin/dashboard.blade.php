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
            <div class="bg-error-container border border-error/20 rounded-xl p-md shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md text-on-error-container uppercase tracking-wider mb-1">Productos con bajo stock</h3>
                        <p class="font-headline-lg text-headline-lg text-on-error-container">24</p>
                    </div>
                    <div class="p-2 bg-error/10 rounded-full">
                        <span class="material-symbols-outlined text-error">warning</span>
                    </div>
                </div>
                <a href="{{ route('admin.productos.index') }}" class="font-label-md text-label-md text-error hover:underline flex items-center gap-1 mt-auto">
                    Revisar inventario <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <!-- Pending Orders -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1">Pedidos pendientes</h3>
                        <p class="font-headline-lg text-headline-lg text-on-surface">156</p>
                    </div>
                    <div class="p-2 bg-primary-fixed-dim/20 rounded-full">
                        <span class="material-symbols-outlined text-primary">local_shipping</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-auto">
                    <span class="text-secondary font-label-md text-label-md flex items-center"><span class="material-symbols-outlined text-sm">trending_up</span> +12%</span>
                    <span class="text-on-surface-variant font-body-sm text-body-sm">vs semana pasada</span>
                </div>
            </div>

            <!-- Today's Revenue -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md shadow-sm flex flex-col justify-between hidden lg:flex">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1">Ingresos Hoy</h3>
                        <p class="font-price-display text-price-display text-on-surface">$1.24M</p>
                    </div>
                    <div class="p-2 bg-secondary-container rounded-full">
                        <span class="material-symbols-outlined text-on-secondary-container">payments</span>
                    </div>
                </div>
                <div class="w-full bg-surface-container-high rounded-full h-2 mt-auto">
                    <div class="bg-primary h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </section>

        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden p-8 flex flex-col items-center justify-center min-h-[300px]">
            <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">construction</span>
            <h3 class="font-headline-md text-on-surface">Panel en construcción</h3>
            <p class="font-body-md text-on-surface-variant mt-2">Aquí irán las tablas o resúmenes principales.</p>
        </section>
    </div>
</div>
@endsection
