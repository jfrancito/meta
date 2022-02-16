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

    public function documento_ctble_tipo_documento()
    {
        return $this->hasMany('App\Modelos\CMPDocumentoCtble', 'COD_CATEGORIA_TIPO_DOC', 'COD_CATEGORIA');
    }

    public function empresa_tipo_documento()
    {
        return $this->hasMany('App\Modelos\STDEmpresa', 'COD_TIPO_DOCUMENTO', 'COD_CATEGORIA');
    }

    public function asiento_moneda()
    {
        return $this->hasMany('App\Modelos\WEBAsiento', 'COD_CATEGORIA_MONEDA', 'COD_CATEGORIA');
    }


}



