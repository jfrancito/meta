<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBInventarioSegundaVenta extends Model
{
    protected $table = 'WEB.inventariosegundaventas';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function detallesegundaventa()
    {
        return $this->hasMany('App\Modelos\WEBDetalleSegundaVenta', 'inventariosegundaventa_id', 'id');
    }
    public function periodo()
    {
        return $this->belongsTo('App\Modelos\CONPeriodo', 'periodo_id', 'COD_PERIODO');
    }
    public function producto()
    {
        return $this->belongsTo('App\Modelos\ALMProducto', 'producto_id', 'COD_PRODUCTO');
    }


}
