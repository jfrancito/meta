<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\CMPCategoria;

use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\STDEmpresa;



use ZipArchive;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait ArchivoTraits
{
	
	public function ar_identificador_fijo(){
        $identificador_fijo  		    = 		'LE';
        return $identificador_fijo;
    }

	public function ar_crear_nombre_venta_validar($anio,$mes,$i){

		$identificador 					=       $this->ar_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$i 								= 		$i+1;
        $nombre_archivo  		    	= 		$ruc.'-'.$anio.'-'.$mes.'-'.$i;
        return $nombre_archivo;
    }

	public function ar_crear_nombre_venta($anio,$mes){

		$identificador 					=       $this->ar_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'140100';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }

	public function ar_crear_nombre_compra($anio,$mes){

		$identificador 					=       $this->ar_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'080100';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }


	public function ar_suma_subtotal_7($asiento,$tipo_documento_06){

		$suma 							= 		0.00;

		$asiento_movimiento 			= 		WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)
												->where('COD_ESTADO','=',1)
												->where('IND_PRODUCTO', '=', 1)
												->select(DB::raw('sum(CAN_DEBE_MN + CAN_HABER_MN) as TOTAL'))
												->first();

		if(count($asiento_movimiento)>0){
			$suma = $asiento_movimiento->TOTAL;

			if($tipo_documento_06=='07'){
				$suma = $suma * -1;
			}	

		}		



        return $suma;
    }

	public function ar_suma_igv_40($asiento,$indicador_afecto,$tipo_documento_06){

		$suma 							= 		0.00;

		if($indicador_afecto==1){
			$asiento_movimiento 			= 		WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)
													->where('COD_ESTADO','=',1)
													->where('TXT_CUENTA_CONTABLE', 'like', '4%')
													->select(DB::raw('sum(CAN_DEBE_MN + CAN_HABER_MN) as TOTAL'))
													->first();

			if(count($asiento_movimiento)>0){
				$suma = $asiento_movimiento->TOTAL;

				if($tipo_documento_06=='07'){
					$suma = $suma * -1;
				}	

			}
		}

        return $suma;
    }
	

	public function ar_suma_igv_40_compras($asiento,$indicador_afecto,$tipo_documento_06){

		$suma 							= 		0.00;

		if($indicador_afecto==1){
			$asiento_movimiento 			= 		WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)
													->where('COD_ESTADO','=',1)
													->where('TXT_CUENTA_CONTABLE', 'like', '40%')
													->select(DB::raw('sum(CAN_DEBE_MN + CAN_HABER_MN) as TOTAL'))
													->first();

			if(count($asiento_movimiento)>0){
				$suma = $asiento_movimiento->TOTAL;

				if($tipo_documento_06=='07'){
					$suma = $suma * -1;
				}	

			}
		}

        return $suma;
    }


	public function ar_suma_total($asiento,$tipo_documento_06){

		$suma 							= 		0.00;

		$asiento_movimiento 			= 		WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)
												->where('COD_ESTADO','=',1)
												->select(DB::raw('sum(CAN_DEBE_MN) as TOTAL'))
												->first();

		if(count($asiento_movimiento)>0){
			$suma = $asiento_movimiento->TOTAL;

			if($tipo_documento_06=='07'){
				$suma = $suma * -1;
			}	
		}


        return $suma;
    }



	public function ar_tabla_asociada($asiento,$atributo){

		$cadena 						= 		"-";
		$array 							= 		CMPReferecenciaAsoc::where('COD_ESTADO','=',1)
        										->where('COD_TABLA','=',$asiento->TXT_REFERENCIA)
		        								->pluck('COD_TABLA_ASOC')
												->toArray();


		$documento 						= 		CMPDocumentoCtble::whereIn('COD_DOCUMENTO_CTBLE',$array)
												->whereIn('COD_CATEGORIA_TIPO_DOC',['TDO0000000000001','TDO0000000000003'])
												->where('COD_ESTADO','=',1)
												->select(DB::raw('('.$atributo.') as cadena'))
												->first();

		if(count($documento)>0){
			$cadena = $documento->cadena;
		}

        return $cadena;
    }



	public function ar_ind_afecta_infecta($asiento){

		$afecto 						= 		0;

		$asiento_modelo 				= 		WEBAsientoModelo::where('id','=',$asiento->COD_ASIENTO_MODELO)
												->first();

		if($asiento_modelo->tipo_igv_id == 'CTI0000000000001'){
			$afecto 					= 		1;
		}

        return $afecto;
    }


	public function archivo_ple_compras($anio,$mes,$listaasiento,$nombre,$path){

	    if (file_exists($path)) {
	        unlink("storage/compras/ple/".$nombre);
	    } 
		$datos = fopen("storage/compras/ple/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	//$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();

	   		
	    	//1->afecto ; 0->no afecto
	    	//$indicador_afecto 		=   $this->ar_ind_afecta_infecta($item);
	   		$indicador_afecto 		=   $item->IND_AFECTO;
	    	$nro_asiento 			=   $item->NRO_ASIENTO;
	    	$mes_01 				= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$periodo->COD_ANIO.$mes_01."00";
	    	$correlativo_02  		= 	$nro_asiento;
	    	$codigo_03  			= 	'M'.$correlativo_02;
	    	$fecha_emision_04  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_05  	= 	'01/01/0001';
			$tipo_documento_06  	= 	$categoria->CODIGO_SUNAT;
			$nro_serie_07  			= 	$item->NRO_SERIE;
			$anio_emision_dua_08  	= 	'0';
			$nro_correlativo_09  	= 	$item->NRO_DOC;
			$it_od_10  				= 	"";
			$identidad_cliente_11  	= 	intval($empresa->tipo_documento->CODIGO_SUNAT);
			$documento_cliente_12  	= 	$empresa->NRO_DOCUMENTO;
			$nombre_cliente_13  	= 	$empresa->NOM_EMPR;


			//suma de las 70
			$suma_70_14  			= 	'0.00';//falta
			if($indicador_afecto==1){

				$suma_70_14 		= 	$item->CAN_TOTAL_DEBE;
				//$suma_70_14 		= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
				$suma_70_14         = 	number_format($suma_70_14, 2, '.', '');

			}


			$suma_40_15 			= 	$this->ar_suma_igv_40_compras($item,$indicador_afecto,$tipo_documento_06);
			$suma_40_15             = 	number_format($suma_40_15, 2, '.', '');
			$codigo_16  			= 	'0.00';//falta
			$codigo_17  			= 	'0.00';//falta
			$codigo_18  			= 	'0.00';//falta
			$codigo_19  			= 	'0.00';//falta

			$codigo_20  			= 	'0.00';//falta
			if($indicador_afecto==0){
				$codigo_20 				= 	$item->CAN_TOTAL_DEBE;
				//$codigo_20 				= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
				$codigo_20              = 	number_format($codigo_20, 2, '.', '');
			}

			$isc_21  				= 	'0.00';//falta
			$icbp_22  				= 	'0.00';//falta
			$codigo_23  			= 	'0.00';//falta

			$importe_total_24 		= 	$this->ar_suma_total($item,$tipo_documento_06);
			$importe_total_24       = 	number_format($importe_total_24, 2, '.', '');
			$moneda_25 				= 	$item->moneda->CODIGO_SUNAT;

			$tipo_cambio_26 		= 	$item->CAN_TIPO_CAMBIO;
			$tipo_cambio_26       	= 	number_format($tipo_cambio_26, 3, '.', '');

			$fecha_asociada_27 		=   '01/01/0001';
			$tipo_asociado_28 		=   '00';
			$serie_asociada_29 		=   '-';
			$codigo_30 				=   '';
			$nro_asociada_31 		=   '-';
			$coddocumento_asociado  =	'';

			//NC Y ND
			if($tipo_documento_06 == '07' or $tipo_documento_06 == '08'){

				$fecha_asociada_28 		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
				$documento_asociado 	= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO_REF)->first();
				if(count($documento_asociado)>0){
					$tipo_asociado_29  	= 	$documento_asociado->CODIGO_SUNAT;
				}

				$serie_asociada_30 		= 	$item->NRO_SERIE_REF;
				$nro_asociada_31 		= 	$item->NRO_DOC_REF;

			}

			$codigo_32  			= 	'';//detraccion
			$codigo_33  			= 	'';//detraccion
			$codigo_34  			= 	'';//detraccion
			$codigo_35  			= 	'';//detraccion
			$codigo_36  			= 	'';
			$codigo_37  			= 	'';
			$codigo_38  			= 	'';
			$codigo_39  			= 	'';
			$codigo_40  			= 	'';
			$codigo_41  			= 	'';
			$codigo_42  			= 	'';//detraccion
			$codigo_43  			= 	$nro_serie_07.'-'.$nro_correlativo_09;


	      	fwrite($datos, $periodo_01."|");
	      	fwrite($datos, $correlativo_02."|");
	      	fwrite($datos, $codigo_03."|");
	      	fwrite($datos, $fecha_emision_04."|");
	      	fwrite($datos, $fecha_vencimiento_05."|");
	      	fwrite($datos, $tipo_documento_06."|");
	      	fwrite($datos, $nro_serie_07."|");
	      	fwrite($datos, $anio_emision_dua_08."|");
	      	fwrite($datos, $nro_correlativo_09."|");
	      	fwrite($datos, $it_od_10."|");

	      	fwrite($datos, $identidad_cliente_11."|");
	      	fwrite($datos, $documento_cliente_12."|");
	      	fwrite($datos, $nombre_cliente_13."|");
	      	fwrite($datos, $suma_70_14."|");
	      	fwrite($datos, $suma_40_15."|");
	      	fwrite($datos, $codigo_16."|");
	      	fwrite($datos, $codigo_17."|");
	      	fwrite($datos, $codigo_18."|");
	      	fwrite($datos, $codigo_19."|");
	      	fwrite($datos, $codigo_20."|");

	      	fwrite($datos, $isc_21."|");
	      	fwrite($datos, $icbp_22."|");
	      	fwrite($datos, $codigo_23."|");
	      	fwrite($datos, $importe_total_24."|");
	      	fwrite($datos, $moneda_25."|");
	      	fwrite($datos, $tipo_cambio_26."|");
	      	fwrite($datos, $fecha_asociada_27."|");
	      	fwrite($datos, $tipo_asociado_28."|");
	      	fwrite($datos, $serie_asociada_29."|");
	      	fwrite($datos, $codigo_30."|");

	      	fwrite($datos, $nro_asociada_31."|");
	      	fwrite($datos, $codigo_32."|");
	      	fwrite($datos, $codigo_33."|");
	      	fwrite($datos, $codigo_34."|");
	      	fwrite($datos, $codigo_35."|");
	      	fwrite($datos, $codigo_36."|");
	      	fwrite($datos, $codigo_37."|");
	      	fwrite($datos, $codigo_38."|");
	      	fwrite($datos, $codigo_39."|");
			fwrite($datos, $codigo_40."|");

	      	fwrite($datos, $codigo_41."|");
			fwrite($datos, $codigo_42."|");
	      	fwrite($datos, $codigo_43.PHP_EOL);

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"periodo" 					=> $periodo_01,
				"codigo_unico_operacion" 	=> $correlativo_02,
				"correlativo_asiento" 		=> $codigo_03,
				"fecha_emision" 			=> $fecha_emision_04,
				"fecha_vencimiento" 		=> $fecha_vencimiento_05,
				"tipo_comprobante" 			=> $tipo_documento_06,
	            "nro_serie" 				=> $nro_serie_07,
	            "anio_emision_dua" 			=> $anio_emision_dua_08,
	            "nro_documento" 			=> $nro_correlativo_09,
	            "campo_10" 					=> $it_od_10,

	            "tipo_documento_identidad" 	=> $identidad_cliente_11,
	            "documento_identidad" 		=> $documento_cliente_12,
	            "nombre_cliente" 			=> $nombre_cliente_13,
	            "valor_14" 					=> $suma_70_14,
	            "valor_15" 					=> $suma_40_15,
	            "valor_16" 					=> $codigo_16,
	            "valor_17" 					=> $codigo_17,
	            "valor_18" 					=> $codigo_18,
	            "valor_19" 					=> $codigo_19,
	            "valor_20" 					=> $codigo_20,

	            "valor_21" 					=> $isc_21,
	            "valor_22" 					=> $icbp_22,
	            "valor_23" 					=> $codigo_23,
	            "valor_24" 					=> $importe_total_24,
	            "valor_25" 					=> $moneda_25,
	            "valor_26" 					=> $tipo_cambio_26,
	            "valor_27" 					=> $fecha_asociada_27,
	            "valor_28" 					=> $tipo_asociado_28,
	            "valor_29" 					=> $serie_asociada_29,
	            "valor_30" 					=> $codigo_30,

	            "valor_31" 					=> $nro_asociada_31,
	            "valor_32" 					=> $codigo_32,
	            "valor_33" 					=> $codigo_33,
	            "valor_34" 					=> $codigo_34,
	            "valor_35" 					=> $codigo_35,
	            "valor_36" 					=> $codigo_36,
	            "valor_37" 					=> $codigo_37,
	            "valor_38" 					=> $codigo_38,
	            "valor_39" 					=> $codigo_39,
	            "valor_40" 					=> $codigo_40,

	            "valor_41" 					=> $codigo_41,
	            "valor_42" 					=> $codigo_42,
	            "valor_43" 					=> $codigo_43

			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);
			

	    }

	    fclose($datos);


	    return $array_detalle_asiento;

    }
	public function archivo_ple_ventas($anio,$mes,$listaasiento,$nombre,$path){


	    if (file_exists($path)) {
	        unlink("storage/ventas/ple/".$nombre);
	    } 

		$datos = fopen("storage/ventas/ple/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();

	    foreach($listaasiento as $index => $item){

	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();

	    	//1->afecto ; 0->no afecto
	    	$indicador_afecto 		=   $this->ar_ind_afecta_infecta($item);

	    	$mes_01 				= 	str_pad($item->periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$item->periodo->COD_ANIO.$mes_01."00";
	    	$correlativo_02  		= 	str_pad($index+1, 9, "0", STR_PAD_LEFT);
	    	$codigo_03  			= 	'M'.$correlativo_02;
	    	$fecha_emision_04  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_05  	= 	'01/01/0001';
			$tipo_documento_06  	= 	$categoria->CODIGO_SUNAT;
			$nro_serie_07  			= 	$item->NRO_SERIE;
			$nro_correlativo_08  	= 	$item->NRO_DOC;
			$codigo_09  			= 	'';
			$identidad_cliente_10  	= 	intval($empresa->tipo_documento->CODIGO_SUNAT);
			$documento_cliente_11  	= 	$empresa->NOM_EMPR;

			$txt_anulado 			= 	'';

			if($item->IND_EXTORNO==1){
				$txt_anulado 			= 	'*** ANULADO *** ';
			}


			$nombre_cliente_12  	= 	$txt_anulado.$empresa->NOM_EMPR;
			$v_f_e_13  				= 	'0.00';//falta

			//suma de las 70
			$suma_70_14  			= 	'0.00';//falta
			if($indicador_afecto==1){
				$suma_70_14 		= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
				//$suma_70_14 		= 	$item->CAN_TOTAL_DEBE;
				$suma_70_14         = 	number_format($suma_70_14, 2, '.', '');
			}

			$codigo_15  			= 	'0.00';//falta
			$suma_40_16 			= 	$this->ar_suma_igv_40($item,$indicador_afecto,$tipo_documento_06);
			$suma_40_16             = 	number_format($suma_40_16, 2, '.', '');
			$codigo_17  			= 	'0.00';//falta
			$codigo_18  			= 	'0.00';//falta
			$codigo_19  			= 	'0.00';//falta
			if($indicador_afecto==0){
				$codigo_19 				= 	$item->CAN_TOTAL_DEBE;
				//$codigo_19 				= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
				$codigo_19              = 	number_format($codigo_19, 2, '.', '');
			}
			$codigo_20  			= 	'0.00';//falta
			$codigo_21  			= 	'0.00';//falta
			$codigo_22  			= 	'0.00';//falta
			$codigo_23  			= 	'0.00';//falta
			$codigo_24  			= 	'0.00';//falta

			$importe_total_25 		= 	$this->ar_suma_total($item,$tipo_documento_06);
			$importe_total_25       = 	number_format($importe_total_25, 2, '.', '');

			$moneda_26 				= 	$item->moneda->CODIGO_SUNAT;

			$tipo_cambio_27 		= 	$item->CAN_TIPO_CAMBIO;
			$tipo_cambio_27       	= 	number_format($tipo_cambio_27, 3, '.', '');


			$fecha_asociada_28 		=   '01/01/0001';
			$tipo_asociado_29 		=   '00';
			$serie_asociada_30 		=   '-';
			$nro_asociada_31 		=   '-';
			$coddocumento_asociado  =	'';

			//NC Y ND
			if($tipo_documento_06 == '07' or $tipo_documento_06 == '08'){


				$fecha_asociada_28 		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
				$documento_asociado 	= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO_REF)->first();
				if(count($documento_asociado)>0){
					$tipo_asociado_29  	= 	$documento_asociado->CODIGO_SUNAT;
				}

				$serie_asociada_30 		= 	$item->NRO_SERIE_REF;
				$nro_asociada_31 		= 	$item->NRO_DOC_REF;


			}

			$codigo_32  			= 	'';//falta
			$codigo_33  			= 	'';//falta
			$codigo_34  			= 	'';//falta

			$codigo_35  			= 	'1';

			if($item->IND_EXTORNO==1){
				$codigo_35 			= 	'2';
			}

			$codigo_36  			= 	'';

	      	fwrite($datos, $periodo_01."|");
	      	fwrite($datos, $correlativo_02."|");
	      	fwrite($datos, $codigo_03."|");
	      	fwrite($datos, $fecha_emision_04."|");
	      	fwrite($datos, $fecha_vencimiento_05."|");
	      	fwrite($datos, $tipo_documento_06."|");
	      	fwrite($datos, $nro_serie_07."|");
	      	fwrite($datos, $nro_correlativo_08."|");
	      	fwrite($datos, $codigo_09."|");
	      	fwrite($datos, $identidad_cliente_10."|");
	      	fwrite($datos, $documento_cliente_11."|");
	      	fwrite($datos, $nombre_cliente_12."|");
	      	fwrite($datos, $v_f_e_13."|");
	      	fwrite($datos, $suma_70_14."|");
	      	fwrite($datos, $codigo_15."|");
	      	fwrite($datos, $suma_40_16."|");

	      	fwrite($datos, $codigo_17."|");
	      	fwrite($datos, $codigo_18."|");
	      	fwrite($datos, $codigo_19."|");
	      	fwrite($datos, $codigo_20."|");
	      	fwrite($datos, $codigo_21."|");
	      	fwrite($datos, $codigo_22."|");
	      	fwrite($datos, $codigo_23."|");
	      	fwrite($datos, $codigo_24."|");

	      	fwrite($datos, $importe_total_25."|");
	      	fwrite($datos, $moneda_26."|");
	      	fwrite($datos, $tipo_cambio_27."|");
	      	fwrite($datos, $fecha_asociada_28."|");
	      	fwrite($datos, $tipo_asociado_29."|");
	      	fwrite($datos, $serie_asociada_30."|");
	      	fwrite($datos, $nro_asociada_31."|");

	      	fwrite($datos, $codigo_32."|");
	      	fwrite($datos, $codigo_33."|");
	      	fwrite($datos, $codigo_34."|");
	      	fwrite($datos, $codigo_35."|");
	      	fwrite($datos, $codigo_36.PHP_EOL);

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"periodo" 					=> $periodo_01,
				"codigo_unico_operacion" 	=> $correlativo_02,
				"correlativo_asiento" 		=> $codigo_03,
				"fecha_emision" 			=> $fecha_emision_04,
				"fecha_vencimiento" 		=> $fecha_vencimiento_05,
				"tipo_comprobante" 			=> $tipo_documento_06,
	            "nro_serie" 				=> $nro_serie_07,
	            "nro_documento" 			=> $nro_correlativo_08,
	            "registro_tickets" 			=> $codigo_09,
	            "tipo_documento_identidad" 	=> $identidad_cliente_10,
	            "documento_identidad" 		=> $documento_cliente_11,
	            "nombre_cliente" 			=> $nombre_cliente_12,
	            "valor_facturado_exportacion"=> $v_f_e_13,
	            "base_imponible_gravada" 	=> $suma_70_14,
	            "descuento_base_imponible" 	=> $codigo_15,
	            "impuesto_generl_ventas" 	=> $suma_40_16,
	            "descuento_impuesto_generl_ventas" => $codigo_17,
	            "importe_total_operacion_exonerada" => $codigo_18,
	            "importe_total_operacion_inafecta" => $codigo_19,
	            "impuesto_selectivo_consumo" => $codigo_20,
	            "base_imponible_operacion_gravada_impuesto_operacion_gravada" => $codigo_21,
	            "impuesto_venta_arroz_pilado" => $codigo_22,
	            "impuesto_consumo_bolsa_plastico" => $codigo_23,
	            "otros_conceptos" 			=> $codigo_24,
	            "importe_total_comprobnte_pago" => $importe_total_25,
	            "codigo_moneda"     		=> $moneda_26,
	            "tipo_cambio"     			=> $tipo_cambio_27,
	            "fecha_emision_comprobante_pago_modifica" => $fecha_asociada_28,
	            "tipo_comprobante_pago_modifica" => $tipo_asociado_29,
	            "nro_serie_modifica" => $serie_asociada_30,
	            "nro_documento_modifica" => $nro_asociada_31,
	            "identificacion_contrato" => $codigo_32,
	            "inconsistencia_tipo_cambio" => $codigo_33,
	            "indicador_comprobante_pago" => $codigo_34,
	            "estado_indica_oportunidad_anotacion" => $codigo_35,
	            "campo_libre_utilizacion" => $codigo_36,
			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);

	    }
	    fclose($datos);


	    return $array_detalle_asiento;

    }

	public function archivo_ple_ventas_validar($anio,$mes,$listaasiento,$count,$asiento_nombre){


		$inicio = 0;
		$fin = 0;

    	for ($i = 0; $i < $count; $i++) {
    		$nombre = $this->ar_crear_nombre_venta_validar($anio,$mes,$i).'.txt';
    		$path = storage_path($asiento_nombre.'/validar/'.$nombre);

		    if (file_exists($path)) {
		        unlink('storage/'.$asiento_nombre.'/validar/'.$nombre);
		    }

			$datos = fopen('storage/'.$asiento_nombre.'/validar/'.$nombre, "a");

			$inicio = $fin + 1;
			$fin    = 100 + $fin;

			//llenado de datalle
		    foreach($listaasiento as $index => $item){

		    	if( ($index + 1) >= $inicio and ($index+1) <= $fin){

			    	//$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
			    	$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
			    	$ruc_01 				= 	Session::get('empresas_meta')->NRO_DOCUMENTO;
					$tipo_documento_02  	= 	$categoria->CODIGO_SUNAT;
					$nro_serie_03  			= 	$item->NRO_SERIE;
					$nro_correlativo_04  	= 	$item->NRO_DOC;
					$fecha_emision_05  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
					$importe_total_06 		= 	$this->ar_suma_total($item,'');
					$importe_total_06       = 	number_format($importe_total_06, 2, '.', '');

			      	fwrite($datos, $ruc_01."|");
			      	fwrite($datos, $tipo_documento_02."|");
			      	fwrite($datos, $nro_serie_03."|");
			      	fwrite($datos, $nro_correlativo_04."|");
			      	fwrite($datos, $fecha_emision_05."|");
			      	fwrite($datos, $importe_total_06.PHP_EOL);



		    	}
		    }
		    fclose($datos);
		}

		$nombre_zip = $this->ar_crear_nombre_venta_validar($anio,$mes,0).'-'.$count.'.zip';
        $zip = new ZipArchive;
        $zip->open('storage/'.$asiento_nombre.'/validar/'.$nombre_zip, ZipArchive::CREATE);

    	for ($i = 0; $i < $count; $i++) {
    		$nombre = $this->ar_crear_nombre_venta_validar($anio,$mes,$i).'.txt';
			$fp = fopen('storage/'.$asiento_nombre.'/validar/'.$nombre, "r");
			$contenido = fread($fp, filesize('storage/'.$asiento_nombre.'/validar/'.$nombre));
			$zip->addFromString($nombre, $contenido);
			fclose($fp);
    	}
        $zip->close();

		return $nombre_zip;



    }

}