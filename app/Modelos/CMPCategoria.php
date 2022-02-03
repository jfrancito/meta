<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CMPCategoria extends Model
{
    protected $table            =   'CMP.CATEGORIA';
    public $timestamps          =   false;
    protected $primaryKey       =   'COD_CATEGORIA';
	public $incrementing        =   false;
    public $keyType             =   'string';


    public function modeloasiento_tipoasiento()
    {
        return $this->hasMany('App\Modelos\WEBAsientoModelo', 'tipo_asiento_id', 'COD_CATEGORIA');
    }


    public function modeloasiento_moneda()
    {
        return $this->hasMany('App\Modelos\WEBAsientoModelo', 'moneda_id', 'COD_CATEGORIA');
    }

}



