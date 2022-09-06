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
use App\Modelos\STDEmpresa;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CMPCategoria;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait ComprasTraits
{


	private function co_reversion_compra($asiento_id)
	{

        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_REVERSION_ASIENTO_MODELO 
							@cod_asiento = ?');

        $stmt->bindParam(1, $asiento_id ,PDO::PARAM_STR);             
        $stmt->execute();


        return $stmt;

	}

	public function co_crear_nombre_compra_detraccion($anio,$mes){
		$identificador 					=       'D';
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$anio 							= 		substr($anio, -2);
		$dd 							= 		'00';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$dd.$mes;
        return $nombre_archivo;
    }


	public function co_archivo_ple_compras($anio,$mes,$listadetracciones,$nombre,$path,$periodo_id,$empresa_id){

	    if (file_exists($path)) {
	        unlink("storage/compras/detraccion/".$nombre);
	    } 
		$datos = fopen("storage/compras/detraccion/".$nombre, "a");
		//llenado de datalle

	    $sum_total_detraccion 			= 		WEBAsiento::where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    										->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    										->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    										->where('WEB.asientos.COD_ESTADO','=','1')
	    										->where('WEB.asientos.CAN_TOTAL_DETRACCION','>',0)
	    										->sum('CAN_TOTAL_DETRACCION');




		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$nombre_empresa					= 		Session::get('empresas_meta')->NOM_EMPR;
		$nombre_empresa 				=   	str_pad($nombre_empresa, 35, " ", STR_PAD_RIGHT);
		$anio 							= 		substr($anio, -2);
		$dd 							= 		'00';
		$mes 							= 		str_pad($mes, 2, "0", STR_PAD_LEFT); 
		$total 							= 		intval($sum_total_detraccion);
		$total 							= 		str_pad($total, 15, "0", STR_PAD_LEFT);

		fwrite($datos, '*');
      	fwrite($datos, $ruc);
      	fwrite($datos, $nombre_empresa);
      	fwrite($datos, $anio.$dd.$mes);
      	fwrite($datos, $total.PHP_EOL);


	    foreach($listadetracciones as $index => $item){

			$tipo_documento_pro = 	$item['tipo_documento_pro'];
			$nro_documento  	= 	$item['nro_documento'];
			$nombre_proveedor  	= 	$item['nombre_proveedor'];
			$plataforma  		= 	$item['plataforma'];
			$codigobienservicio = 	$item['codigobienservicio'];
			$nro_cuenta 		= 	$item['nro_cuenta'];
			$importe 			=   str_pad($item['importe'], 13, "0", STR_PAD_LEFT);
			if($importe==''){
	   			$importe 	    =   '0000000000000';
	   		}
	   		$tipo_operacion 	= 	$item['tipo_operacion'];

	   		$periodo 			=   $item['periodo'];;
	   		$nro_serie 			= 	$item['nro_serie'];
	   		$nro_correlativo 	= 	$item['nro_correlativo'];

			fwrite($datos, $tipo_documento_pro);
	      	fwrite($datos, $nro_documento);
	      	fwrite($datos, $nombre_proveedor);
	      	fwrite($datos, $plataforma);
	      	fwrite($datos, $codigobienservicio);
	      	fwrite($datos, $nro_cuenta);
	      	fwrite($datos, $importe);
	      	fwrite($datos, $tipo_operacion);
	      	fwrite($datos, $periodo);
	      	fwrite($datos, $nro_serie);
	      	fwrite($datos, $nro_correlativo.PHP_EOL);

	    }
	    fclose($datos);


	    return true;

    }

	private function co_lista_compras_detracciones($anio,$periodo_id,$empresa_id)
	{

	    $lista_asiento 			= 	WEBAsiento::where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    							->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientos.CAN_TOTAL_DETRACCION','>',0)
									->orderby('WEB.asientos.FEC_ASIENTO','asc')
	    							->get();

		$array_detracciones 	=	array();




	    foreach($lista_asiento as $index => $item){

	   		$empresa 			= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$cuentadetraccion   =   WEBCuentaDetraccion::where('DOCUMENTO','=',$empresa->NRO_DOCUMENTO)->first();
	   		$categoria 			= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();


	   		$nro_documento  	= 	$empresa->NRO_DOCUMENTO;
	   		$plataforma 		= 	'';
	   		$codigobienservicio =   '';
	   		$nombre_proveedor 	= 	'';
			$tipo_documento_pro = 	intval($empresa->tipo_documento->CODIGO_SUNAT);
			$nro_cuenta 		= 	'';
			$tipo_operacion 	= 	'';
			$mes 				= 	substr($item->FEC_ASIENTO, 5, 2);
	    	$mes_01 			= 	str_pad($mes, 2, "0", STR_PAD_LEFT); 
			$periodo 			= 	$item->periodo->COD_ANIO.$mes_01;
			$tipo_documento  	= 	intval($categoria->CODIGO_SUNAT);
			$nro_serie  		= 	$item->NRO_SERIE;
			$nro_correlativo 	= 	$item->NRO_DOC;


	   		if(count($cuentadetraccion)>0){

	   			$codigobienservicio =   $cuentadetraccion->TIPO_BIEN_SERVICIO;
	   			$nro_cuenta 		=   $cuentadetraccion->NRO_CUENTA;
	  			$nro_cuenta 		=   str_pad($nro_cuenta, 11, "0", STR_PAD_LEFT);
	  			$tipo_operacion 	=   $cuentadetraccion->TIPO_OPERACION;

	   			if($codigobienservicio == '40' and $tipo_documento_pro <> '06'){
	   				$nombre_proveedor 	= 	$empresa->NOM_EMPR;
	   			}

	   		}

	   		$importe 			= 	intval($item->CAN_TOTAL_DETRACCION);
	   		if($plataforma==''){
	   			$plataforma 	=   '0000000000';
	   		}
	   		if($nombre_proveedor==''){
	   			$nombre_proveedor 	=   str_pad($nombre_proveedor, 35, " ", STR_PAD_LEFT);
	   		}
	   		if($nro_cuenta==''){
	   			$nro_cuenta 		=   str_pad($nro_cuenta, 11, "0", STR_PAD_LEFT);
	   		}
	   		$codigobienservicio =   str_pad($codigobienservicio, 2, "0", STR_PAD_LEFT);
	   		$tipo_operacion 	=   str_pad($codigobienservicio, 4, "0", STR_PAD_LEFT);
	   		$tipo_documento 	=   str_pad($tipo_documento, 2, "0", STR_PAD_LEFT);
	   		$nro_correlativo 	=   str_pad($nro_correlativo, 8, "0", STR_PAD_LEFT);

	    	$array_nuevo 		=	array();
			$array_nuevo    	=	array(
				"tipo_documento_pro" 				=> $tipo_documento_pro,
				"nro_documento" 					=> $nro_documento,
				"nombre_proveedor" 					=> $nombre_proveedor,
				"plataforma" 						=> $plataforma,
				"codigobienservicio" 				=> $codigobienservicio,
				"nro_cuenta" 						=> $nro_cuenta,
				"importe" 							=> $importe,
				"tipo_operacion" 					=> $tipo_operacion,
				"periodo" 							=> $periodo,
				"tipo_documento" 					=> $tipo_documento,
				"nro_serie" 						=> $nro_serie,
				"nro_correlativo" 					=> $nro_correlativo,
			);

			array_push($array_detracciones,$array_nuevo);
	    }	


		return $array_detracciones;

	}



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

		//dd(count(trim($orden)));	
		if(isset($orden->CAN_DETRACCION)){
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

	private function co_lista_compras_terminado_asiento($anio,$periodo_id,$empresa_id,$serie,$documento)
	{

	    $lista_compras 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', function ($join) use ($periodo_id,$empresa_id){
							            $join->on('WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
							        })
	    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    							->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    							->NroSerie($serie)
	    							->NroDocumento($documento)
	    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
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