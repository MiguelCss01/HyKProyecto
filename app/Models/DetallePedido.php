<?php

namespace App\Models;

use Database\Factories\DetallePedidoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    /** @use HasFactory<DetallePedidoFactory> */
    use HasFactory;

    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'presentacion_producto_id',
        'cantidad',
        'precio_unitario',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'cantidad' => 'integer',
        ];
    }

    /**
     * Pedido al que pertenece el detalle.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Presentación del producto comprada.
     */
    public function presentacionProducto()
    {
        return $this->belongsTo(PresentacionProducto::class, 'presentacion_producto_id');
    }

    /**
     * Subtotal calculado del detalle.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->precio_unitario * $this->cantidad);
    }
}
