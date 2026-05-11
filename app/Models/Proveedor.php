<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'nombre',
        'telefono',
        'activo',
    ];

    public function ingresos()
    {
        return $this->hasMany(IngresoMercaderia::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedor');
    }
}
