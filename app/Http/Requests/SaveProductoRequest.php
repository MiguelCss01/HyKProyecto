<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Solo llega acá si el middleware admin lo permite
    }

    public function rules(): array
    {
        return [
            // Datos básicos del producto
            'nombre_producto' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'descripcion_producto' => 'nullable|string',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'activo' => 'boolean',

            // Datos de las presentaciones (Es un arreglo, debe haber al menos una)
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.tipo' => 'required|in:unidad,caja,pack,bulto',
            'presentaciones.*.cantidad_contenida' => 'required|integer|min:1',
            'presentaciones.*.precio_minorista' => 'required|numeric|min:0',
            'presentaciones.*.precio_mayorista' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'presentaciones.required' => 'El producto debe tener al menos una presentación o precio.',
            'presentaciones.*.precio_minorista.required' => 'El precio minorista es obligatorio en todas las presentaciones.',
            'presentaciones.*.precio_mayorista.required' => 'El precio mayorista es obligatorio en todas las presentaciones.',
        ];
    }
}
