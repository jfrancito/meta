<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoModeloDetalle extends Model
{
    protected $table = 'WEB.asientomodelodetalles';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';


    public function asientomodelo()
    {
        return $this->hasMany('App\Modelos\WEBAsientoModelo', 'asiento_modelo_id', 'id');
    }

    public function cuentacontable()
    {
        return $this->belongsTo('App\Modelos\WEBCuentaContable', 'cuenta_contable_id', 'id');
    }

}
