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


	public function archivo_ple_ventas($anio,$mes,$listaasiento,$nombre,$path){


	    if (file_exists($path)) {
	        unlink("storage/ventas/".$nombre);
	    } 

		$datos = fopen("storage/ventas/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();

	    foreach($listaasiento as $index => $item){



	    	$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
	    	//1->afecto ; 0->no afecto
	    	$indicador_afecto 		=   $this->ar_ind_afecta_infecta($item);

	    	$mes_01 				= 	str_pad($item->periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$item->periodo->COD_ANIO.$mes_01."00";
	    	$correlativo_02  		= 	str_pad($index+1, 9, "0", STR_PAD_LEFT);
	    	$codigo_03  			= 	'M'.$correlativo_02;
	    	$fecha_emision_04  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_05  	= 	'01/01/0001';
			$tipo_documento_06  	= 	$documento->tipo_documento->CODIGO_SUNAT;
			$nro_serie_07  			= 	$documento->NRO_SERIE;
			$nro_correlativo_08  	= 	$documento->NRO_DOC;
			$codigo_09  			= 	'';
			$identidad_cliente_10  	= 	intval($documento->empresa->tipo_documento->CODIGO_SUNAT);
			$documento_cliente_11  	= 	$documento->empresa->NRO_DOCUMENTO;

			$txt_anulado 			= 	'';

			if($item->IND_EXTORNO==1){
				$txt_anulado 			= 	'*** ANULADO *** ';
			}


			$nombre_cliente_12  	= 	$txt_anulado.$documento->empresa->NOM_EMPR;
			$v_f_e_13  				= 	'0.00';//falta

			//suma de las 70
			$suma_70_14  			= 	'0.00';//falta
			if($indicador_afecto==1){
				$suma_70_14 		= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
				$suma_70_14         = 	number_format($suma_70_14, 2, '.', '');
			}

			$codigo_15  			= 	'0.00';//falta
			$suma_40_16 			= 	$this->ar_suma_igv_40($item,$indicador_afecto,$tipo_documento_06);
			$suma_40_16             = 	number_format($suma_40_16, 2, '.', '');
			$codigo_17  			= 	'0.00';//falta
			$codigo_18  			= 	'0.00';//falta
			$codigo_19  			= 	'0.00';//falta
			if($indicador_afecto==0){
				$codigo_19 				= 	$this->ar_suma_subtotal_7($item,$tipo_documento_06);
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

				$fecha_asociada_28 		= 	date_format(date_create($this->ar_tabla_asociada($item,'FEC_EMISION')), 'd/m/Y');
				$coddocumento_asociado 	= 	$this->ar_tabla_asociada($item,'COD_DOCUMENTO_CTBLE');
				$documento_asociado 	= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$coddocumento_asociado)->first();
				if(count($documento_asociado)>0){
					$tipo_asociado_29  	= 	$documento_asociado->tipo_documento->CODIGO_SUNAT;
				}
				$serie_asociada_30 		= 	$this->ar_tabla_asociada($item,'NRO_SERIE');
				$nro_asociada_31 		= 	$this->ar_tabla_asociada($item,'NRO_DOC');
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



}