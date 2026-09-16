@extends('layouts.admin')

@section('title', 'Nuevo Producto')

@section('content')
<div class="flex-1 overflow-auto custom-scrollbar">
    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Nuevo Producto</h2>
            <p class="text-sm text-gray-600">Completá los datos del producto y sus presentaciones (precios).</p>
        </div>
        <a href="{{ route('admin.productos.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 font-medium">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-red-500 mr-2">error</span>
                <p class="text-red-700 font-bold text-sm">Hay errores en el formulario:</p>
            </div>
            <ul class="mt-2 ml-6 list-disc text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.productos.store') }}" method="POST" class="bg-white shadow rounded-lg overflow-hidden" id="producto-form">
        @csrf

        <!-- SECCIÓN 1: Datos Básicos -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">inventory_2</span>
                1. Información Básica
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_producto" value="{{ old('nombre_producto') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                    <select name="categoria_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion_producto" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('descripcion_producto') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_actual" value="{{ old('stock_actual', 0) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo (Alerta) <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2 flex items-center mt-2">
                    <input type="checkbox" name="activo" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900 font-medium">Producto Activo (Visible en la tienda)</label>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: Presentaciones (Javascript dinámico) -->
        <div class="p-6 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600">sell</span>
                    2. Presentaciones y Precios
                </h3>
                <button type="button" id="btn-add-presentation" class="text-sm bg-green-100 text-green-700 hover:bg-green-200 font-semibold py-1 px-3 rounded flex items-center gap-1 transition">
                    <span class="material-symbols-outlined text-sm">add</span> Agregar Variante
                </button>
            </div>

            <!-- Contenedor donde JS inyectará las filas -->
            <div id="presentaciones-container" class="space-y-4">
                <!-- Fila 1 (Siempre visible) -->
                <div class="presentacion-row bg-white p-4 border border-gray-200 rounded-md shadow-sm relative group">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Tipo</label>
                            <select name="presentaciones[0][tipo]" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="unidad">Unidad</option>
                                <option value="caja">Caja</option>
                                <option value="pack">Pack</option>
                                <option value="bulto">Bulto</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Cantidad Incluida</label>
                            <input type="number" name="presentaciones[0][cantidad_contenida]" value="1" min="1" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Ej: 1 (Unidad) o 6 (Caja)">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Precio Minorista ($)</label>
                            <input type="number" name="presentaciones[0][precio_minorista]" step="0.01" min="0" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Precio Mayorista ($)</label>
                            <input type="number" name="presentaciones[0][precio_mayorista]" step="0.01" min="0" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex justify-end gap-3 bg-gray-100">
            <a href="{{ route('admin.productos.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancelar
            </a>
            <button type="submit" class="bg-indigo-600 py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Guardar Producto
            </button>
        </div>
    </form>
</div>
</div>

<!-- Lógica Javascript para agregar filas dinámicas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1; // Ya tenemos la fila 0 en el HTML
    const container = document.getElementById('presentaciones-container');
    const btnAdd = document.getElementById('btn-add-presentation');

    btnAdd.addEventListener('click', function() {
        // 1. Creamos un nuevo div que será la fila
        const newRow = document.createElement('div');
        newRow.className = 'presentacion-row bg-white p-4 border border-gray-200 rounded-md shadow-sm relative group mt-4';
        
        // 2. Le inyectamos el HTML de los inputs, PERO usamos "rowIndex" en los "name" para que sean un array válido para PHP
        newRow.innerHTML = `
            <button type="button" class="btn-remove absolute -top-3 -right-3 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-200 transition opacity-0 group-hover:opacity-100 shadow-sm border border-red-200" title="Eliminar fila">
                <span class="material-symbols-outlined" style="font-size: 14px;">close</span>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Tipo</label>
                    <select name="presentaciones[${rowIndex}][tipo]" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="unidad">Unidad</option>
                        <option value="caja" selected>Caja</option>
                        <option value="pack">Pack</option>
                        <option value="bulto">Bulto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Cantidad Incluida</label>
                    <input type="number" name="presentaciones[${rowIndex}][cantidad_contenida]" value="6" min="1" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Precio Minorista ($)</label>
                    <input type="number" name="presentaciones[${rowIndex}][precio_minorista]" step="0.01" min="0" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 uppercase font-bold mb-1">Precio Mayorista ($)</label>
                    <input type="number" name="presentaciones[${rowIndex}][precio_mayorista]" step="0.01" min="0" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
        `;
        
        // 3. Agregamos la fila al contenedor
        container.appendChild(newRow);
        
        // 4. Sumamos 1 al índice para la próxima vez
        rowIndex++;

        // 5. Le damos vida al botón de eliminar ("X") que creamos recién
        const btnRemove = newRow.querySelector('.btn-remove');
        btnRemove.addEventListener('click', function() {
            newRow.remove(); // Borra el HTML del DOM
        });
    });
});
</script>
@endsection
