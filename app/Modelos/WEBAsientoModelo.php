<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoModelo extends Model
{
    protected $table = 'WEB.asientomodelos';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';


    public function tipoasiento()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'tipo_asiento_id', 'COD_CATEGORIA');
    }

    public function moneda()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'moneda_id', 'COD_CATEGORIA');
    }

    public function scopetipoasiento($query,$tipoasiento){
        if(trim($tipoasiento) != 'TODO'){
            $query->where('tipo_asiento_id','=',$tipoasiento);
        }
    }

    public function asientomodelodetalle()
    {
        return $this->belongsTo('App\Modelos\WEBAsientoModeloDetalle', 'asiento_modelo_id', 'id');
    }


}
