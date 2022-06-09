<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class TESCajaBanco extends Model
{
    protected $table = 'TES.CAJA_BANCO';
    public $timestamps=false;

    protected $primaryKey = 'COD_CAJA_BANCO';
	public $incrementing = false;
    public $keyType = 'string';
    

    public function operacioncaja()
    {
        return $this->hasMany('App\Modelos\TESOperacionCaja', 'COD_CAJA_BANCO', 'COD_CAJA_BANCO');
    }


    public function cuentacontable()
    {
        return $this->belongsTo('App\Modelos\WEBCuentaContable', 'TXT_TIPO_REFERENCIA', 'id');
    }

}
