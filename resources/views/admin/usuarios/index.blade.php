@extends('layouts.admin')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="flex-1 overflow-auto p-margin-desktop custom-scrollbar">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="mb-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-headline-md font-bold text-on-surface">Clientes y Usuarios</h2>
                <p class="text-on-surface-variant text-sm mt-1">Administración de clientes registrados en la plataforma.</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant p-4 mb-6 flex flex-wrap gap-4 items-center">
            <span class="text-sm font-bold text-on-surface-variant">Filtrar por tipo:</span>
            <div class="flex rounded-md shadow-sm" role="group">
                <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ !request('tipo') ? 'bg-primary-container text-on-primary-container' : 'bg-white text-gray-900' }}">
                    Todos
                </a>
                <a href="{{ route('admin.usuarios.index', ['tipo' => 'minorista']) }}" class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ request('tipo') == 'minorista' ? 'bg-primary-container text-on-primary-container' : 'bg-white text-gray-900' }}">
                    Minoristas
                </a>
                <a href="{{ route('admin.usuarios.index', ['tipo' => 'mayorista']) }}" class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-primary focus:z-10 focus:ring-2 focus:ring-primary {{ request('tipo') == 'mayorista' ? 'bg-primary-container text-on-primary-container' : 'bg-white text-gray-900' }}">
                    Mayoristas
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-surface-bright rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-on-surface">
                    <thead class="bg-surface-container-low text-on-surface-variant font-label-md border-b border-outline-variant">
                        <tr>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Cliente</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Email</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Tipo</th>
                            <th scope="col" class="py-4 px-6 font-bold uppercase text-[11px] tracking-wider">Fecha Registro</th>
                            <th scope="col" class="py-4 px-6 text-right font-bold uppercase text-[11px] tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        @forelse($usuarios as $user)
                            <tr class="hover:bg-surface-container-lowest transition-colors group">
                                <td class="py-4 px-6 font-medium text-primary">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold text-xs uppercase shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-on-surface-variant">
                                    {{ $user->email }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->tipo_cliente === 'mayorista')
                                        <span class="inline-flex items-center gap-1 bg-primary-fixed text-on-primary-fixed px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-[14px]">store</span>
                                            Mayorista
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-surface-variant text-on-surface-variant px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-[14px]">person</span>
                                            Minorista
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-on-surface-variant text-xs">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar a este cliente? Se borrarán sus datos.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-error hover:bg-error-container hover:text-on-error-container rounded-full transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" title="Eliminar Cliente">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-6 text-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-4xl mb-3 opacity-50 block">group_off</span>
                                    <p class="font-bold">No se encontraron clientes.</p>
                                    <p class="text-sm mt-1">Todavía no hay usuarios registrados con ese filtro.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($usuarios->hasPages())
            <div class="p-4 border-t border-outline-variant bg-surface-container-lowest">
                {{ $usuarios->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
