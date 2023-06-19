<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBDetalleSegundaVenta extends Model
{
    protected $table = 'WEB.detallesegundaventas';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';


    public function inventariosegundaventa()
    {
        return $this->belongsTo('App\Modelos\WEBInventarioSegundaVenta', 'inventariosegundaventa_id', 'id');
    }

}
