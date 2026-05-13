<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'activo',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function ingresos()
    {
        return $this->hasMany(IngresoMercaderia::class);
    }
}