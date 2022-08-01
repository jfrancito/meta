<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\TESOperacionCaja;
use App\Modelos\TESCajaBanco;
use App\Modelos\WEBKardexProducto;
use App\Modelos\WEBAsiento;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait KardexTraits
{



	public function kd_cantidad_producto_venta($listamovimiento,$producto_id ,$periodo_id)
	{

		$monto 				= 	0;

	    $montomovi 			= 	$listamovimiento->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_PERIODO','=',$periodo_id)
	    						->first();

	    if(count($montomovi)>0){
			$monto 			= 	$montomovi->CAN_PRODUCTO;
	    }


		return $monto;

	}

	private function kd_lista_saldo_inicial($empresa_id, $tipo_producto_id)
	{

	    $listasaldoincial 	= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('activo','=',1)
									->orderBy('id', 'asc')
			    					->get();

		return $listasaldoincial;

	}

	private function kd_lista_movimiento($empresa_id, $anio, $tipo_producto_id, $tipo_asiento_id)
	{


	    $listamovimiento 	= 	WEBAsiento::join('CMP.DETALLE_PRODUCTO', 'WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
	    						->join('WEB.kardexproductos', 'WEB.kardexproductos.producto_id', '=', 'CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
	    						->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    						//->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    						->TipoAsiento($tipo_asiento_id)
	    						->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    						->where('WEB.kardexproductos.tipo_producto_id','=',$tipo_producto_id)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->where('CON.PERIODO.COD_ANIO','=',$anio)
	    						->where('WEB.kardexproductos.activo','=',1)
	    						->where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
	    						->select(DB::raw("CMP.DETALLE_PRODUCTO.COD_PRODUCTO,
												TXT_NOMBRE_PRODUCTO,
												sum(CAN_PRODUCTO) CAN_PRODUCTO,
												CON.PERIODO.COD_ANIO AS ANIO,
												CON.PERIODO.COD_PERIODO,
												CON.PERIODO.COD_MES,
												CON.PERIODO.TXT_NOMBRE AS MES"))
	    						->groupBy('CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
	    						->groupBy('CMP.DETALLE_PRODUCTO.TXT_NOMBRE_PRODUCTO')
	    						->groupBy('CON.PERIODO.COD_ANIO')
	    						->groupBy('CON.PERIODO.COD_PERIODO')
	    						->groupBy('CON.PERIODO.COD_MES')
	    						->groupBy('CON.PERIODO.TXT_NOMBRE')->get();


		return $listamovimiento;

	}

	private function kd_lista_producto_periodo($empresa_id, $anio, $tipo_asiento_id,$producto_id,$periodo_id)
	{

	    $listaproducto 	= 	WEBAsiento::join('CMP.DETALLE_PRODUCTO', 'WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
	    						->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    						->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    						->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    						->where('CON.PERIODO.COD_ANIO','=',$anio)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->where('CMP.DETALLE_PRODUCTO.COD_PRODUCTO','=',$producto_id)
	    						->where('CON.PERIODO.COD_PERIODO','=',$periodo_id)
	    						->where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
	    						->select(DB::raw("
	    										CON.PERIODO.TXT_NOMBRE AS NOMBRE_PERIODO
												,WEB.asientos.TXT_CATEGORIA_TIPO_DOCUMENTO
												,WEB.asientos.FEC_ASIENTO
												,WEB.asientos.NRO_SERIE
												,WEB.asientos.NRO_DOC
												,CMP.DETALLE_PRODUCTO.TXT_NOMBRE_PRODUCTO
												,CMP.DETALLE_PRODUCTO.CAN_PRODUCTO
												"))->orderBy('WEB.asientos.FEC_ASIENTO', 'asc')->get();


		return $listaproducto;

	}

	public function kd_cantidad_producto_venta_totales($listamovimiento ,$periodo_id)
	{

		$monto 				= 	0;

	    $monto 				= 	$listamovimiento->where('COD_PERIODO','=',$periodo_id)
	    						->sum('CAN_PRODUCTO');


		return $monto;

	}
	public function kd_cantidad_producto_if($listamovimientocompra,$listamovimientoventa,$cantii,$producto_id ,$mes)
	{

		$mes 				= 	(int)$mes;
		//compra
		$montocom 			= 	0;
	    $montomovic 		= 	$listamovimientocompra->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_MES','<=',$mes)
	    						->sum('CAN_PRODUCTO');

		//venta
		$montoven 			= 	0;
	    $montomoviv 		= 	$listamovimientoventa->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_MES','<=',$mes)
	    						->sum('CAN_PRODUCTO');

	    $monto 				= 	0;
		$monto 				= 	(float)$cantii+$montomovic-$montomoviv;


		return $monto;

	}


}