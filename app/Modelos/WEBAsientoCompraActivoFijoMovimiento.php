<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoCompraActivoFijoMovimiento extends Model
{
    //
    protected $table = 'WEB.asientoscompraactivosfijosmovimientos';
    public $timestamps = false;
    protected $primaryKey = 'COD_ASIENTO_MOVIMIENTO';
    public $incrementing = false;
    public $keyType = 'string';
}
