<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\CONPeriodo;

use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ArchivoTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



class ArchivoController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ArchivoTraits;


	public function actionGestionLibrosElectronicoPle($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Libros Electronicos - PLE');
	    $sel_tipo_asiento 		=	'';
	    $sel_periodo 			=	'';

	    $array_id_tipo_asiento  =    ['TAS0000000000003'];

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria_xarrayid('TIPO_ASIENTO','Seleccione tipo asiento','',$array_id_tipo_asiento);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		
		return View::make('archivople/descargararchivople',
						 [
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionDescargarArchivoPle(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->orderby('FEC_ASIENTO','asc')
	    							->get();

	    //ventas 
	    if($tipo_asiento_id == 'TAS0000000000003'){

			$nombre = $this->ar_crear_nombre_venta($anio,$mes).'.txt';
			$path = storage_path('ventas/'.$nombre);
	    	$this->archivo_ple_ventas($anio,$mes,$listaasiento,$nombre,$path);
		    if (file_exists($path)) {

		        return Response::download($path);


		    }

	    }



	}





	public function actionRegistroVentasTxt()
	{
		$anio 					= 	'2022';
		$mes 					= 	'01';
		$periodo_id 			= 	'ICCHPE0000000066';
		$tipo_asiento_id 		= 	'TAS0000000000003';

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->orderby('FEC_ASIENTO','asc')
	    							->get();


		$nombre = $this->ar_crear_nombre_venta($anio,$mes).'.txt';


		$path = storage_path('ventas/'.$nombre);

	    if (file_exists($path)) {
	        unlink("storage/ventas/".$nombre);
	    } 

		$datos = fopen("storage/ventas/".$nombre, "a");
		//llenado de datalle
	    foreach($listaasiento as $index => $item){

	    	$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
	    	//1->afecto ; 0->no afecto
	    	$indicador_afecto 		=   $this->ar_ind_afecta_infecta($item);



	    	$mes_01 				= 	str_pad($item->periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$item->periodo->COD_ANIO.$mes_01."00";
	    	$correlativo_02  		= 	str_pad($index+1, 10, "0", STR_PAD_LEFT);
	    	$codigo_03  			= 	'M'.$correlativo_02;
	    	$fecha_emision_04  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_05  	= 	'01/01/0001';
			$tipo_documento_06  	= 	$documento->tipo_documento->CODIGO_SUNAT;
			$nro_serie_07  			= 	$documento->NRO_SERIE;
			$nro_correlativo_08  	= 	$documento->NRO_DOC;
			$codigo_09  			= 	'';
			$identidad_cliente_10  	= 	intval($documento->empresa->tipo_documento->CODIGO_SUNAT);
			$documento_cliente_11  	= 	$documento->empresa->NRO_DOCUMENTO;
			$nombre_cliente_12  	= 	$documento->empresa->NOM_EMPR;
			$v_f_e_13  				= 	'0.00';//falta

			//suma de las 70
			$suma_70_14  			= 	'0.00';//falta
			if($indicador_afecto==1){
				$suma_70_14 		= 	$this->ar_suma_subtotal_7($item);
				$suma_70_14         = 	number_format($suma_70_14, 2, '.', '');
			}

			$codigo_15  			= 	'0.00';//falta

			$suma_40_16 			= 	$this->ar_suma_igv_40($item,$indicador_afecto);
			$suma_40_16             = 	number_format($suma_40_16, 2, '.', '');

			$codigo_17  			= 	'0.00';//falta
			$codigo_18  			= 	'0.00';//falta

			$codigo_19  			= 	'0.00';//falta
			if($indicador_afecto==0){
				$codigo_19 				= 	$this->ar_suma_subtotal_7($item);
				$codigo_19              = 	number_format($codigo_19, 2, '.', '');
			}


			$codigo_20  			= 	'0.00';//falta
			$codigo_21  			= 	'0.00';//falta
			$codigo_22  			= 	'0.00';//falta
			$codigo_23  			= 	'0.00';//falta
			$codigo_24  			= 	'0.00';//falta

			$importe_total_25 		= 	$this->ar_suma_total($item);
			$importe_total_25       = 	number_format($importe_total_25, 2, '.', '');

			$moneda_26 				= 	$item->moneda->CODIGO_SUNAT;

			$tipo_cambio_27 		= 	$item->CAN_TIPO_CAMBIO;
			$tipo_cambio_27       	= 	number_format($tipo_cambio_27, 2, '.', '');


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
			$codigo_35  			= 	'1';//falta

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
	      	fwrite($datos, $codigo_35.PHP_EOL);



	    }
	    fclose($datos);


	    if (file_exists($path)) {
	        return Response::download($path);
	    }


                
	}

}
