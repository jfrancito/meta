<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class TESOperacionCaja extends Model
{
    protected $table = 'TES.OPERACION_CAJA';
    public $timestamps=false;

    protected $primaryKey = 'COD_OPERACION_CAJA';
	public $incrementing = false;
    public $keyType = 'string';
    

    public function cajabanco()
    {
        return $this->belongsTo('App\Modelos\TESCajaBanco', 'COD_CAJA_BANCO', 'COD_CAJA_BANCO');
    }

}
