<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBViewMigrarCompras;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsiento;
use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\CMPOrden;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait ComprasTraits
{

	private function co_generacion_combo_detraccion($txt_grupo,$titulo,$todo) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
        								->where('COD_CATEGORIA','=','DCT0000000000002')
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}


	private function co_orden_compra_tipo_descuento($orden)
	{

		$sel_tipo_descuento  =	'';
		if(count($orden)>0){
			if($orden->CAN_DETRACCION>0){
				$sel_tipo_descuento  =	'DCT0000000000002';
			}else{
				if($orden->CAN_RETENCION>0){
					$sel_tipo_descuento  =	'DCT0000000000001';
				}else{
					if($orden->CAN_PERCEPCION>0){
						$sel_tipo_descuento  =	'DCT0000000000004';
					}
				}
			}
		}					
		return $sel_tipo_descuento;
	}


	private function co_orden_xdocumento_contable($cod_documento_ctble)
	{

		$orden 			=	'';
		$referencia		=	CMPReferecenciaAsoc::where('COD_TABLA','=',$cod_documento_ctble)
							->where('COD_ESTADO','=',1)
							->where('TXT_TABLA_ASOC','=','CMP.ORDEN')
							->first();

		if(count($referencia)>0){
			$orden      = 	CMPOrden::where('COD_ORDEN','=',$referencia->COD_TABLA_ASOC)->first();
		}					

		return $orden;
	}


	private function co_lista_compras_migrar($anio,$periodo_id,$empresa_id,$serie,$documento)
	{

		$lista_compras		=		WEBViewMigrarCompras::where('WEB.viewmigrarcompras.COD_PERIODO','=',$periodo_id)
									->where('WEB.viewmigrarcompras.COD_EMPR','=',$empresa_id)
									->NroSerie($serie)
									->NroDocumento($documento)
									->select(DB::raw('WEB.viewmigrarcompras.NRO_SERIE,
													  WEB.viewmigrarcompras.NRO_DOC,
													  WEB.viewmigrarcompras.FEC_EMISION,
													  WEB.viewmigrarcompras.NOM_TIPO_DOC,
													  WEB.viewmigrarcompras.NOM_PROVEEDOR,
													  WEB.viewmigrarcompras.NOM_MONEDA,
													  sum(WEB.viewmigrarcompras.CAN_SUB_TOTAL) as CAN_SUB_TOTAL,
													  sum(WEB.viewmigrarcompras.CAN_IMPUESTO_VTA) as CAN_IMPUESTO_VTA,
													  sum(WEB.viewmigrarcompras.CAN_TOTAL) as CAN_TOTAL,
													  WEB.viewmigrarcompras.NOM_ESTADO,
													  WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE'))
									->groupBy('WEB.viewmigrarcompras.NRO_SERIE')
									->groupBy('WEB.viewmigrarcompras.NRO_DOC')
									->groupBy('WEB.viewmigrarcompras.FEC_EMISION')
									->groupBy('WEB.viewmigrarcompras.NOM_TIPO_DOC')
									->groupBy('WEB.viewmigrarcompras.NOM_PROVEEDOR')
									->groupBy('WEB.viewmigrarcompras.NOM_MONEDA')
									->groupBy('WEB.viewmigrarcompras.NOM_ESTADO')
									->groupBy('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
									->get();

		return $lista_compras;

	}

	private function co_lista_compras_asiento($anio,$periodo_id,$empresa_id,$serie,$documento)
	{

	    $lista_compras 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', function ($join) use ($periodo_id,$empresa_id){
							            $join->on('WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
							        })
	    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    							->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    							->NroSerie($serie)
	    							->NroDocumento($documento)
	    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000032')
									->select(DB::raw('WEB.asientos.*'))
									->orderby('CMP.DOCUMENTO_CTBLE.FEC_EMISION','asc')
	    							->get();

		return $lista_compras;

	}


	public function co_data_view_compras($cod_documento_ctble)
	{

		$data_compra		=		WEBViewMigrarCompras::where('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE','=',$cod_documento_ctble)
									->select(DB::raw('WEB.viewmigrarcompras.NRO_SERIE,
													  WEB.viewmigrarcompras.NRO_DOC,
													  WEB.viewmigrarcompras.FEC_EMISION,
													  WEB.viewmigrarcompras.NOM_TIPO_DOC,
													  WEB.viewmigrarcompras.NOM_PROVEEDOR,
													  WEB.viewmigrarcompras.NOM_MONEDA,
													  sum(WEB.viewmigrarcompras.CAN_SUB_TOTAL) as CAN_SUB_TOTAL,
													  sum(WEB.viewmigrarcompras.CAN_IMPUESTO_VTA) as CAN_IMPUESTO_VTA,
													  sum(WEB.viewmigrarcompras.CAN_TOTAL) as CAN_TOTAL,
													  WEB.viewmigrarcompras.NOM_ESTADO,
													  WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE'))
									->groupBy('WEB.viewmigrarcompras.NRO_SERIE')
									->groupBy('WEB.viewmigrarcompras.NRO_DOC')
									->groupBy('WEB.viewmigrarcompras.FEC_EMISION')
									->groupBy('WEB.viewmigrarcompras.NOM_TIPO_DOC')
									->groupBy('WEB.viewmigrarcompras.NOM_PROVEEDOR')
									->groupBy('WEB.viewmigrarcompras.NOM_MONEDA')
									->groupBy('WEB.viewmigrarcompras.NOM_ESTADO')
									->groupBy('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
									->get();

		return $data_compra;

	}


	private function co_documento_compra($documento_ctble_id)
	{
		$compra		=		WEBViewMigrarCompras::where('COD_DOCUMENTO_CTBLE','=',$documento_ctble_id)->first();
		return $compra;
	}

	private function co_detalle_compra($documento_ctble_id)
	{
		$listadetallecompra		=		WEBViewMigrarCompras::where('COD_DOCUMENTO_CTBLE','=',$documento_ctble_id)->get();
		return $listadetallecompra;
	}


	private function co_asiento_modelo($tipo_asiento_id,$empresa_id,$relacionado,$cod_moneda,$atributo)
	{	

		$icono 					=		'mdi-close-circle';

		//tipo de cliente
		if($atributo == 'tipo_cliente'){
			$check					=		WEBAsientoModelo::where('tipo_asiento_id','=',$tipo_asiento_id)
											->where('empresa_id','=',$empresa_id)
											->where('activo','=',1)
											->where($atributo,'=',$relacionado)
											->get();
		}

		if($atributo == 'moneda_id'){
			$check					=		WEBAsientoModelo::where('tipo_asiento_id','=',$tipo_asiento_id)
											->where('empresa_id','=',$empresa_id)
											->where('activo','=',1)
											->where('tipo_cliente','=',$relacionado)
											->where($atributo,'=',$relacionado)
											->get();
		}



		if(count($check)>0){
			$icono 					=		'mdi-check-circle';
		}


		return $icono;
	}

	


}