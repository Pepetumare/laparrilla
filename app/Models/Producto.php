<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
        'unidad_medida',
        'activo',
    ];

    public function detallesIngresos()
    {
        return $this->hasMany(DetalleIngresoMercaderia::class);
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedor');
    }

    public function detallesCierres()
    {
        return $this->hasMany(DetalleCierreDiario::class);
    }
}
