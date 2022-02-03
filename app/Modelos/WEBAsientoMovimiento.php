<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoMovimiento extends Model
{
    protected $table = 'WEB.asientomovimientos';
    public $timestamps=false;
    protected $primaryKey = 'COD_ASIENTO_MOVIMIENTO';
	public $incrementing = false;
	public $keyType = 'string';


}
