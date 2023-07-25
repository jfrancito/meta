<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Session;

class WEBActivoFijo extends Model
{

    protected $table = 'WEB.activosfijos';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    
    public function usuario()
    {        
        return $this->belongsTo(User::class);
    }

    public function producto()
    {        
        return $this->belongsTo(ALMProducto::class, 'cod_producto', 'COD_PRODUCTO');       
    }

    public function categoria()
    {
        return $this->belongsTo(WEBCategoriaActivoFijo::class, 'categoria_activo_fijo_id', 'id');        
    }

    public function documento()
    {
        return $this->belongsTo(CMPDocumentoCtble::class, 'cod_documento_ctble', 'COD_DOCUMENTO_CTBLE');
    }

    public function empresa()
    {
        return $this->documento->belongsTo(STDEmpresa::class, 'COD_EMPR', 'COD_EMPR');
    }

    public function referencia()
    {
        return $this->belongsTo(CMPReferecenciaAsoc::class, 'cod_tabla', 'COD_TABLA');
    }

    public function detalle()
    {
        return $this->referencia->belongsTo(CMPDetalleProducto::class, 'COD_TABLA', 'COD_TABLA');
    }

    public function obtenerAlmacen()
    {
        //$centro_id = Session::get('centros')->COD_CENTRO;
		$empresa_id = Session::get('empresas_meta')->COD_EMPR;
        $almacen_id = DB::table('ALM.ALMACEN')->select('COD_ALMACEN')
                                              ->where('COD_EMPR','=',$empresa_id)
                                              //->where('COD_CENTRO','=',$centro_id)
                                              ->where('COD_ESTADO','=','1')
                                              ->where('COD_ACTIVO','=','1')
                                              ->where('NOM_ALMACEN','LIKE','%FIJO%')
                                              ->first();
        return $almacen_id->COD_ALMACEN;
    }

    public function obtenerActivoFijoAlmacen($producto_id, $id_almacen, $documento_id)
    {
        $producto = DB::select('EXEC WEB.LISTAR_ACTIVOS_FIJOS "'.$producto_id.'","'.$id_almacen.'","'.$documento_id.'"');        
        return $producto;
    }
}
