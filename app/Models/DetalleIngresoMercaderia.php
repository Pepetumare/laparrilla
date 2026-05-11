<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleIngresoMercaderia extends Model
{
    protected $table = 'detalle_ingresos_mercaderia';

    protected $fillable = [
        'ingreso_mercaderia_id',
        'producto_id',
        'numero_caja',
        'peso_kg',
        'observacion',
    ];

    public function ingreso()
    {
        return $this->belongsTo(IngresoMercaderia::class, 'ingreso_mercaderia_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}