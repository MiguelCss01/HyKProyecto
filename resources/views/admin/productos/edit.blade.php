@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
<div class="flex-1 overflow-auto custom-scrollbar">
    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Producto: {{ $producto->nombre_producto }}</h2>
        </div>
        <a href="{{ route('admin.productos.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 font-medium">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <ul class="ml-6 list-disc text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.productos.update', $producto->id) }}" method="POST" class="bg-white shadow rounded-lg overflow-hidden">
        @csrf
        @method('PUT')

        <!-- SECCIÓN 1: Datos Básicos -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">inventory_2</span>
                1. Información Básica
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre_producto" value="{{ old('nombre_producto', $producto->nombre_producto) }}" required class="w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select name="categoria_id" required class="w-full border-gray-300 rounded-md">
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion_producto" rows="3" class="w-full border-gray-300 rounded-md">{{ old('descripcion_producto', $producto->descripcion_producto) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual</label>
                    <input type="number" name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual) }}" min="0" required class="w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" min="0" required class="w-full border-gray-300 rounded-md">
                </div>
                <div class="md:col-span-2 flex items-center mt-2">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $producto->activo) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900 font-medium">Producto Activo</label>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: Presentaciones -->
        <div class="p-6 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">sell</span>
                    2. Presentaciones
                </h3>
                <button type="button" id="btn-add-presentation" class="text-sm bg-green-100 text-green-700 hover:bg-green-200 font-semibold py-1 px-3 rounded flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Agregar Variante
                </button>
            </div>

            <div id="presentaciones-container" class="space-y-4">
                @foreach(old('presentaciones', $producto->presentaciones) as $index => $pres)
                <div class="presentacion-row bg-white p-4 border border-gray-200 rounded-md shadow-sm relative group {{ $index > 0 ? 'mt-4' : '' }}">
                    @if($index > 0)
                        <button type="button" class="btn-remove absolute -top-3 -right-3 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-200 opacity-0 group-hover:opacity-100 shadow border border-red-200">
                            <span class="material-symbols-outlined" style="font-size: 14px;">close</span>
                        </button>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Tipo</label>
                            <select name="presentaciones[{{ $index }}][tipo]" class="w-full text-sm border-gray-300 rounded-md" required>
                                <option value="unidad" {{ (isset($pres['tipo']) ? $pres['tipo'] : $pres->tipo) == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                <option value="caja" {{ (isset($pres['tipo']) ? $pres['tipo'] : $pres->tipo) == 'caja' ? 'selected' : '' }}>Caja</option>
                                <option value="pack" {{ (isset($pres['tipo']) ? $pres['tipo'] : $pres->tipo) == 'pack' ? 'selected' : '' }}>Pack</option>
                                <option value="bulto" {{ (isset($pres['tipo']) ? $pres['tipo'] : $pres->tipo) == 'bulto' ? 'selected' : '' }}>Bulto</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Cantidad</label>
                            <input type="number" name="presentaciones[{{ $index }}][cantidad_contenida]" value="{{ isset($pres['cantidad_contenida']) ? $pres['cantidad_contenida'] : $pres->cantidad_contenida }}" min="1" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">P. Minorista</label>
                            <input type="number" name="presentaciones[{{ $index }}][precio_minorista]" value="{{ isset($pres['precio_minorista']) ? $pres['precio_minorista'] : $pres->precio_minorista }}" step="0.01" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">P. Mayorista</label>
                            <input type="number" name="presentaciones[{{ $index }}][precio_mayorista]" value="{{ isset($pres['precio_mayorista']) ? $pres['precio_mayorista'] : $pres->precio_mayorista }}" step="0.01" class="w-full text-sm border-gray-300 rounded-md" required>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex justify-end gap-3 bg-gray-100">
            <button type="submit" class="bg-indigo-600 py-2 px-6 rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">Actualizar Producto</button>
        </div>
    </form>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = {{ count(old('presentaciones', $producto->presentaciones)) }};
    const container = document.getElementById('presentaciones-container');
    const btnAdd = document.getElementById('btn-add-presentation');

    btnAdd.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'presentacion-row bg-white p-4 border border-gray-200 rounded-md shadow-sm relative group mt-4';
        
        newRow.innerHTML = `
            <button type="button" class="btn-remove absolute -top-3 -right-3 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-200 opacity-0 group-hover:opacity-100 shadow border border-red-200" title="Eliminar fila">
                <span class="material-symbols-outlined" style="font-size: 14px;">close</span>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Tipo</label>
                    <select name="presentaciones[${rowIndex}][tipo]" class="w-full text-sm border-gray-300 rounded-md" required>
                        <option value="unidad">Unidad</option><option value="caja">Caja</option><option value="pack">Pack</option><option value="bulto">Bulto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Cantidad</label>
                    <input type="number" name="presentaciones[${rowIndex}][cantidad_contenida]" value="1" min="1" class="w-full text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">P. Minorista</label>
                    <input type="number" name="presentaciones[${rowIndex}][precio_minorista]" step="0.01" class="w-full text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">P. Mayorista</label>
                    <input type="number" name="presentaciones[${rowIndex}][precio_mayorista]" step="0.01" class="w-full text-sm border-gray-300 rounded-md" required>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        rowIndex++;

        newRow.querySelector('.btn-remove').addEventListener('click', function() {
            newRow.remove();
        });
    });

    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.presentacion-row').remove();
        });
    });
});
</script>
@endsection
