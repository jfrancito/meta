<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CMPDocumentoCtble extends Model
{
    protected $table = 'CMP.DOCUMENTO_CTBLE';
    public $timestamps=false;

    protected $primaryKey = 'COD_DOCUMENTO_CTBLE';
	public $incrementing = false;
    public $keyType = 'string';
    
    public function migrar_venta()
    {
        return $this->hasMany('App\Modelos\WEBHistorialMigrar', 'COD_REFERENCIA', 'COD_DOCUMENTO_CTBLE');
    }
    
    public function periodo()
    {
        return $this->belongsTo('App\Modelos\CONPeriodo', 'COD_PERIODO', 'COD_PERIODO');
    }
    
}
