<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CONFlujoCaja extends Model
{
    //
    protected $table = 'CON.FLUJO_CAJA';
    public $timestamps=false;

    protected $primaryKey = 'COD_FLUJO_CAJA';
	public $incrementing = false;
    public $keyType = 'string';    
}
