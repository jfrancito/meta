<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CONAsiento extends Model
{
    protected $table = 'CON.ASIENTO';
    public $timestamps=false;
    protected $primaryKey = 'COD_ASIENTO';
	public $incrementing = false;
    public $keyType = 'string';
    
}
