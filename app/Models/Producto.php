<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['categoria_id', 'nombre_producto', 'descripcion_producto', 'imagen_url', 'stock_actual', 'stock_minimo', 'activo'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function presentaciones()
    {
        return $this->hasMany(PresentacionProducto::class);
    }
}
