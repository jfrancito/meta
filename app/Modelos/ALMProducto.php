<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class ALMProducto extends Model
{
    protected $table = 'ALM.PRODUCTO';
    public $timestamps=false;

    protected $primaryKey = 'COD_PRODUCTO';
    public $incrementing = false;
    public $keyType = 'string';

    public function inventariosegundaventa()
    {
        return $this->hasMany('App\Modelos\WEBInventarioSegundaVenta', 'COD_PERIODO', 'id');
    }

    public function scopeCodProducto($query,$producto_id){
        if(trim($producto_id) != ''){
            $query->where('ALM.PRODUCTO.COD_PRODUCTO', '=', $producto_id);
        }
    }

    public function scopeCodServicio($query,$servicio_id){
        if(trim($servicio_id) != ''){
            $query->where('ALM.PRODUCTO.COD_CATEGORIA_SERVICIO', '=', $servicio_id);
        }
    }

    public function scopeCodMaterial($query,$material_id){
        if(trim($material_id) != ''){
            $query->where('ALM.PRODUCTO.COD_CATEGORIA_SUB_FAMILIA', '=', $material_id);
        }
    }

    public function scopeIndMaterialServicio($query,$serviciomaterial){
        if(trim($serviciomaterial) != ''){
            $query->where('ALM.PRODUCTO.IND_MATERIAL_SERVICIO', '=', $serviciomaterial);
        }
    }

    public function scopeArrayProducto($query,$array_productos_empresa){
        if(count($array_productos_empresa) > 0){
            $query->whereIn('ALM.PRODUCTO.COD_PRODUCTO', $array_productos_empresa);
        }
    }

}
