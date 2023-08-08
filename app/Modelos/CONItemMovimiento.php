<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CONItemMovimiento extends Model
{
    //
    protected $table = 'CON.ITEM_MOVIMIENTO';
    public $timestamps=false;

    protected $primaryKey = 'COD_ITEM_MOVIMIENTO';
	public $incrementing = false;
    public $keyType = 'string';
    
}
