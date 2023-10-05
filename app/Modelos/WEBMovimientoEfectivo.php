<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBMovimientoEfectivo extends Model
{
    protected $table = 'WEB.MOVIMIENTOEFECTIVO';
    public $timestamps=false;

    protected $primaryKey = 'COD_OPERACION';
    public $incrementing = false;
    public $keyType = 'string';

}