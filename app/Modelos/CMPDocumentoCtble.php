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
    
    public function tipo_documento()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'COD_CATEGORIA_TIPO_DOC', 'COD_CATEGORIA');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Modelos\STDEmpresa', 'COD_EMPR_RECEPTOR', 'COD_EMPR');
    }

    public function scopeTransGratuita($query,$trangratuita){
        if(trim($trangratuita) != 'TODOS'){
            $query->where('CMP.DOCUMENTO_CTBLE.IND_GRATUITO', '=', $trangratuita);
        }
    }


}
