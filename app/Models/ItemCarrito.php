<?php

namespace App\Models;

use Database\Factories\ItemCarritoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrito extends Model
{
    /** @use HasFactory<ItemCarritoFactory> */
    use HasFactory;

    protected $table = 'item_carritos';

    protected $fillable = ['carrito_id', 'presentacion_producto_id', 'cantidad'];

    /**
     * Carrito al que pertenece este ítem.
     */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }

    /**
     * Presentación del producto seleccionada.
     */
    public function presentacionProducto()
    {
        return $this->belongsTo(PresentacionProducto::class, 'presentacion_producto_id');
    }

    /**
     * Accesor para calcular el subtotal del ítem según el tipo de cliente.
     */
    public function getSubtotalAttribute(): float
    {
        $this->loadMissing(['carrito.user', 'presentacionProducto']);

        $isMayorista = $this->carrito?->user?->tipo_cliente === 'mayorista';
        $precio = $isMayorista
            ? (float) $this->presentacionProducto?->precio_mayorista
            : (float) $this->presentacionProducto?->precio_minorista;

        return $precio * $this->cantidad;
    }
}
