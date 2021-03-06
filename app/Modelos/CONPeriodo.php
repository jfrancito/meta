<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CONPeriodo extends Model
{
    protected $table = 'CON.PERIODO';
    public $timestamps=false;
    protected $primaryKey = 'COD_PERIODO';
	public $incrementing = false;
	public $keyType = 'string';

	public function asiento()
    {
        return $this->hasMany('App\Modelos\WEBAsiento', 'COD_PERIODO', 'id');
    }


    public function migrar_venta()
    {
        return $this->hasMany('App\Modelos\WEBHistorialMigrar', 'COD_PERIODO', 'COD_PERIODO');
    }

    public function documento_ctble()
    {
        return $this->hasMany('App\Modelos\CMPDocumentoCtble', 'COD_PERIODO', 'COD_PERIODO');
    }


}



