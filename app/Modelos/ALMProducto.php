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


}
