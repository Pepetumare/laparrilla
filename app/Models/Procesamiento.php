<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procesamiento extends Model
{
    protected $fillable = [
        'ingreso_mercaderia_id',
        'producto_id',
        'sucursal_id',
        'user_id',
        'fecha_procesamiento',
        'peso_inicial_kg',
        'peso_util_kg',
        'merma_kg',
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

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleProcesamiento::class);
    }
}