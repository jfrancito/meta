<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBCuentaContable extends Model
{
    protected $table = 'WEB.cuentacontables';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function asientomodelodetalle()
    {
        return $this->hasMany('App\Modelos\WEBAsientoModeloDetalle', 'cuenta_contable_id', 'id');
    }


}
