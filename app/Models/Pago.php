<?php

namespace App\Models;

use Database\Factories\PagoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    /** @use HasFactory<PagoFactory> */
    use HasFactory;

    protected $fillable = [
        'monto',
        'id_transaccion_mp',
        'fecha_pago',
        'pedido_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'date',
        ];
    }

    /**
     * Pedido al que pertenece el pago.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
