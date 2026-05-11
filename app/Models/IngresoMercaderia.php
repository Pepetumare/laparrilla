<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoMercaderia extends Model
{
    protected $table = 'ingresos_mercaderia';

    protected $fillable = [
        'proveedor_id',
        'fecha_ingreso',
        'observacion',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleIngresoMercaderia::class);
    }
}