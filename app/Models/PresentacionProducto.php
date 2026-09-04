<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresentacionProducto extends Model
{
    use HasFactory;

    protected $table = 'presentacion_productos';

    protected $fillable = ['producto_id', 'tipo', 'precio_minorista', 'precio_mayorista', 'cantidad_contenida'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
