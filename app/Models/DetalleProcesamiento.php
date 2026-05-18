<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleProcesamiento extends Model
{
    protected $table = 'detalle_procesamientos';

    protected $fillable = [
        'procesamiento_id',
        'numero_bolsa',
        'peso_kg',
        'observacion',
    ];

    public function procesamiento()
    {
        return $this->belongsTo(Procesamiento::class);
    }
}