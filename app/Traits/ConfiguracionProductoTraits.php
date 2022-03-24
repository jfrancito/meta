<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait ConfiguracionProductoTraits
{

	private function cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa)
	{
		$lista_configuracion_producto 	= 	ALMProducto::leftJoin('WEB.productoempresas', function ($join) use ($anio,$empresa_id){
									            $join->on('WEB.productoempresas.producto_id', '=', 'ALM.PRODUCTO.COD_PRODUCTO')
									            ->whereIn('WEB.productoempresas.empresa_id',[$empresa_id])
									            ->whereIn('WEB.productoempresas.anio',[$anio]);
									        })
											->leftJoin('WEB.cuentacontables as relacionada', 'relacionada.id', '=', 'WEB.productoempresas.cuenta_contable_venta_relacionada_id')
											->leftJoin('WEB.cuentacontables as tercero', 'tercero.id', '=', 'WEB.productoempresas.cuenta_contable_venta_tercero_id')
											->leftJoin('WEB.cuentacontables as cuentacompra', 'cuentacompra.id', '=', 'WEB.productoempresas.cuenta_contable_compra_id')
											->whereIn('ALM.PRODUCTO.COD_PRODUCTO',$array_productos_empresa)
											->select(DB::raw("ALM.PRODUCTO.COD_PRODUCTO as producto_id,
																ALM.PRODUCTO.NOM_PRODUCTO as producto_nombre,
																ALM.PRODUCTO.IND_MATERIAL_SERVICIO as material_servicio,
																relacionada.nro_cuenta +' '+relacionada.nombre as nombre_nro_cuenta_r,
																tercero.nro_cuenta +' '+tercero.nombre as nombre_nro_cuenta_t,
																cuentacompra.nro_cuenta +' '+cuentacompra.nombre as nombre_nro_cuenta_compra,
																WEB.productoempresas.anio"))
											->get();

		return $lista_configuracion_producto;

	}

}