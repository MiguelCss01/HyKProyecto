<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total'];

    /**
     * Obtiene el usuario propietario del carrito.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los ítems guardados en el carrito.
     */
    public function items()
    {
        return $this->hasMany(ItemCarrito::class);
    }

    /**
     * Recalcula el total del carrito según el tipo de cliente y lo persiste.
     */
    public function recalcularTotal(): float
    {
        $this->loadMissing(['user', 'items.presentacionProducto']);

        $isMayorista = $this->user?->tipo_cliente === 'mayorista';

        $nuevoTotal = 0.0;
        foreach ($this->items as $item) {
            $presentacion = $item->presentacionProducto;
            if ($presentacion) {
                $precio = $isMayorista ? (float) $presentacion->precio_mayorista : (float) $presentacion->precio_minorista;
                $nuevoTotal += $precio * $item->cantidad;
            }
        }

        $this->update(['total' => $nuevoTotal]);

        return $nuevoTotal;
    }
}
