<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCierreDiario extends Model
{
    protected $table = 'detalle_cierres_diarios';

    protected $fillable = [
        'cierre_diario_id',
        'producto_id',
        'stock_disponible_kg',
        'kilos_vendidos_kg',
        'stock_restante_calculado_kg',
        'observacion',
    ];

    public function cierre()
    {
        return $this->belongsTo(CierreDiario::class, 'cierre_diario_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
