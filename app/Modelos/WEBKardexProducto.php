<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBKardexProducto extends Model
{
    protected $table = 'WEB.kardexproductos';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function producto()
    {
        return $this->belongsTo('App\Modelos\ALMProducto', 'producto_id', 'COD_PRODUCTO');
    }

     public function tipoproducto()
    {
        return $this->belongsTo('App\Modelos\CMPCategoria', 'tipo_producto_id', 'COD_CATEGORIA');
    }   
}
