<?php

namespace App\Models;

use Database\Factories\PedidoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    /** @use HasFactory<PedidoFactory> */
    use HasFactory;

    protected $fillable = ['fecha_pedido', 'total', 'estado', 'user_id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_pedido' => 'date',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Usuario propietario del pedido.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detalles de productos incluidos en el pedido.
     */
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
