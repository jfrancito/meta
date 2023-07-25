<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoCompraActivoFijo extends Model
{
    //
    protected $table = 'WEB.asientoscompraactivosfijos';
    public $timestamps = false;
    protected $primaryKey = 'COD_ASIENTO';
    public $incrementing = false;
    public $keyType = 'string';
}
