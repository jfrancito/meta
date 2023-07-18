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

	private function cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa,$producto_id,$servicio_id,$material_id,$serviciomaterial)
	{
		$lista_configuracion_producto 	= 	ALMProducto::leftJoin('WEB.productoempresas', function ($join) use ($anio,$empresa_id){
									            $join->on('WEB.productoempresas.producto_id', '=', 'ALM.PRODUCTO.COD_PRODUCTO')
									            ->whereIn('WEB.productoempresas.empresa_id',[$empresa_id])
									            ->whereIn('WEB.productoempresas.anio',[$anio]);
									        })
											->leftJoin('WEB.cuentacontables as relacionada', 'relacionada.id', '=', 'WEB.productoempresas.cuenta_contable_venta_relacionada_id')
											->leftJoin('WEB.cuentacontables as tercero', 'tercero.id', '=', 'WEB.productoempresas.cuenta_contable_venta_tercero_id')
											->leftJoin('WEB.cuentacontables as relacionadasv', 'relacionadasv.id', '=', 'WEB.productoempresas.cuenta_contable_venta_segunda_relacionada_id')
											->leftJoin('WEB.cuentacontables as tercerosv', 'tercerosv.id', '=', 'WEB.productoempresas.cuenta_contable_venta_segunda_tercero_id')
											->leftJoin('WEB.cuentacontables as cuentacompra', 'cuentacompra.id', '=', 'WEB.productoempresas.cuenta_contable_compra_id')
											->leftJoin('CMP.CATEGORIA as servicio', 'servicio.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_SERVICIO')
											->leftJoin('CMP.CATEGORIA as material', 'material.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_SUB_FAMILIA')
											->ArrayProducto($array_productos_empresa)
											->CodProducto($producto_id)
											->CodServicio($servicio_id)
											->CodMaterial($material_id)
											->IndMaterialServicio($serviciomaterial)
											->where('ALM.PRODUCTO.COD_ESTADO','=',1)
											->select(DB::raw("ALM.PRODUCTO.COD_PRODUCTO as producto_id,
																ALM.PRODUCTO.NOM_PRODUCTO as producto_nombre,
																ALM.PRODUCTO.IND_MATERIAL_SERVICIO as material_servicio,

																relacionada.nro_cuenta +' '+relacionada.nombre as nombre_nro_cuenta_r,
																tercero.nro_cuenta +' '+tercero.nombre as nombre_nro_cuenta_t,

																relacionadasv.nro_cuenta +' '+relacionadasv.nombre as nombre_nro_cuenta_r_sv,
																tercerosv.nro_cuenta +' '+tercerosv.nombre as nombre_nro_cuenta_t_sv,		

																cuentacompra.nro_cuenta +' '+cuentacompra.nombre as nombre_nro_cuenta_compra,
																WEB.productoempresas.anio,
																servicio.NOM_CATEGORIA as  nom_servicio,
																material.NOM_CATEGORIA as  nom_material,
																WEB.productoempresas.codigo_migracion"))
											->get();

		//dd($lista_configuracion_producto);

		return $lista_configuracion_producto;

	}

}