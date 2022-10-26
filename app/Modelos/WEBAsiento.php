<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsiento extends Model
{
    protected $table = 'WEB.asientos';
    public $timestamps=false;
    protected $primaryKey = 'COD_ASIENTO';
	public $incrementing = false;
	public $keyType = 'string';


    public function periodo()
    {
        return $this->belongsTo('App\Modelos\CONPeriodo', 'COD_PERIODO', 'COD_PERIODO');
    }

    public function moneda()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'COD_CATEGORIA_MONEDA', 'COD_CATEGORIA');
    }

    public function viewmigrarcompra()
    {
        return $this->belongsTo('App\Modelos\WEBViewMigrarCompras', 'TXT_REFERENCIA', 'COD_DOCUMENTO_CTBLE');
    }


    public function tipo_documento()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'COD_CATEGORIA_TIPO_DOCUMENTO', 'COD_CATEGORIA');
    }

    public function tipo_documento_ref()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'COD_CATEGORIA_TIPO_DOCUMENTO_REF', 'COD_CATEGORIA');
    }

    public function asientomovimiento()
    {
        return $this->hasMany('App\Modelos\WEBAsientoMovimiento', 'COD_ASIENTO', 'COD_ASIENTO');
    }

    public function empresa()
    {
        return $this->belongsTo('App\Modelos\STDEmpresa', 'COD_EMPR_CLI', 'COD_EMPR');
    }


    public function scopeNroSerie($query,$serie){
        if(trim($serie) != ''){
            $query->where('WEB.asientos.NRO_SERIE', 'like', '%'.$serie.'%');
        }
    }

    public function scopeNroDocumento($query,$documento){
        if(trim($documento) != ''){
            $query->where('WEB.asientos.NRO_DOC', 'like', '%'.$documento.'%');
        }
    }

    public function scopeTipoAsiento($query,$tipo_asiento_id){
        if(trim($tipo_asiento_id) != ''){
            $query->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO', '=', $tipo_asiento_id);
        }
    }
    
    public function scopePeriodo($query,$periodo_id){
        if(trim($periodo_id) != ''){
            $query->where('CON.PERIODO.COD_PERIODO', '=', $periodo_id);
        }
    }

    public function scopeOrdFecha($query,$tipoasiento){
        if($tipoasiento == 'TAS0000000000004'){
            $query->orderBy('FEC_USUARIO_MODIF_AUD','ASC');
        }else{
            $query->orderBy('FEC_ASIENTO','ASC');
        }
    }



}
