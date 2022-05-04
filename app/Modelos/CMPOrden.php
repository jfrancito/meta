<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CMPOrden extends Model
{
    protected $table = 'CMP.ORDEN';
    public $timestamps=false;

    protected $primaryKey = 'COD_ORDEN';
	public $incrementing = false;
    public $keyType = 'string';
    
}
