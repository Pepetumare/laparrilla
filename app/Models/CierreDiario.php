<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreDiario extends Model
{
    protected $table = 'cierres_diarios';

    protected $fillable = [
        'sucursal_id',
        'user_id',
        'fecha_cierre',
        'observacion',
    ];

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
        return $this->hasMany(DetalleCierreDiario::class);
    }
}
