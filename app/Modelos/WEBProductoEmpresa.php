<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBProductoEmpresa extends Model
{
    protected $table = 'WEB.productoempresas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    
    public function cuentacontabletercero()
    {
        return $this->belongsTo('App\Modelos\WEBCuentaContable', 'cuenta_contable_tercero_id', 'id');
    }

    public function cuentacontablerelacionada()
    {
        return $this->belongsTo('App\Modelos\WEBCuentaContable', 'cuenta_contable_relacionada_id', 'id');
    }

    public function cuentacontablecompra()
    {
        return $this->belongsTo('App\Modelos\WEBCuentaContable', 'cuenta_contable_id', 'id');
    }

}
